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
        } else {
            $cart = session('cart', []);
            unset($cart[$itemId]);
            session(['cart' => $cart]);
        }
    }

    public function render(): mixed
    {
        if (Auth::check()) {
            $cart = Auth::user()->cart;
            $items = $cart->items;
            $total = $cart->items->sum(function ($item) {
                return $item->product->price;
            });
        } else {
            $cart = null;
            $items = session('cart', []);
            $total = 0;

            foreach ($items as $item) {
                $product = \App\Models\Product::find($item['product_id']);
                if ($product) {
                    $total += $product->price;
                }
            }
        }

        return view('livewire.modals.cart', compact('items', 'cart', 'total'));
    }
}; ?>

<flux:modal name="open-cart" variant="flyout" class="!h-screen">
    <div class="flex flex-col h-full">
        @if (count($items) > 0)
            <div class="mb-6">
                <flux:heading size="lg">Mi carrito</flux:heading>
                <flux:text class="mt-2">¡Estas muy cerca de la prenda que queres!</flux:text>
            </div>
            <flux:separator class="!mb-2" />
        @endif

        <div class="space-y-6 flex-1 overflow-y-auto py-4">
            @forelse($items as $item)
                @if (Auth::check())
                    <div wire:key="item-{{ $item->product->id }}" class="flex items-center justify-between">
                        <div class="flex items-start gap-4">
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                                class="w-16 h-16 object-cover bg-gray-100">
                            <div>
                                <flux:heading>{{ Str::ucfirst($item->product->name) }}</flux:heading>
                                <flux:subheading>${{ $item->product->price }}UYU</flux:subheading>
                            </div>
                        </div>
                        <flux:button icon="trash" class="mr-2" variant="subtle" wire:click="removeFromCart({{ $item->id }})" />
                    </div>
                @else
                    <div wire:key="item-{{ $item['product_id'] }}" class="flex items-center justify-between">
                        @php
                            $product = \App\Models\Product::find($item['product_id']);
                        @endphp

                        @if ($product)
                            <div class="flex items-start gap-4">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                    class="w-16 h-16 object-cover bg-gray-100">
                                <div>
                                    <flux:heading>{{ Str::ucfirst($product->name) }}</flux:heading>
                                    <flux:subheading>${{ $product->price }}UYU</flux:subheading>
                                </div>
                            </div>
                            <flux:button icon="trash" class="mr-2" variant="subtle"
                                wire:click="removeFromCart({{ $product->id }})" />
                        @endif
                    </div>
                @endif
            @empty
                <div class="flex flex-col items-center justify-center h-full text-center space-y-4 overflow-y-hidden">
                    <flux:icon.shopping-cart class="w-16 h-16 text-zinc-400" />
                    <flux:heading size="xl">Tu carrito está vacío.</flux:heading>
                    <flux:subheading>Agrega productos para verlos aquí.</flux:subheading>
                    <flux:button wire:navigate href="{{ route('home') }}" variant="primary"
                        class="!rounded-full w-full mt-6">
                        Ir a la tienda
                    </flux:button>
                </div>
            @endforelse
        </div>

        @if (count($items) > 0)
            <footer class="space-y-6">
                <flux:separator class="!mt-2" />
                {{-- <flux:heading size="lg">Total ${{ number_format($total, 2) }} UYU</flux:heading> --}}
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">Total</flux:heading>
                    <flux:heading size="lg">${{ number_format($total, 2) }} UYU</flux:heading>
                </div>
                <flux:button variant="primary" class="!rounded-full w-full">Finalizar compra</flux:button>
            </footer>
        @endif
    </div>
</flux:modal>
