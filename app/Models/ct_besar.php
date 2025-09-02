<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ct_besar extends Model
{
    use HasFactory;
    protected $fillable = [
        'no_reg',
        'no_payroll',
        'nama_asli',
        'kcuti',
        'cuti_bsr',
        'sisa_cb',
        'tahun',
        'lebih_cb',
        'sisa_cl',
    ];
}
