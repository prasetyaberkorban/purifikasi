<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PertaminaRefHasilPenawaran extends Model
{
    use HasFactory;

    protected $table = 'pertamina_ref_hasil_penawaran';

    protected $fillable = [
        'nama',
    ];
}
