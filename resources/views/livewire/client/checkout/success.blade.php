<?php

use Livewire\Attributes\{Layout, Title};
use Livewire\Volt\Component;

new #[Layout('components.layouts.blank')] #[Title('Éxito • Tortuga')] class extends Component {
    public function mount()
    {
        // $paymentId = request()->query('payment_id');

        // if (!$paymentId) {
        //     abort(404);
        // }
    }
}; ?>

<section class="container mx-auto px-4 mt-6 md:px-6 lg:px-8 my-12">
    <flux:text size="xs">
        <flux:link variant="subtle" href="{{ route('home') }}" wire:navigate>
            <flux:icon.arrow-left variant="micro" class="mr-1 mb-0.5 inline-block" />
            Volver al inicio
        </flux:link>
    </flux:text>

    <flux:heading class="mt-6" size="xl">Éxito</flux:heading>
    <flux:text>Tu pago con ID <strong>{{ request()->query('payment_id') }}</strong> fue procesado correctamente.
    </flux:text>

    <div class="flex justify-center items-center min-h-[70vh]">
        <flux:icon.rocket-launch variant="solid" class="size-48 dark:text-zinc-700 text-zinc-100" />
    </div>
</section>
