<?php

namespace Tests\Feature;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class LoanFlowTest extends TestCase
{
    use RefreshDatabase;

    private function asset(int $quantity = 5, int $borrowed = 0): Asset
    {
        $category = Category::query()->create(['code' => 'PL', 'name' => 'Peralatan Lapangan', 'color' => '#1E6B38']);
        $location = Location::query()->create(['name' => 'Gudang Utama', 'city' => 'Depok']);

        return Asset::query()->create([
            'code' => 'DEP.PL/2026/SGI.0001',
            'name' => 'Linggis',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'quantity' => $quantity,
            'minimum_stock' => 1,
            'borrowed_quantity' => $borrowed,
            'condition' => AssetCondition::Baik,
            'status' => $borrowed > 0 ? AssetStatus::Dipinjam : AssetStatus::Tersedia,
        ]);
    }

    private function staff(): User
    {
        return User::query()->create([
            'name' => 'Sri Wahyuni',
            'username' => 'sri.wahyuni',
            'email' => 'logistik@example.test',
            'password' => 'password',
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);
    }

    public function test_borrowing_reduces_available_stock_and_sets_status(): void
    {
        $asset = $this->asset(quantity: 5);
        $staff = $this->staff();

        $loan = app(LoanService::class)->borrow($asset, $staff, ['quantity' => 2, 'purpose' => 'Pemeriksaan'], $staff);

        $asset->refresh();

        $this->assertSame(2, $asset->borrowed_quantity);
        $this->assertSame(3, $asset->availableQuantity());
        $this->assertSame(AssetStatus::Dipinjam, $asset->status);
        $this->assertSame(LoanStatus::Dipinjam, $loan->status);
        $this->assertSame(2, $loan->quantity);
    }

    public function test_borrowing_more_than_available_is_rejected(): void
    {
        $asset = $this->asset(quantity: 2);
        $staff = $this->staff();

        $this->expectException(RuntimeException::class);

        app(LoanService::class)->borrow($asset, $staff, ['quantity' => 3], $staff);
    }

    public function test_fully_borrowed_asset_becomes_habis(): void
    {
        $asset = $this->asset(quantity: 2);
        $staff = $this->staff();

        app(LoanService::class)->borrow($asset, $staff, ['quantity' => 2], $staff);

        $this->assertSame(AssetStatus::Habis, $asset->refresh()->status);
    }

    public function test_returning_restores_stock_and_records_condition(): void
    {
        $asset = $this->asset(quantity: 4);
        $staff = $this->staff();
        $loan = app(LoanService::class)->borrow($asset, $staff, ['quantity' => 3], $staff);

        app(LoanService::class)->returnLoan($loan, $staff, AssetCondition::RusakRingan->value, 'Ada baret');

        $loan->refresh();
        $asset->refresh();

        $this->assertSame(LoanStatus::Dikembalikan, $loan->status);
        $this->assertNotNull($loan->returned_at);
        $this->assertSame(AssetCondition::RusakRingan, $loan->condition_on_return);
        $this->assertSame(0, $asset->borrowed_quantity);
        $this->assertSame(4, $asset->availableQuantity());
        $this->assertSame(AssetStatus::Tersedia, $asset->status);
        $this->assertSame(AssetCondition::RusakRingan, $asset->condition);
        $this->assertNotNull($asset->last_checked_at);
    }

    public function test_same_loan_cannot_be_returned_twice(): void
    {
        $asset = $this->asset(quantity: 3);
        $staff = $this->staff();
        $loan = app(LoanService::class)->borrow($asset, $staff, ['quantity' => 1], $staff);

        app(LoanService::class)->returnLoan($loan, $staff, AssetCondition::Baik->value);

        $this->expectException(RuntimeException::class);

        app(LoanService::class)->returnLoan($loan->refresh(), $staff, AssetCondition::Baik->value);
    }

    public function test_borrow_and_return_write_activity_log(): void
    {
        $asset = $this->asset(quantity: 2);
        $staff = $this->staff();

        $loan = app(LoanService::class)->borrow($asset, $staff, ['quantity' => 1], $staff);
        app(LoanService::class)->returnLoan($loan, $staff, AssetCondition::Baik->value);

        $types = $asset->activities()->pluck('type')->all();

        $this->assertContains('loan', $types);
        $this->assertContains('return', $types);
    }

    public function test_derived_status_never_reports_tersedia_when_stock_is_empty(): void
    {
        $asset = $this->asset(quantity: 0);

        $this->assertSame(AssetStatus::Habis, $asset->derivedStatus());
        $this->assertTrue($asset->isCritical());
    }

    public function test_staff_can_borrow_via_http_and_pegawai_cannot(): void
    {
        $asset = $this->asset(quantity: 3);
        $staff = $this->staff();
        $pegawai = User::query()->create([
            'name' => 'Bagus',
            'username' => 'bagus',
            'email' => 'pegawai@example.test',
            'password' => 'password',
            'role' => UserRole::Karyawan,
            'is_active' => true,
        ]);

        $this->actingAs($pegawai)
            ->post(route('loans.store'), [
                'asset_id' => $asset->id,
                'borrower_id' => $pegawai->id,
                'quantity' => 1,
                'purpose' => 'Coba pinjam',
            ])
            ->assertForbidden();

        $this->actingAs($staff)
            ->post(route('loans.store'), [
                'asset_id' => $asset->id,
                'borrower_id' => $pegawai->id,
                'quantity' => 1,
                'purpose' => 'Pemeriksaan lapangan',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('asset_loans', [
            'asset_id' => $asset->id,
            'borrower_id' => $pegawai->id,
            'status' => LoanStatus::Dipinjam->value,
        ]);
        $this->assertSame(1, $asset->refresh()->borrowed_quantity);
    }

    public function test_overdue_detection_uses_due_date(): void
    {
        $asset = $this->asset(quantity: 2);
        $staff = $this->staff();

        $loan = AssetLoan::query()->create([
            'code' => 'PJM.TEST.0001',
            'asset_id' => $asset->id,
            'borrower_id' => $staff->id,
            'quantity' => 1,
            'borrowed_at' => now()->subDays(10),
            'due_at' => now()->subDays(3)->toDateString(),
            'status' => LoanStatus::Dipinjam,
        ]);

        $this->assertTrue($loan->isOverdue());

        $loan->update(['due_at' => now()->addDays(3)->toDateString()]);

        $this->assertFalse($loan->refresh()->isOverdue());
    }

    public function test_borrowing_with_initial_photo_saves_photo_and_updates_unit_item(): void
    {
        Storage::fake('public');

        $asset = $this->asset(quantity: 2);
        $asset->syncItems();
        $staff = $this->staff();

        $photo = UploadedFile::fake()->image('kondisi-awal.jpg');

        $this->actingAs($staff)
            ->post(route('loans.store'), [
                'asset_id' => $asset->id,
                'borrower_id' => $staff->id,
                'quantity' => 1,
                'purpose' => 'Pemeriksaan lapangan',
                'initial_photo' => $photo,
            ])
            ->assertRedirect();

        $loan = AssetLoan::query()->where('asset_id', $asset->id)->firstOrFail();
        $this->assertNotNull($loan->initial_photo_path);
        Storage::disk('public')->assertExists($loan->initial_photo_path);

        $borrowedItem = $asset->items()->where('status', AssetStatus::Dipinjam)->first();
        $this->assertNotNull($borrowedItem);
        $this->assertSame($loan->initial_photo_path, $borrowedItem->photo_path);
    }
}