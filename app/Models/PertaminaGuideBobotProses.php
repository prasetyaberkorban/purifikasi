<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PertaminaGuideBobotProses extends Model
{
    use HasFactory;

    protected $table = 'pertamina_guide_bobot_proses';

    protected $fillable = [
        'tahap',
        'bobot',
    ];

    protected function casts(): array
    {
        return [
            'bobot' => 'float',
        ];
    }
}
