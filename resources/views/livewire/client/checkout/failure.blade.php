<?php

use Livewire\Attributes\{Layout, Title};
use Livewire\Volt\Component;

new #[Layout('components.layouts.blank')] #[Title('Error • Tortuga')] class extends Component {
    public function mount()
    {
        $paymentId = request()->query('payment_id');

        if (!$paymentId) {
            abort(404);
        }
    }
}; ?>

<section class="container mx-auto px-4 mt-6 md:px-6 lg:px-8 my-12">
    <flux:text size="xs">
        <flux:link variant="subtle" href="{{ route('client.checkout') }}" wire:navigate>
            <flux:icon.arrow-left variant="micro" class="mr-1 mb-0.5 inline-block" />
            Volver al checkout
        </flux:link>
    </flux:text>

    <flux:heading class="mt-6" size="xl">Error</flux:heading>
    <flux:text>
        Ocurrió un problema al procesar tu pago. Verificá la información ingresada e intentá nuevamente.
    </flux:text>

    <div class="flex justify-center items-center min-h-[70vh]">
        <flux:icon.exclamation-triangle variant="solid" class="size-48 dark:text-zinc-700 text-zinc-100" />
    </div>
</section>  