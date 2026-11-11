<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PertaminaRefPic extends Model
{
    use HasFactory;

    protected $table = 'pertamina_ref_pic';

    protected $fillable = [
        'nama',
    ];
}
