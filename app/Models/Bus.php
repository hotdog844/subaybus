<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Route;       // <-- Added missing import
use App\Models\User;        // <-- Added missing import (for Drivers)
use App\Models\FareMatrix;  // <-- Added missing import

class Bus extends Model
{
    use HasFactory;

    // 1. ALLOW MASS ASSIGNMENT
    protected $fillable = [
        'bus_number',    
        'plate_number',
        'type',          
        'capacity',
        'status',        
        'device_id',     
        'route_id',      
        'driver_id',
        'fare_matrix_id',     
        'fare',
        'lat', 'lng',
        'last_seen',
    ];

    protected $casts = [
        'last_seen' => 'datetime',
        'fare' => 'float',
    ];

    // --- RELATIONSHIPS ---

    // 1. FOR THE MOBILE APP (API)
    public function route() 
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    // 2. FOR THE ADMIN PANEL
    public function assignedRoute() 
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    // 3. FOR THE DRIVER (CRITICAL FIX)
    // We changed 'Driver::class' to 'User::class' because your drivers are Users.
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // 4. FARE MATRIX
    public function fareMatrix()
    {
        return $this->belongsTo(FareMatrix::class, 'fare_matrix_id');
    }
}