<?php

use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use App\Models\Product;

new class extends Component {
    public $product;

    public function mount(Product $product)
    {
        $this->product = $product;
    }
}; ?>

<article class="relative">
    @can('add', \App\Models\Wishlist::class)
        <div class="absolute top-2 right-2 z-10">
            <livewire:wishlist.add :product="$product" :key="'wishlist-add-' . $product->id" />
        </div>
    @endcan

    <a href="{{ route('products.show', $product->slug) }}" wire:navigate
        class="block rounded-md w-full aspect-square overflow-hidden bg-gray-100 mb-4">
        @if ($product->featuredImage)
            <img src="{{ Storage::url($product->featuredImage->path) }}" alt="{{ $product->name }}"
                class="w-full h-full object-cover">
        @else
            <img src="{{ $product->featuredImage }}" alt="{{ $product->name }}"
                class="w-full h-full object-cover">
        @endif
    </a>

    @if ($product->discount_price)
        <div class="flex items-center gap-2">
            <flux:heading class="text-red-600 !mb-0" size="lg">
                <strong>${{ number_format($product->discount_price, 2, ',', '.') }}&nbsp;UYU</strong>
            </flux:heading>
            <flux:subheading class="line-through">${{ number_format($product->price, 2, ',', '.') }}&nbsp;UYU</flux:subheading>
        </div>
    @else
        <flux:heading size="lg"><strong>${{ number_format($product->price, 2, ',', '.') }}&nbsp;UYU</strong></flux:heading>
    @endif

    <div class="mt-2">
        <flux:heading class="!mb-1" size="lg">{{ Str::ucfirst($product->name) }}</flux:heading>
        <flux:subheading>
            <flux:link variant="subtle" href="{{ route('categories.show', $product->category->slug) }}" wire:navigate>
                {{ Str::ucfirst($product->category->name) }}</flux:link>
        </flux:subheading>

    </div>
</article>
