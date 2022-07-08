<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_product extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'recipe_id',
        'product_code',
        'image',
        'name',
        'cost',
        'price',
        'isActive',
    ];
    
}
