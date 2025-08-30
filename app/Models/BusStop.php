<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'name',
        'latitude',
        'longitude',
        'sequence',
    ];

    /**
     * Get the route that this bus stop belongs to.
     */
    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}