<?php

namespace App\Services;

use App\Enums\AssetCondition;
use App\Enums\LoanStatus;
use App\Models\Asset;
use App\Models\AssetActivity;
use App\Models\AssetLoan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class LoanService
{
    /**
     * Catat peminjaman: kunci baris aset, validasi stok, tambah borrowed_quantity,
     * sinkronkan status, dan tulis aktivitas dalam satu transaksi.
     */
    public function borrow(Asset $asset, User $borrower, array $data, ?User $handler = null): AssetLoan
    {
        return DB::transaction(function () use ($asset, $borrower, $data, $handler) {
            /** @var Asset $locked */
            $locked = Asset::query()->lockForUpdate()->findOrFail($asset->getKey());
            $quantity = max(1, (int) ($data['quantity'] ?? 1));

            if ($quantity > $locked->availableQuantity()) {
                throw new RuntimeException("Stok tersedia hanya {$locked->availableQuantity()} unit untuk {$locked->name}.");
            }

            $loan = $locked->loans()->create([
                'code' => $this->nextCode(),
                'borrower_id' => $borrower->getKey(),
                'handled_by' => $handler?->getKey(),
                'quantity' => $quantity,
                'purpose' => $data['purpose'] ?? null,
                'room' => $data['room'] ?? $locked->room,
                'borrowed_at' => $data['borrowed_at'] ?? now(),
                'due_at' => $data['due_at'] ?? now()->addDays(7)->toDateString(),
                'status' => LoanStatus::Dipinjam,
                'initial_photo_path' => $data['initial_photo_path'] ?? null,
            ]);

            $locked->borrowed_quantity += $quantity;
            $locked->syncStatus()->save();

            // Tandai unit yang dipinjam & perbarui foto kondisi jika ada
            $availableItems = $locked->items()->where('status', \App\Enums\AssetStatus::Tersedia->value)->limit($quantity)->get();
            foreach ($availableItems as $item) {
                $itemUpdate = ['status' => \App\Enums\AssetStatus::Dipinjam];
                if (! empty($data['initial_photo_path'])) {
                    $itemUpdate['photo_path'] = $data['initial_photo_path'];
                }
                $item->update($itemUpdate);
            }

            $this->log($locked, $handler ?? $borrower, 'loan', $loan,
                "{$borrower->name} meminjam {$quantity} unit {$locked->name} ({$loan->code}).");

            return $loan->load('asset', 'borrower');
        });
    }

    public function returnLoan(AssetLoan $loan, ?User $handler = null, ?string $condition = null, ?string $note = null, ?string $returnPhotoPath = null): AssetLoan
    {
        return DB::transaction(function () use ($loan, $handler, $condition, $note, $returnPhotoPath) {
            /** @var AssetLoan $locked */
            $locked = AssetLoan::query()->lockForUpdate()->findOrFail($loan->getKey());

            if ($locked->status === LoanStatus::Dikembalikan) {
                throw new RuntimeException("Peminjaman {$locked->code} sudah dikembalikan.");
            }

            $asset = Asset::query()->lockForUpdate()->findOrFail($locked->asset_id);
            $condition = $condition ?: AssetCondition::Baik->value;

            $locked->update([
                'status'              => LoanStatus::Dikembalikan,
                'returned_at'         => now(),
                'condition_on_return' => $condition,
                'note'                => $note ?? $locked->note,
            ]);

            $asset->borrowed_quantity = max(0, $asset->borrowed_quantity - $locked->quantity);
            $asset->condition = AssetCondition::from($condition);
            $asset->last_checked_at = now();
            $asset->syncStatus()->save();

            // Kembalikan status unit barang & perbarui kondisi + foto kondisi kembali
            $borrowedItems = $asset->items()->where('status', \App\Enums\AssetStatus::Dipinjam->value)->limit($locked->quantity)->get();
            foreach ($borrowedItems as $item) {
                $updateData = [
                    'status'    => \App\Enums\AssetStatus::Tersedia,
                    'condition' => $condition,
                ];
                if ($returnPhotoPath) {
                    $updateData['photo_path'] = $returnPhotoPath;
                }
                $item->update($updateData);
            }

            $this->log($asset, $handler, 'return', $locked,
                "{$locked->code} dikembalikan ({$locked->quantity} unit {$asset->name}). Kondisi: ".AssetCondition::from($condition)->label().'.');

            return $locked->fresh(['asset', 'borrower']);
        });
    }

    public function nextCode(): string
    {
        $prefix = 'PJM.'.now()->format('Y.m');
        $last = AssetLoan::query()->where('code', 'like', $prefix.'.%')->orderByDesc('id')->value('code');
        $sequence = $last ? ((int) Str::afterLast($last, '.')) + 1 : 1;

        return sprintf('%s.%04d', $prefix, $sequence);
    }

    public function log(Asset $asset, ?User $user, string $type, ?object $subject, string $description): void
    {
        AssetActivity::query()->create([
            'asset_id' => $asset->getKey(),
            'user_id' => $user?->getKey(),
            'type' => $type,
            'subject' => $subject && isset($subject->code) ? $subject->code : null,
            'description' => $description,
        ]);
    }

}