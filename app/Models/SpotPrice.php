<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpotPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'timestamp',
        'price_in_eurocent',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'price_in_eurocent' => 'integer',
    ];
}
