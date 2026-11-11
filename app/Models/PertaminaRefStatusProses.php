<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PertaminaRefStatusProses extends Model
{
    use HasFactory;

    protected $table = 'pertamina_ref_status_proses';

    protected $fillable = [
        'nama',
    ];
}
