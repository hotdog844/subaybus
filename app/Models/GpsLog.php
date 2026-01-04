<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GpsLog extends Model
{
    use HasFactory;

    protected $table = 'gps_logs';

    protected $fillable = [
        'device_id',
        'latitude',
        'longitude',
        'speed',
        'battery_level',
        'timestamp'
    ];
}