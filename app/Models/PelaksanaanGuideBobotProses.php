<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelaksanaanGuideBobotProses extends Model
{
    use HasFactory;

    protected $table = 'pelaksanaan_guide_bobot_proses';

    protected $fillable = [
        'tahap',
        'bobot',
    ];

    protected $casts = [
        'bobot' => 'float',
    ];
}
