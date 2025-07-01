<?php

use Livewire\Volt\Component;

new class extends Component {
    public $name;
    public $email;

    public function saveGuestDataForCheckout()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        session([
            'guest_checkout_data' => [
                'name' => $this->name,
                'email' => $this->email,
            ],
        ]);

        $this->reset(['name', 'email']);

        $this->dispatch('guest-data-saved');

        Flux::toast(variant: 'success', heading: 'Guardado correctamente.', text: 'Tus datos se guardaron correctamente en la sesión.');
    }
}; ?>

<div class="mt-6 w-full md:w-1/2 space-y-6">
    <flux:heading size="lg">Ingresa tus datos para terminar la compra</flux:heading>

    <flux:card>
        <div class="space-y-6">

            <flux:input type="text" label="Nombre" wire:model="name" placeholder="John Doe" />
            <flux:input type="email" label="Email" wire:model="email" placeholder="johndoe@example.com" />

            <div class="flex">
                <flux:spacer />
                <flux:button wire:click="saveGuestDataForCheckout" class="hover:cursor-pointer" type="submit"
                    variant="primary">Guardar</flux:button>
            </div>
        </div>
    </flux:card>
</div>
