<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'image_url',
        'address',
        'phone',
        'status',
        'role',
        'is_subscribe',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    /**
     * Get the orders for the user.
     *
     */
    public function order()
    {
        return $this->hasMany(Order::class);
    }
    public function importReceipt()
    {
        return $this->hasMany(ImportReceipt::class);
    }
    public function emailOtps()
    {
        return $this->hasMany(EmailOtp::class);
    }
    public function productReports()
    {
        return $this->hasMany(ProductReport::class);
    }
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
