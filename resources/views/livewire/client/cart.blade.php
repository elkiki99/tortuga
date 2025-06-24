<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<section class="min-h-screen container mx-auto px-4 mt-6 md:px-6 lg:px-8 my-12">
    <flux:breadcrumbs class="my-6">
        <flux:breadcrumbs.item href="{{ route('home') }}" wire:navigate>Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Carrito</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl">Carrito</flux:heading>

    
</section>
