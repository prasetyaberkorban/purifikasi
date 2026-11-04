<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'pic_name',
        'driver_name',
        'departure_date',
        'return_date',
        'departure_km',
        'vehicle_type',
        'purpose',
        'cleanliness_condition',
        'suggestions',
        'departure_speedometer_photo_path',
        'return_speedometer_photo_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'return_date' => 'date',
        ];
    }

    public static function vehicleTypeOptions(): array
    {
        return [
            'Innova Reborn' => 'Innova Reborn',
            'Ertiga' => 'Ertiga',
            'Ignis' => 'Ignis',
            'Xenia' => 'Xenia',
            'other' => 'Yang lain:',
        ];
    }

    public static function cleanlinessOptions(): array
    {
        return [
            'sampah sudah dibersihkan' => 'Sampah sudah dibersihkan',
            'sampah masih tersisa di dalam mobil' => 'Sampah masih tersisa di dalam mobil',
            'other' => 'Yang lain:',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function departureSpeedometerPhotoUrl(): ?string
    {
        return $this->departure_speedometer_photo_path
            ? asset('storage/'.$this->departure_speedometer_photo_path)
            : null;
    }

    public function returnSpeedometerPhotoUrl(): ?string
    {
        return $this->return_speedometer_photo_path
            ? asset('storage/'.$this->return_speedometer_photo_path)
            : null;
    }

    public static function nextCode(): string
    {
        $prefix = 'PKND.'.date('Y.m.');
        $latest = static::query()
            ->where('code', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('code');

        $sequence = 1;
        if ($latest && preg_match('/(\d+)$/', $latest, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
