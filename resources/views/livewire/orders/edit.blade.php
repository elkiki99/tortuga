<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
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

    #[On('editOrder')]
    public function openEditOrderModal($id)
    {
        $this->order = Order::findOrFail($id);

        $this->authorize('edit', $this->order);

        $this->buyer_name = $this->order->buyer_name;
        $this->buyer_email = $this->order->buyer_email;
        $this->purchase_id = $this->order->purchase_id;
        $this->purchase_date = Carbon::parse($this->order->purchase_date)->format('Y-m-d');
        $this->total = number_format($this->order->total, 2, ',', '.');
        $this->status = $this->order->status;   
        $this->payment_method = Str::ucfirst($this->order->payment_method);

        $this->modal('edit-order')->show();
    }

    public function closeEditOrderModal()
    {
        $this->order = null;
        $this->buyer_name = '';
        $this->buyer_email = '';
        $this->purchase_id = '';
        $this->purchase_date = '';
        $this->total = '';
        $this->status = '';
        $this->payment_method = '';
    }

    public function updateOrder()
    {
        $this->authorize('edit', $this->order);

        $this->validate([
            'status' => 'required|string|max:255|in:pending,payed,cancelled,delivered',
        ]);

        $this->order->update([
            'status' => $this->status,
        ]);

        $this->modal('edit-order')->close();

        $this->dispatch('orderUpdated');

        Flux::toast(heading: 'Orden actualizada', text: 'El estado de la orden fue actualizado correctamente', variant: 'success');
    }
}; ?>

<form wire:submit.prevent="updateOrder">
    <flux:modal name="edit-order" class="md:w-auto space-y-6">
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
            <flux:select.option value="pending">
                <div class="flex items-center gap-2">
                    <flux:icon.clock variant="mini" class="text-yellow-400" /> Pendiente
                </div>
            </flux:select.option>
            <flux:select.option value="payed">
                <div class="flex items-center gap-2">
                    <flux:icon.currency-dollar variant="mini" class="text-blue-400" /> Pago
                </div>
            </flux:select.option>
            <flux:select.option value="cancelled">
                <div class="flex items-center gap-2">
                    <flux:icon.x-circle variant="mini" class="text-red-400" /> Cancelado
                </div>
            </flux:select.option>
            <flux:select.option value="delivered">
                <div class="flex items-center gap-2">
                    <flux:icon.check-circle variant="mini" class="text-green-400" /> Entregado
                </div>
            </flux:select.option>
        </flux:select>

        <div class="flex gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="ghost">Cancelar</flux:button>
            </flux:modal.close>

            <flux:button variant="primary" type="submit">Actualizar</flux:button>
        </div>  
    </flux:modal>
</form>
