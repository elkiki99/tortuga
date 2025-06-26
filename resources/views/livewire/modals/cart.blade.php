<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Product;

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
                $items = $cart->items()->with('product')->get();
                $total = $items->sum(fn($item) => $item->product->price);
            } else {
                $items = collect(); // Usamos una colección vacía para evitar errores
                $total = 0;
            }
        } else {
            $cart = null;
            $items = session('cart', []);
            $total = 0;

            $productIds = collect($items)->pluck('product_id')->unique()->all();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $items = collect($items)
                ->map(function ($item) use ($products) {
                    $item['product'] = $products[$item['product_id']] ?? null;
                    return $item;
                })
                ->filter(fn($item) => $item['product']);

            $total = $items->sum(fn($item) => $item['product']->price);
        }

        return view('livewire.modals.cart', compact('items', 'cart', 'total'));
    }
}; ?>

@php
    $count = Auth::check() ? Auth::user()->cart?->items->count() ?? 0 : count(session('cart', []));
@endphp

<div>
    <flux:modal.trigger name="open-cart">
        <flux:navbar.item class="relative" icon="shopping-cart" href="#" label="Cart"
            badge="{{ $count > 0 ? $count : '' }}" badge-color="red" />
    </flux:modal.trigger>

    <flux:modal name="open-cart" variant="flyout" class="!h-screen">
        <div class="flex flex-col h-full">
            @if (count($items) > 0)
                <div class="mb-6">
                    <flux:heading size="lg">Mi carrito</flux:heading>
                    <flux:text class="mt-2">¡Estas muy cerca de la prenda que queres!</flux:text>
                </div>
                <flux:separator />
            @endif

            <div class="space-y-6 flex-1 flex flex-col overflow-y-auto py-4">
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
                            @if ($item['product'])
                                <div class="flex items-start gap-4">
                                    <a href="{{ route('products.show', $item['product']->slug) }}" wire:navigate
                                        class="block w-full aspect-square object-cover bg-gray-100">
                                        <img src="{{ $item['product']->image_url }}"
                                            alt="{{ $item['product']->name }}" class="w-16 h-16 object-cover">
                                    </a>
                                    <div>
                                        <flux:heading>{{ Str::ucfirst($item['product']->name) }}</flux:heading>
                                        <flux:subheading>${{ $item['product']->price }}UYU</flux:subheading>
                                    </div>
                                </div>
                                <flux:button icon="trash" class="mr-2 hover:cursor-pointer" variant="subtle"
                                    wire:click="removeFromCart({{ $item['product']->id }})" />
                            @endif
                        </div>
                    @endif
                @empty
                    <div
                        class="flex flex-col items-center justify-center h-full text-center space-y-4 overflow-y-hidden">
                        <flux:icon.shopping-cart class="size-12 text-zinc-400" />
                        <flux:heading size="lg">Tu carrito está vacío.</flux:heading>
                        <flux:subheading>Agrega productos para verlos aquí.</flux:subheading>
                        <flux:button size="sm" wire:navigate href="{{ route('home') }}" variant="primary"
                            class="!rounded-full w-full mt-6">
                            Ir a la tienda
                        </flux:button>
                    </div>
                @endforelse
            </div>

            @if (count($items) > 0)
                <footer class="space-y-6">
                    <flux:separator />
                    <div class="flex items-center justify-between">
                        <flux:heading size="lg">Total</flux:heading>
                        <flux:heading size="lg">${{ number_format($total, 2) }} UYU</flux:heading>
                    </div>
                    <div class="flex flex-col">
                        <flux:button as:link href="{{ route('client.checkout') }}" wire:navigate variant="primary"
                            class="!rounded-full w-full">Finalizar
                            compra
                        </flux:button>
                        <flux:button wire:click="clearCart" class="ml-auto mt-4" icon="backspace" size="sm"
                            variant="subtle">Vaciar carrito
                        </flux:button>
                    </div>
                </footer>
            @endif
        </div>
    </flux:modal>
</div>
