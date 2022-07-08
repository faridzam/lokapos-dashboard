<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_category extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'type',
        'platform',
        'isActive',
    ];
    
    use HasFactory;
}
