<?php

use Livewire\Volt\Component;

new #[Layout('components.layouts.blank')] #[Title('Éxito • Tortuga')] class extends Component {
    //
}; ?>

<section class="container mx-auto px-4 mt-6 md:px-6 lg:px-8 my-12">
    <flux:breadcrumbs class="my-6">
        <flux:breadcrumbs.item href="{{ route('home') }}" wire:navigate>Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Checkout</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Éxito</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl">Éxito</flux:heading>
</section>
