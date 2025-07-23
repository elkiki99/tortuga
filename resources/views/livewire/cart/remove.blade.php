<?php

use Livewire\Volt\Component;

new class extends Component {

    public $itemId;

    public function mount($itemId)
    {
        $this->itemId = $itemId;
    }

    public function removeFromCart(): void
    {
        $this->authorize('remove', \App\Models\Cart::class);

        if (Auth::check()) {
            Auth::user()->cart->items()->where('id', $this->itemId)->delete();
        } else {
            $cart = session('cart', []);
            unset($cart[$this->itemId]);
            session(['cart' => $cart]);
        }

        $this->dispatch('cart-item-removed');
    }
}; ?>

<flux:button icon="trash" class="mr-2" variant="subtle" wire:click="removeFromCart" />
