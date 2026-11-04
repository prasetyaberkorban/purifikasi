<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AssetMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'asset_id',
        'pic_name',
        'asset_name',
        'brand_type',
        'damage_description',
        'previously_serviced',
        'service_date',
        'service_location',
        'condition_photo_path',
        'invoice_photo_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function conditionPhotoUrl(): ?string
    {
        return $this->condition_photo_path ? asset('storage/'.$this->condition_photo_path) : null;
    }

    public function invoicePhotoUrl(): ?string
    {
        return $this->invoice_photo_path ? asset('storage/'.$this->invoice_photo_path) : null;
    }

    public static function nextCode(): string
    {
        $prefix = 'SRV.'.now()->format('Y.m');
        $last = self::query()->where('code', 'like', $prefix.'.%')->orderByDesc('id')->value('code');
        $sequence = $last ? ((int) Str::afterLast($last, '.')) + 1 : 1;

        return sprintf('%s.%04d', $prefix, $sequence);
    }
}
