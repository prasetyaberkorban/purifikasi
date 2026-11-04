<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetActivity extends Model
{
    protected $fillable = ['asset_id', 'user_id', 'type', 'subject', 'description', 'meta'];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function icon(): string
    {
        return match ($this->type) {
            'created' => 'plus',
            'updated' => 'pencil',
            'loan' => 'arrow-up-right',
            'return' => 'arrow-down-left',
            'condition' => 'shield-check',
            default => 'bell',
        };
    }

    public function tone(): string
    {
        return match ($this->type) {
            'created' => 'leaf',
            'loan' => 'sky',
            'return' => 'leaf',
            'condition' => 'olive',
            default => 'muted',
        };
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('created_at')->orderByDesc('id');
    }
}