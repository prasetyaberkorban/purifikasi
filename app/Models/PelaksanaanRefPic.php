<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelaksanaanRefPic extends Model
{
    use HasFactory;

    protected $table = 'pelaksanaan_ref_pic';

    protected $fillable = [
        'nama',
    ];
}
