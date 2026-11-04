<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'city', 'address', 'pic_name', 'pic_phone'];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function getLabelAttribute(): string
    {
        return $this->city ? "{$this->name}, {$this->city}" : $this->name;
    }
}