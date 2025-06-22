<?php

use Livewire\Attributes\{Layout, Title, On};
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] #[Title('Wishlist • Tortuga')] class extends Component {
    use WithPagination;

    // public function refreshWishlist()
    // {
    //     $this->dispatch('$refresh');
    // }

    #[On('wishlistUpdated')]
    public function render(): mixed
    {
        $wishlist = Auth::user()->wishlist()->firstOrCreate();

        $items = $wishlist->items()->with('product')->latest()->paginate(12);

        $products = $items->map(fn($item) => $item->product);

        return view('livewire.client.wishlist', [
            'products' => $products,
            'paginator' => $items,
        ]);
    }
}; ?>

<section class="min-h-screen container mx-auto px-4 mt-6 md:px-6 lg:px-8 my-12">
    <flux:breadcrumbs class="my-6">
        <flux:breadcrumbs.item href="{{ route('home') }}" wire:navigate>Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Wishlist</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl">Wishlist</flux:heading>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-6">
        @forelse ($products as $product)
            <livewire:components.product-card wire:key="product-{{ $product->id }}" :product="$product" />
        @empty
            <flux:text>
                Tu wishlist está vacía. Agrega productos a tu lista de deseos para verlos aquí.
            </flux:text>
        @endforelse
    </div>

    @if ($paginator->hasPages())
        <flux:pagination :paginator="$paginator" />
    @endif
</section>
