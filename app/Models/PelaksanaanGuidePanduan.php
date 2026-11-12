<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelaksanaanGuidePanduan extends Model
{
    use HasFactory;

    protected $table = 'pelaksanaan_guide_panduan';

    protected $fillable = [
        'bagian_warna',
        'fungsi',
        'cara_penggunaan',
    ];
}
