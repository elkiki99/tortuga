<?php

use App\Livewire\Actions\PersistSessionCartToDatabase;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\User;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(PersistSessionCartToDatabase $persistSessionCart): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'client';

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $persistSessionCart($user);

        if (Auth::user()->isAdmin()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        } else {
            $this->redirectIntended(default: route('home', absolute: false), navigate: true);
        }
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Crea tu cuenta')" :description="__('Ingresa los detalles para crear tu cuenta')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input wire:model="name" :label="__('Nombre')" type="text" required autofocus autocomplete="name"
            :placeholder="__('Nombre y apellido')" />

        <!-- Email Address -->
        <flux:input wire:model="email" :label="__('Correo electrónico')" type="email" required autocomplete="email"
            placeholder="email@example.com" />

        <!-- Password -->
        <flux:input wire:model="password" :label="__('Contraseña')" type="password" required autocomplete="new-password"
            :placeholder="__('Tu contraseña')" viewable />

        <!-- Confirm Password -->
        <flux:input wire:model="password_confirmation" :label="__('Confirmar contraseña')" type="password" required
            autocomplete="new-password" :placeholder="__('Confirma tu contraseña')" viewable />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Crear cuenta') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('¿Ya tenes una cuenta? ') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Inicia sesión') }}</flux:link>
    </div>
</div>
