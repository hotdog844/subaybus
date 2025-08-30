<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Route as BusRoute; // Use an alias

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number', 'driver_id', 'route_id', 'route_name',
        'fare', 'status', 'latitude', 'longitude', 'last_seen',
    ];

    protected $casts = [
        'last_seen' => 'datetime',
        'fare' => 'float',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function route()
    {
        return $this->belongsTo(BusRoute::class, 'route_id');
    }
}