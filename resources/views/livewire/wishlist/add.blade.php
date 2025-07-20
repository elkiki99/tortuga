<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public $product;
    public bool $liked = false;

    public function mount($product)
    {        
        $this->product = $product;

        if (Auth::check()) {
            $this->liked = Auth::user()->wishlist?->items()->where('product_id', $product->id)->exists() ?? false;
        } else {
            $this->liked = false;
        }
    }

    public function toggleWishlist()
    {
        $this->authorize('add', \App\Models\Wishlist::class);

        $this->liked = !$this->liked;

        if (Auth::check()) {
            $wishlist = auth()->user()->wishlist()->firstOrCreate([]);

            if ($this->liked) {
                $wishlist->items()->firstOrCreate(['product_id' => $this->product->id]);

                Flux::toast(heading: 'Producto añadido', text: 'Producto añadido a tu wishlist exitosamente', variant: 'success');
            } else {
                $wishlist->items()->where('product_id', $this->product->id)->delete();

                Flux::toast(heading: 'Producto eliminado', text: 'Producto eliminado de tu wishlist exitosamente', variant: 'danger');
            }
        } else {
            $this->liked = false;

            Flux::toast(heading: 'Inicia sesión', text: 'Debes iniciar sesión para usar la wishlist', variant: 'warning');
        }

        $this->dispatch('wishlistUpdated');
    }
}; ?>

<flux:button wire:click="toggleWishlist" @class([
    '!text-red-600' => $liked,
    '!text-red-300' => !$liked,
    '!rounded-full hover:cursor-pointer',
]) variant="ghost" icon="heart" />
