<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpotPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_time',
        'price_in_eurocent',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'price_in_eurocent' => 'integer',
    ];

}
