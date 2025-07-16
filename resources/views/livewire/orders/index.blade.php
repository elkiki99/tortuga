<?php

use Livewire\Attributes\{Layout, Title, Computed, On};
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Order;

new #[Layout('components.layouts.dashboard')] #[Title('Pedidos • Tortuga')] class extends Component {
    use WithPagination;

    public $sortBy = 'purchase_date';
    public $sortDirection = 'desc';
    public $search = '';

    #[On('orderCreated')]
    #[On('orderUpdated')]
    #[On('orderDeleted')]
    public function refreshPage()
    {
        $this->dispatch('$refresh');
    }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function orders()
    {
        return Order::query()->search($this->search)->orderBy($this->sortBy, $this->sortDirection)->paginate(10);
    }
}; ?>

<div class="space-y-6">
    <div class="relative w-full">
        <flux:heading size="xl" level="1">Pedidos</flux:heading>
        <flux:subheading size="lg" class="mb-6">Gestiona los pedidos realizados</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex items-center gap-4">
        <div class="w-full">
            <flux:input wire:model.live="search" size="sm" variant="filled"
                placeholder="Buscar por email o código de compra..." icon="magnifying-glass" />
        </div>
    </div>

    <flux:table :paginate="$this->orders">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'purchase_id'" :direction="$sortDirection"
                wire:click="sort('purchase_id')">Código</flux:table.column>
            <flux:table.column>Comprador</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'total'" :direction="$sortDirection"
                wire:click="sort('total')">Total</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'purchase_date'" :direction="$sortDirection"
                wire:click="sort('purchase_date')">Fecha</flux:table.column>
            <flux:table.column>Estado</flux:table.column>
            <flux:table.column>Método</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->orders as $order)
                <flux:table.row wire:key="order-{{ $order->id }}">
                    <flux:table.cell variant="strong">
                        <flux:text>{{ $order->purchase_id }}</flux:text>
                    </flux:table.cell>

                    <flux:table.cell>{{ $order->buyer_name }}</flux:table.cell>
                    <flux:table.cell>{{ $order->buyer_email }}</flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap">
                        ${{ number_format($order->total, 2, ',', '.') }} UYU
                    </flux:table.cell>

                    <flux:table.cell>{{ \Carbon\Carbon::parse($order->purchase_date)->format('d/m/Y') }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge color="{{ $order->status === 'completed' ? 'green' : 'yellow' }}" size="sm"
                            inset="top bottom">
                            {{ ucfirst($order->status) }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell>{{ ucfirst($order->payment_method) }}</flux:table.cell>

                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom">
                            </flux:button>
                            <flux:menu>
                                <flux:menu.item
                                    href="http://tortuga.test/checkout/exito?payment_id={{ $order->purchase_id }}"
                                    wire:navigate icon-trailing="chevron-right">Ver pedido</flux:menu.item>
                                <flux:menu.separator />

                                <flux:modal.trigger name="delete-order-{{ $order->id }}">
                                    <flux:menu.item variant="danger" icon="trash">Eliminar pedido</flux:menu.item>
                                </flux:modal.trigger>
                            </flux:menu>
                        </flux:dropdown>

                        {{-- <!-- Delete order modal -->
                        <livewire:orders.delete :$order wire:key="delete-order-{{ $order->id }}" /> --}}
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center">
                        @if ($this->search != '')
                            No se encontraron órdenes para "{{ $this->search }}"
                        @else
                            No hay órdenes registradas aún.
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @if ($this->search != '' && $this->orders->isEmpty())
        <div class="flex justify-center mt-6">
            <flux:icon.magnifying-glass variant="solid" class="size-48 dark:text-zinc-700 text-zinc-100" />
        </div>
    @endif
</div>
