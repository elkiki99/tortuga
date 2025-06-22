<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component {
    #[On('open-cart')]
    public function openCart(): void
    {
        Flux::modal('open-cart')->show();
    }

    public function removeFromCart($itemId): void
    {
        if (Auth::check()) {
            $cart = Auth::user()->cart;
            $cart->items()->where('id', $itemId)->delete();

            if ($cart->items()->count() === 0) {
                Flux::modal('open-cart')->close();
            }
        } else {
            $cart = session('cart', []);
            unset($cart[$itemId]);
            session(['cart' => $cart]);

            if (empty($cart)) {
                Flux::modal('open-cart')->close();
            }
        }
    }

    public function render(): mixed
    {
        $items = Auth::check() ? Auth::user()->cart->items : session('cart', []);

        return view('livewire.modals.cart', compact('items'));
    }
}; ?>

<flux:modal name="open-cart" variant="flyout">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Mi carrito</flux:heading>
            <flux:text class="mt-2">Tus prendas.</flux:text>
        </div>

        @forelse($items as $item)
            @if (Auth::check())
                {{-- Usuario autenticado: $item es un modelo CartItem --}}
                <div wire:key="item-{{ $item->id }}" class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                            class="w-16 h-16 object-cover rounded">
                        <div>
                            <flux:text>{{ $item->product->name }}</flux:text>
                            <flux:text class="text-sm text-zinc-500">{{ $item->product->price }} €</flux:text>
                        </div>
                    </div>
                    <flux:button wire:click="removeFromCart({{ $item->id }})" variant="danger"
                        class="!rounded-full hover:cursor-pointer">
                        <flux:icon.trash class="w-5 h-5" />
                    </flux:button>
                </div>
            @else
                {{-- Invitado: $item es un array --}}
                <div wire:key="item-{{ $item['product_id'] }}" class="flex items-center justify-between">
                    @php
                        $product = \App\Models\Product::find($item['product_id']);
                    @endphp

                    @if ($product)
                        <div class="flex items-center gap-4">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                class="w-16 h-16 object-cover rounded">
                            <div>
                                <flux:text>{{ $product->name }}</flux:text>
                                <flux:text class="text-sm text-zinc-500">{{ $product->price }} €</flux:text>
                            </div>
                        </div>
                        <flux:button wire:click="removeFromCart({{ $product->id }})" variant="danger"
                            class="!rounded-full hover:cursor-pointer">
                            <flux:icon.trash class="w-5 h-5" />
                        </flux:button>
                    @endif
                </div>
            @endif
        @empty
            <flux:text>No hay productos en tu carrito.</flux:text>
        @endforelse
    </div>
</flux:modal>
