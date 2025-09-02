<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'office_id',
        'type',
        'time',
        'address',
        'longitude',
        'latitude',
        'no_payroll',
        'photo',
    ];

    protected $casts = [
        'time' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
