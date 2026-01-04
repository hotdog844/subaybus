<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'distance',
        'color',

        // --- FIXED: Text Names (Must match Controller) ---
        'origin',        // Was 'start_location' -> changed to 'origin'
        'destination',   // Was 'end_location'   -> changed to 'destination'

        // --- FIXED: Coordinates (Must match Database columns) ---
        'origin_lat',
        'origin_lng',
        'destination_lat', // Was 'dest_lat' -> changed to 'destination_lat'
        'destination_lng', // Was 'dest_lng' -> changed to 'destination_lng'

        // --- FIXED: Blue Line Data (Was missing!) ---
        'path_data',
    ];

    // --- RELATIONSHIPS ---

    // 1. Standard naming convention
    public function stops()
    {
        return $this->hasMany(Stop::class);
    }

    // 2. The specific method your error is asking for
    public function busStops()
    {
        return $this->hasMany(Stop::class);
    }
}