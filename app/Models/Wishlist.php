<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use App\Policies\WishlistPolicy;
use App\Models\WishlistItem;

#[UsePolicy(WishlistPolicy::class)]
class Wishlist extends Model
{
    /** @use HasFactory<\Database\Factories\WishlistFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    public function items() : HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }
}
