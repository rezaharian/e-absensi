<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TglLibur extends Model
{
    use HasFactory;
    protected $fillable = [
        'tgl_libur',
        'keterangan',

    ];
}
