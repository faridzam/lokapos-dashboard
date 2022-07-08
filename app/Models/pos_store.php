<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_store extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'store_id',
        'name',
        'ip_address_mobile',
        'type',
        'area',
        'platform',
        'isActive',
    ];

}
