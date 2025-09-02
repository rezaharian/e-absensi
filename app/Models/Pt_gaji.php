<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pt_gaji extends Model
{
    protected $fillable = [
        'no_payroll',
        'nama_asli',
        'gj_bulan',
        'no_bln',
        'thn',
        'tahun_cuti',
        'jml_hari',
        'keterangan',
    ];
}
