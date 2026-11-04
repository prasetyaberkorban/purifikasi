<?php

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\LoanStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'asset_id', 'borrower_id', 'handled_by', 'quantity', 'purpose', 'room',
        'borrowed_at', 'due_at', 'returned_at', 'status', 'condition_on_return', 'initial_photo_path', 'note',
    ];

    protected function casts(): array
    {
        return [
            'status' => LoanStatus::class,
            'condition_on_return' => AssetCondition::class,
            'borrowed_at' => 'datetime',
            'due_at' => 'date',
            'returned_at' => 'datetime',
            'quantity' => 'integer',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function isOverdue(): bool
    {
        return $this->status === LoanStatus::Dipinjam
            && $this->due_at !== null
            && $this->due_at->isPast();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', LoanStatus::Dipinjam->value);
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('borrowed_at')->orderByDesc('id');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('code', 'like', "%{$term}%")
                ->orWhere('purpose', 'like', "%{$term}%")
                ->orWhereHas('asset', fn (Builder $a) => $a->where('name', 'like', "%{$term}%")->orWhere('code', 'like', "%{$term}%"))
                ->orWhereHas('borrower', fn (Builder $u) => $u->where('name', 'like', "%{$term}%"));
        });
    }
}