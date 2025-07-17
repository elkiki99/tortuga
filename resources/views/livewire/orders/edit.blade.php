<?php

use Livewire\Volt\Component;
use App\Models\Order;
use Carbon\Carbon;

new class extends Component {
    public Order $order;

    public $buyer_name;
    public $buyer_email;
    public $purchase_id;
    public $purchase_date;
    public $total;
    public $status;
    public $payment_method;

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->buyer_name = $order->buyer_name;
        $this->buyer_email = $order->buyer_email;
        $this->purchase_id = $order->purchase_id;
        $this->purchase_date = Carbon::parse($order->purchase_date)->format('Y-m-d');
        $this->total = number_format($order->total, 2, ',', '.');
        $this->status = $order->status;
        $this->payment_method = Str::ucfirst($order->payment_method);
    }

    public function updateOrder()
    {
        $this->validate([
            'status' => 'required|string|max:255|in:pending,payed,cancelled,delivered',
        ]);

        $this->order->update([
            'status' => $this->status,
        ]);

        Flux::modals()->close();
        $this->dispatch('orderUpdated');

        Flux::toast(heading: 'Orden actualizada', text: 'El estado de la orden fue actualizado correctamente', variant: 'success');
    }
}; ?>

<flux:modal name="edit-order-{{ $order->id }}" class="md:w-auto">
    <form wire:submit.prevent="updateOrder" class="space-y-6">
        <div>
            <flux:heading size="lg">Editar orden</flux:heading>
            <flux:text class="mt-2">Solo puedes modificar el estado de la orden</flux:text>
        </div>

        <div class="flex items-center gap-4">
            <div class="w-full">
                <flux:input label="Comprador" wire:model="buyer_name" disabled />
            </div>
            <div class="w-full">
                <flux:input label="Email" wire:model="buyer_email" type="email" disabled />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="w-full">
                <flux:input label="ID de compra" wire:model="purchase_id" disabled />
            </div>
            <div class="w-full">
                <flux:input label="Fecha de compra" wire:model="purchase_date" type="date" disabled />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="w-full">
                <flux:input label="Total" wire:model="total" disabled />
            </div>
            <div class="w-full">
                <flux:input label="Método de pago" wire:model="payment_method" disabled />
            </div>
        </div>

        <flux:select variant="listbox" searchable placeholder="Selecciona un estado" label="Estado" wire:model="status">
            <flux:select.option value="pending">Pendiente</flux:select.option>
            <flux:select.option value="payed">Pago</flux:select.option>
            <flux:select.option value="cancelled">Cancelado</flux:select.option>
            <flux:select.option value="delivered">Entregado</flux:select.option>
        </flux:select>

        <div class="flex justify-end gap-2">
            <flux:button type="button" variant="ghost" x-on:click="$flux.modals().close()">Cancelar</flux:button>
            <flux:button type="submit" variant="primary">Actualizar</flux:button>
        </div>
    </form>
</flux:modal>
