<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'distance',
        'color',
        'origin',
        'destination',
        'origin_lat',
        'origin_lng',
        'destination_lat',
        'destination_lng',
        'path_data',
        'stops' // <--- IMPORTANTE: Para ma-save ang Red Pins
    ];

    protected $casts = [
        'path_data' => 'array',
        'stops' => 'array', // <--- IMPORTANTE: Para mabasa ng Dashboard bilang Listahan, hindi text
    ];
    
    // TANGGALIN MO NA ANG MGA "public function stops()" dito para walang conflict.
}