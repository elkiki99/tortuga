<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;

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

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeSearch($query, $term)
    {
        if (trim($term) === '') return $query;

        return $query->where(function ($query) use ($term) {
            $query->where('buyer_email', 'like', '%' . $term . '%')
                ->orWhere('purchase_id', 'like', '%' . $term . '%');
        });
    }
}
