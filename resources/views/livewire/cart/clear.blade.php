<?php

use Livewire\Volt\Component;

new class extends Component {
    
    public function clearCart(): void
    {
        $this->authorize('clear', \App\Models\Cart::class);

        if (Auth::check()) {
            $cart = Auth::user()->cart;
            $cart->items()->delete();
        } else {
            session(['cart' => []]);
        }

        $this->dispatch('cart-cleared');
    }
}; ?>

<div class="flex flex-col">
    <flux:modal.trigger name="clear-cart">
        <flux:button class="ml-auto mt-4" icon="backspace" size="sm" variant="subtle">
            Vaciar carrito
        </flux:button>
    </flux:modal.trigger>

    <form wire:submit.prevent="clearCart">
        <flux:modal name="clear-cart" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">¿Vaciar carrito?</flux:heading>
                    <flux:subheading>
                        Esta acción eliminará todos los productos del carrito. ¿Estás seguro?
                    </flux:subheading>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button variant="ghost">Cancelar</flux:button>
                    </flux:modal.close>

                    <flux:button variant="danger" type="submit">Vaciar carrito</flux:button>
                </div>
            </div>
        </flux:modal>
    </form>
</div>
