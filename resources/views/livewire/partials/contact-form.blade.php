<?php

use Illuminate\Support\Facades\Mail;
use Livewire\Volt\Component;
use App\Mail\ContactForm;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $subject = '';
    public string $message = '';

    public function sendContactMessage()
    {
        $this->validate([
            'name' => 'required|string|min:2',
            'email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        Mail::to(config('mail.from.address'))->send(new ContactForm($this->name, $this->email, $this->subject, $this->message));

        $this->reset(['name', 'email', 'subject', 'message']);

        Flux::toast(heading: 'Email enviado exitosamente', text: 'Tu mensaje ha sido enviado correctamente, te responderemos a la brevedad', variant: 'success');
    }
}; ?>

<flux:card class="max-w-xl space-y-6">
    <div>
        <flux:heading size="lg" level="2">
            {{ __('Formulario de contacto') }}
        </flux:heading>
        <flux:subheading level="2" size="md" class="text-gray-600">
            {{ __('Completa el formulario a continuación para enviarnos un mensaje.') }}
        </flux:subheading>
    </div>

    <flux:separator variant="subtle" />

    <form wire:submit.prevent="sendContactMessage" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input wire:model="email" :label="__('Correo electrónico')" type="email" required autofocus
            autocomplete="email" placeholder="email@example.com" />

        <!-- Name -->
        <flux:input wire:model="name" :label="__('Nombre')" type="text" required autocomplete="name"
            placeholder="Tu nombre" />

        <!-- Subject -->
        <flux:input wire:model="subject" :label="__('Asunto')" type="text" required placeholder="Asunto" />

        <!-- Message -->
        <flux:textarea wire:model="message" :label="__('Mensaje')" required placeholder="Escribe tu mensaje aquí..."
            rows="5" />

        <!-- Submit button -->
        <flux:button type="submit" variant="primary">
            {{ __('Enviar mensaje') }}
        </flux:button>
    </form>
</flux:card>
