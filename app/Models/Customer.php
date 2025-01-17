<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'otp',
        'otp_expires_at',
        'token',
    ];

    protected $dates = [
        'otp_expires_at',
    ];
}
