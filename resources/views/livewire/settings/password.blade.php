<?php

use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\{Layout, Title};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Component;

new #[Layout('components.layouts.dashboard')] #[Title('Password • Tortuga')] class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        Flux::toast(variant: 'success', heading: 'Contraseña actualizada', text: 'Tu contraseña ha sido actualizada exitosamente');
    }
}; ?>

<section class="w-full">
    @include('livewire.partials.settings-heading')

    <x-settings.layout :heading="__('Actualiza tu contraseña')" :subheading="__('Asegurate de que tu cuenta utilice una contraseña larga y aleatoria para mantener su seguridad')">
        <form wire:submit="updatePassword" class="mt-6 space-y-6">
            <flux:input wire:model="current_password" :label="__('Contraseña actual')" type="password" required
                autocomplete="current-password" viewable />
            <flux:input wire:model="password" :label="__('Nueva contraseña')" type="password" required
                autocomplete="new-password" viewable />
            <flux:input wire:model="password_confirmation" :label="__('Confirmar contraseña')" type="password" required
                autocomplete="new-password" viewable />

            <!-- Submit button -->
            <flux:button variant="primary" type="submit">{{ __('Guardar') }}</flux:button>
        </form>
    </x-settings.layout>
</section>
