<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Product;

new class extends Component {
    public $items;
    public $cart;
    public $total;
    public $count;

    public function mount()
    {
        $this->showCart();
    }

    #[On('cart-item-removed')]
    #[On('cart-item-added')]
    #[On('cart-cleared')]
    public function refreshCart()
    {
        $this->showCart();
    }
    
    public function showCart()
    {
        $this->authorize('view', \App\Models\Cart::class);
        Flux::modal('open-cart')->show();

        $this->items = collect();
        $this->total = 0;
        $this->cart = null;

        if (Auth::check()) {
            $this->cart = Auth::user()->cart;

            if ($this->cart) {
                $this->items = $this->cart->items()->with('product')->get()->map(
                    fn($item) => (object) [
                        'id' => $item->id,
                        'product' => $item->product,
                        'price' => $item->product->discount_price ?? $item->product->price,
                    ],
                );
            }
        } else {
            $sessionItems = session('cart', []);
            $productIds = collect($sessionItems)->pluck('product_id')->unique()->all();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $this->items = collect($sessionItems)
                ->map(function ($item) use ($products) {
                    $product = $products[$item['product_id']] ?? null;
                    return $product
                        ? (object) [
                            'id' => $product->id,
                            'product' => $product,
                            'price' => $product->discount_price ?? $product->price,
                        ]
                        : null;
                })
                ->filter();
        }

        $this->count = $this->items->count();
        $this->total = $this->items->sum('price');
    }
}; ?>

<div>
    <flux:modal.trigger name="open-cart">
        <flux:navbar.item class="relative" icon="shopping-cart" href="#" label="Cart"
            badge="{{ $count > 0 ? $count : '' }}" badge-color="red" />
    </flux:modal.trigger>

    <flux:modal name="open-cart" variant="flyout" class="!h-screen">

        <div class="flex flex-col h-full">
            @if ($items->isNotEmpty())
                <div class="mb-6">
                    <flux:heading size="lg">Mi carrito</flux:heading>
                    <flux:text class="mt-2">¡Estas muy cerca de la prenda que querés!</flux:text>
                </div>
                <flux:separator />
            @endif

            <div class="space-y-4 flex-1 flex flex-col overflow-y-auto py-4">
                @forelse($items as $item)
                    <div wire:key="item-{{ $item->product->id }}" class="flex items-center justify-between">
                        <div class="flex items-start gap-4">
                            <a href="{{ route('products.show', $item->product->slug) }}" wire:navigate
                                class="block w-16 h-16 aspect-square object-cover bg-gray-100">
                                @if ($item->product->featuredImage)
                                    <img src="{{ Storage::url($item->product->featuredImage->path) }}"
                                        alt="{{ $item->product->name }}" class="object-cover w-16 h-16">
                                @else
                                    <img src="{{ $item->product->featuredImage }}" alt="{{ $item->product->name }}"
                                        class="object-cover w-16 h-16">
                                @endif
                            </a>
                            <div>
                                <flux:heading>{{ Str::ucfirst($item->product->name) }}</flux:heading>
                                <flux:subheading>
                                    ${{ number_format($item->price, 2, ',', '.') }}&nbsp;UYU
                                </flux:subheading>
                            </div>
                        </div>
                        <livewire:cart.remove :key="$item->product->id" :itemId="$item->product->id" />
                    </div>
                @empty
                    <div
                        class="flex flex-col items-center justify-center h-full text-center space-y-4 overflow-y-hidden">
                        <flux:icon.shopping-cart class="size-12 text-zinc-400" />
                        <flux:heading size="lg">Tu carrito está vacío.</flux:heading>
                        <flux:subheading>Agregá productos para verlos aquí.</flux:subheading>
                    </div>
                @endforelse
            </div>

            @if ($items->isNotEmpty())
                <footer class="space-y-6">
                    <flux:separator />
                    <div class="flex items-center justify-between">
                        <flux:subheading size="lg">Total</flux:subheading>
                        <flux:heading size="lg">${{ number_format($total, 2, ',', '.') }}&nbsp;UYU
                        </flux:heading>
                    </div>
                    <div>
                        <flux:button as:link href="{{ route('client.checkout') }}" variant="primary"
                            class="!rounded-full w-full">Finalizar compra</flux:button>
                        <livewire:cart.clear />
                    </div>
                </footer>
            @endif
        </div>
    </flux:modal>
</div>
