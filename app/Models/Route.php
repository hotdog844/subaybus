<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [ 'name', 'start_destination', 'end_destination', 'description', ];

    public function busStops()
{
    // Order the stops by their sequence number
    Route::resource('something', BusStopController::class);
}

public function buses()
{
    return $this->hasMany(Bus::class, 'route_id');
}
    
}