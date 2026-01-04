<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stop extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'route_name', // We added this in the previous step
        'name',
        'latitude',
        'longitude',
        'sequence',
        'lat', 
        'lng',
        'order_index'
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}