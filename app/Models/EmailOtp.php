<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailOtp extends Model
{
    protected $fillable = ['email', 'otp', 'expires_at'];
     public $timestamps = true;
     public function user()
    {
        return $this->belongsTo(User::class);
    }
}
