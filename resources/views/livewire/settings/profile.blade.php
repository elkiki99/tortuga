<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\{Layout, Title};
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new #[Layout('components.layouts.dashboard')] #[Title('Profile • Tortuga')] class extends Component {
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Flux::toast(variant: 'success', heading: 'Perfil actualizado', text: 'Tu perfil se ha actualizado correctamente');
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        $user->sendEmailVerificationNotification();

        Flux::toast(variant: 'success', heading: 'Enlace de verificación enviado', text: 'Hemos enviado un nuevo enlace de verificación a tu email');
    }
}; ?>

<section class="w-full">
    @include('livewire.partials.settings-heading')

    <x-settings.layout :heading="__('Perfil')" :subheading="__('Actualiza tu nombre y tu email')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Nombre')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Su dirección de correo electrónico no está verificada.') }}

                            <flux:link class="text-sm cursor-pointer"
                                wire:click.prevent="resendVerificationNotification">
                                {{ __('Haga clic aquí para volver a enviar el enlace de verificación.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('Hemos enviado un nuevo enlace de verificación a tu email') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Submit button -->
            <flux:button variant="primary" type="submit">
                {{ __('Guardar') }}
            </flux:button>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
