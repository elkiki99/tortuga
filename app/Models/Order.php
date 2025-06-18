<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrdersFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'buyer_name',
        'buyer_email',
        'purchase_id',
        'purchase_date',
        'total',
        'status',
        'payment_method',
    ];
}
