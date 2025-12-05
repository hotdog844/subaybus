<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteRoute extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'bus_id'];

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }
}