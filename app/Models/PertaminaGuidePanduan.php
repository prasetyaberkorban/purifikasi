<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PertaminaGuidePanduan extends Model
{
    use HasFactory;

    protected $table = 'pertamina_guide_panduan';

    protected $fillable = [
        'bagian_warna',
        'fungsi',
        'cara_penggunaan',
    ];
}
