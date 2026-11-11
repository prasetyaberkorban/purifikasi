<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PertaminaRefTahapPelaksanaan extends Model
{
    use HasFactory;

    protected $table = 'pertamina_ref_tahap_pelaksanaan';

    protected $fillable = [
        'tahap',
        'bobot',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'bobot' => 'float',
            'urutan' => 'integer',
        ];
    }
}
