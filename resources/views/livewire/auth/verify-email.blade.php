<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            if (Auth::user()->isAdmin()) {
                $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
            } else {
                $this->redirectIntended(default: route('home', absolute: false), navigate: true);
            }
            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Flux::toast(variant: 'success', heading: 'Enlace de verificación enviado', text: 'Hemos enviado un nuevo enlace de verificación a tu email');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="mt-4 flex flex-col gap-6">
    <flux:text class="text-center">
        {{ __('Verifique su email haciendo clic en el enlace que le enviamos por correo electrónico.') }}
    </flux:text>

    <div class="flex flex-col items-center justify-between space-y-3">
        <flux:button wire:click="sendVerification" variant="primary" class="w-full">
            {{ __('Reenviar enlace de verificación') }}
        </flux:button>

        <flux:link class="text-sm cursor-pointer" wire:click="logout">
            {{ __('Cerrar sesión') }}
        </flux:link>
    </div>
</div>
