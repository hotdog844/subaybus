<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Driver extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [ 'name', 'email', 'license_number', 'contact_number', 'password', 'license_image_path', 'cert_image_path', ];
    protected $hidden = [ 'password', 'remember_token', ];

    public function bus()
    {
        return $this->hasOne(Bus::class);
    }
}