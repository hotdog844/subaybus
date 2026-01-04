<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    // ✅ This line unlocks the fields so you can save data
    protected $fillable = [
        'title',
        'message',
        'type'
    ];
}