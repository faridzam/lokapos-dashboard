<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pos_order_desktop extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'no_invoice',
        'pc_id',
        'store_id',
        'cashier_id',
        'payment_id',
        'bill_amount',
        'tax',
        'discount',
        'isActive',
        'created_at',
        'updated_at',
    ];
}
