<?php

use Livewire\Volt\Component;
use App\Models\Order;

new class extends Component {
    public ?Order $order;

    public function mount(Order $order)
    {
        $this->order = $order;
    }

    public function deleteOrder()
    {
        $this->order->delete();

        Flux::toast(variant: 'danger', heading: 'Pedido eliminado', text: 'El pedido fue eliminado exitosamente');

        $url = request()->header('Referer');

        if ($url === url()->route('orders.index')) {
            $this->dispatch('orderDeleted');
        } else {
            $this->redirectRoute('orders.index', navigate: true);
        }

        Flux::modals()->close();
    }
}; ?>

<form wire:submit.prevent="deleteOrder">
    <flux:modal name="delete-order-{{ $order->id }}" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">¿Eliminar pedido?</flux:heading>

                <flux:subheading>
                    Esta acción eliminará permanentemente el pedido con el
                    código<strong>{{ Str::ucfirst($order->purchase_id) }}</strong>. Asegurate de haber entregado este
                    pedido antes de eliminarlo. ¿Deseas continuar?
                </flux:subheading>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit">Eliminar pedido</flux:button>
            </div>
        </div>
    </flux:modal>
</form>
