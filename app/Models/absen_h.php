<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class absen_h extends Model
{
    use HasFactory;
    protected $fillable = [
        'int_absen',
        'bln_absen',
        'thn_absen',
        'no_payroll',
        'nama_asli',
        'bagian',
        'sex',
        'tgl_masuk',
        'no_reg',
        'int_peg',

    ];


    public function absen_d()
    {
        return $this->hasMany(absen_d::class, 'int_peg', 'int_peg');
    }
}
