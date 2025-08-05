<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $subject = '';
    public string $message = '';

    public function submit()
    {
        $this->validate([
            'name' => 'required|string|min:2',
            'email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        Mail::to('contacto@tusitio.com')->send(new ContactFormMail($this->name, $this->email, $this->subject, $this->message));

        $this->reset(['name', 'email', 'subject', 'message']);
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

    <form wire:submit="submit" class="flex flex-col gap-6">
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
