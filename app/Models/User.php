<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'passenger_type',
        'id_image_path',
        'is_verified',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Override the default email verification notification to use Make.com.
     */
    public function sendEmailVerificationNotification()
    {
        // Generate the unique, signed verification URL
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->getKey(), 'hash' => sha1($this->getEmailForVerification())]
        );

        // Send the data to your Make.com webhook
        Http::post(env('MAKE_VERIFICATION_WEBHOOK_URL'), [
            'email' => $this->getEmailForVerification(),
            'name' => $this->name,
            'verification_url' => $verificationUrl,
        ]);
    }

    public function favoriteRoutes()
{
    return $this->belongsToMany(Route::class, 'favorite_routes');
}

}