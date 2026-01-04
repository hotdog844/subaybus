<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupRequest extends Model
{
    use HasFactory;

    // 1. Allow these fields to be saved (Mass Assignment)
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'expires_at'
    ];

    // 2. Link this request to a User (Passenger)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}