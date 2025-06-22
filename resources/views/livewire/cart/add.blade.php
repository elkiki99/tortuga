<?php

use Livewire\Volt\Component;

new class extends Component {
    public $product;

    public function mount($product)
    {
        $this->product = $product;
    }

    public function addToCart() 
    {
        
    }
}; ?>

<flux:button wire:click="addToCart" variant="primary" class="!rounded-full w-full hover:cursor-pointer"
    icon="shopping-cart">
    Agregar al carrito
</flux:button>
