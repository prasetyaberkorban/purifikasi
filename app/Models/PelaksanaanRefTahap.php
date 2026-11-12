<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelaksanaanRefTahap extends Model
{
    use HasFactory;

    protected $table = 'pelaksanaan_ref_tahap';

    protected $fillable = [
        'tahap',
        'bobot',
        'urutan',
        'keterangan',
    ];

    protected $casts = [
        'bobot' => 'float',
        'urutan' => 'integer',
    ];
}
