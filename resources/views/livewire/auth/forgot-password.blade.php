<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        Flux::toast(variant: 'success', heading: 'Enlace de verificación enviado', text: 'Hemos enviado un nuevo enlace de verificación a tu email');
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('¿Olvidaste tu contraseña?')" :description="__('Ingresa tu email para recibir el enlace de verificación')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Correo electrónico')"
            type="email"
            required
            autofocus
            placeholder="email@example.com"
            viewable
        />

        <flux:button variant="primary" type="submit" class="w-full">{{ __('Enviar') }}</flux:button>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
        {{ __('O vuelve a ') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('iniciar sesión') }}</flux:link>
    </div>
</div>
