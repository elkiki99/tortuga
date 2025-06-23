<?php

namespace App\Livewire\Actions;

use App\Models\User;
use App\Models\Cart;

class PersistSessionCartToDatabase
{
    public function __invoke(User $user): void
    {
        $sessionCart = session('cart', []);

        if (empty($sessionCart)) {
            return;
        }

        $cart = $user->cart ?? Cart::create(['user_id' => $user->id]);

        foreach ($sessionCart as $item) {
            $alreadyExists = $cart->items()->where('product_id', $item['product_id'])->exists();

            if (!$alreadyExists) {
                $cart->items()->create([
                    'product_id' => $item['product_id'],
                ]);
            }
        }

        session()->forget('cart');
    }
}
