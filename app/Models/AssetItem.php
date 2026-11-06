<?php

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetItem extends Model
{
    protected $fillable = [
        'asset_id',
        'item_code',
        'serial_number',
        'unit_number',
        'condition',
        'status',
        'photo_path',
        'notes',
        'room',
        'purchase_price',
    ];

    protected $casts = [
        'condition' => AssetCondition::class,
        'status' => AssetStatus::class,
        'unit_number' => 'integer',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
