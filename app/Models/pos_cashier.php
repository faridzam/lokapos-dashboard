<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_cashier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'email',
        'email_verified_at',
        'password',
        'isActive',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
