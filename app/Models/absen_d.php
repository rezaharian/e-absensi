<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class absen_d extends Model
{
    use HasFactory;
    protected $fillable = [
        'no_reg',
        'int_absen',
        'bln_absen',  
        'thn_absen',  
        'tgl_absen',  
        'jns_absen',  
        'dsc_absen',  
        'keterangan',  
        'int_absen_d',
        'int_peg',
        'thn_jns'
    ];

}
