<?php

use Livewire\Volt\Component;

new class extends Component {
    
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

    public function clearCart(): void
    {
        if (Auth::check()) {
            $cart = Auth::user()->cart;
            $cart->items()->delete();
        } else {
            session(['cart' => []]);
        }
    }

    public function render(): mixed
    {
        if (Auth::check()) {
            $cart = Auth::user()->cart;
            if ($cart) {
                $items = $cart->items;
                $total = $items->sum(fn($item) => $item->product->price);
            } else {
                $items = [];
                $total = 0;
            }
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

        return view('livewire.client.checkout', compact('items', 'cart', 'total'));
    }
}; ?>

<section class="min-h-screen container mx-auto px-4 mt-6 md:px-6 lg:px-8 my-12">
    <flux:breadcrumbs class="my-6">
        <flux:breadcrumbs.item href="{{ route('home') }}" wire:navigate>Home</flux:breadcrumbs.item>
        {{-- <flux:breadcrumbs.item href="{{ route('client.cart') }}" wire:navigate>Carrito</flux:breadcrumbs.item> --}}
        <flux:breadcrumbs.item>Checkout</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl">Checkout</flux:heading>

    <div class="flex flex-col h-full w-1/2">
        {{-- @if (count($items) > 0)
            <div class="mb-6">
                <flux:heading size="lg">Mi carrito</flux:heading>
                <flux:text class="mt-2">¡Estas muy cerca de la prenda que queres!</flux:text>
            </div>
            <flux:separator />
        @endif --}}

        <div class="space-y-6 flex-1 flex flex-col overflow-y-auto py-4 py-2">
            @forelse($items as $item)
                @if (Auth::check())
                    <div wire:key="item-{{ $item->product->id }}" class="flex items-center justify-between">
                        <div class="flex items-start gap-4">
                            <a href="{{ route('products.show', $item->product->slug) }}" wire:navigate
                                class="block w-full aspect-square object-cover bg-gray-100">
                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                                    class="w-16 h-16 object-cover">
                            </a>

                            <div>
                                <flux:heading>{{ Str::ucfirst($item->product->name) }}</flux:heading>
                                <flux:subheading>${{ $item->product->price }}UYU</flux:subheading>
                            </div>
                        </div>
                        <flux:button icon="trash" class="mr-2 hover:cursor-pointer" variant="subtle"
                            wire:click="removeFromCart({{ $item->id }})" />
                    </div>
                @else
                    <div wire:key="item-{{ $item['product_id'] }}" class="flex items-center justify-between">
                        @php
                            $product = \App\Models\Product::find($item['product_id']);
                        @endphp

                        @if ($product)
                            <div class="flex items-start gap-4">
                                <a href="{{ route('products.show', $product->slug) }}" wire:navigate
                                    class="block w-full aspect-square object-cover bg-gray-100">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                        class="w-16 h-16 object-cover">
                                </a>
                                <div>
                                    <flux:heading>{{ Str::ucfirst($product->name) }}</flux:heading>
                                    <flux:subheading>${{ $product->price }}UYU</flux:subheading>
                                </div>
                            </div>
                            <flux:button icon="trash" class="mr-2 hover:cursor-pointer" variant="subtle"
                                wire:click="removeFromCart({{ $product->id }})" />
                        @endif
                    </div>
                @endif
            @empty
                {{-- <div class="flex flex-col items-center justify-center h-full text-center space-y-4 overflow-y-hidden">
                    <flux:icon.shopping-cart class="size-12 text-zinc-400" />
                    <flux:heading size="lg">Tu carrito está vacío.</flux:heading>
                    <flux:subheading>Agrega productos para verlos aquí.</flux:subheading>
                    <flux:button size="sm" wire:navigate href="{{ route('home') }}" variant="primary"
                        class="!rounded-full w-full mt-6">
                        Ir a la tienda
                    </flux:button>
                </div> --}}
            @endforelse
        </div>

        @if (count($items) > 0)
            <footer class="space-y-6">
                <flux:separator />
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">Total</flux:heading>
                    <flux:heading size="lg">${{ number_format($total, 2) }} UYU</flux:heading>
                </div>
                {{-- <div class="flex flex-col">
                    <flux:button as:link href="{{ route('client.checkout') }}" wire:navigate variant="primary"
                        class="!rounded-full w-full">Finalizar
                        compra
                    </flux:button>
                    <flux:button wire:click="clearCart" class="ml-auto mt-4" icon="backspace" size="sm"
                        variant="subtle">Vaciar carrito
                    </flux:button>
                </div> --}}
            </footer>
        @endif
    </div>
</section>
