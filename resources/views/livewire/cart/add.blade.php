<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $product;

    public function mount($product)
    {
        $this->product = $product;
    }

    public function addToCart()
    {
        $this->authorize('add', \App\Models\Cart::class);

        if (Auth::check()) {
            $cart = Auth::user()->cart()->firstOrCreate([]);

            if (!$cart->items()->where('product_id', $this->product->id)->exists()) {
                $cart->items()->create([
                    'product_id' => $this->product->id,
                ]);
            }
        } else {
            $cart = session()->get('cart', []);

            if (!array_key_exists($this->product->id, $cart)) {
                $cart[$this->product->id] = [
                    'product_id' => $this->product->id,
                ];
                session()->put('cart', $cart);
            }
        }

        $this->dispatch('cart-item-added');
    }
}; ?>

<div class="flex-1">
    @can('add', \App\Models\Cart::class)
        <flux:button wire:click="addToCart" variant="primary" class="!rounded-full w-full hover:cursor-pointer"
            icon="shopping-cart">
            Agregar al carrito
        </flux:button>
    @else
        <flux:button variant="primary" class="!rounded-full w-full hover:cursor-not-allowed"
            icon="shopping-cart">
            Agregar al carrito
        </flux:button>
    @endcan
</div>
