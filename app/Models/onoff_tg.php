<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class onoff_tg extends Model
{
    use HasFactory;
    protected $fillable = [
        'tgl_off',
        'tgl_on',

    ];
}
