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

    #[On('orderUpdated')]
    #[On('orderDeleted')]
    public function refreshPage()
    {
        $this->dispatch('$refresh');
    }

    public function mount()
    {
        $this->authorize('viewAny', Order::class);
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
        return Order::query()->search($this->search)->orderBy($this->sortBy, $this->sortDirection)->paginate(12);
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
            <flux:table.column class="hidden xl:table-cell">Comprador</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'total'" :direction="$sortDirection"
                wire:click="sort('total')" class="hidden md:table-cell">Total</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'purchase_date'" :direction="$sortDirection"
                wire:click="sort('purchase_date')" class="hidden sm:table-cell">Fecha</flux:table.column>
            <flux:table.column>Estado</flux:table.column>
            <flux:table.column class="hidden xl:table-cell">Método</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->orders as $order)
                <flux:table.row wire:key="order-{{ $order->id }}">
                    <flux:table.cell variant="strong">
                        <flux:text>
                            <flux:link variant="ghost" wire:navigate
                                href="http://tortuga.test/checkout/exito?payment_id={{ $order->purchase_id }}">
                                {{ $order->purchase_id }}
                            </flux:link>
                        </flux:text>
                    </flux:table.cell>

                    <flux:table.cell class="hidden xl:table-cell">{{ $order->buyer_name }}</flux:table.cell>

                    <flux:table.cell>{{ $order->buyer_email }}</flux:table.cell>

                    <flux:table.cell class="whitespace-nowrap hidden md:table-cell">
                        ${{ number_format($order->total, 2, ',', '.') }}&nbsp;UYU
                    </flux:table.cell>

                    <flux:table.cell class="hidden sm:table-cell">
                        {{ \Carbon\Carbon::parse($order->purchase_date)->format('d/m/Y') }}
                    </flux:table.cell>

                    @php
                        $statusMap = [
                            'pending' => [
                                'color' => 'yellow',
                                'icon' => 'clock',
                                'label' => 'Pendiente',
                            ],
                            'payed' => [
                                'color' => 'blue',
                                'icon' => 'currency-dollar',
                                'label' => 'Pago',
                            ],
                            'cancelled' => [
                                'color' => 'red',
                                'icon' => 'x-circle',
                                'label' => 'Cancelado',
                            ],
                            'delivered' => [
                                'color' => 'green',
                                'icon' => 'check-circle',
                                'label' => 'Entregado',
                            ],
                        ];

                        $status = $order->status;
                        $config = $statusMap[$status] ?? $statusMap['pending'];
                    @endphp

                    <flux:table.cell>
                        <flux:badge variant="pill" color="{{ $config['color'] }}" icon="{{ $config['icon'] }}"
                            size="sm" inset="top bottom">
                                <span class="hidden sm:inline">{{ $config['label'] }}</span>
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="hidden xl:table-cell">{{ ucfirst($order->payment_method) }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom">
                            </flux:button>
                            <flux:menu>
                                <flux:menu.item
                                    href="http://tortuga.test/checkout/exito?payment_id={{ $order->purchase_id }}"
                                    wire:navigate icon-trailing="chevron-right">Ver pedido</flux:menu.item>
                                <flux:menu.separator />

                                <flux:modal.trigger name="edit-order-{{ $order->id }}">
                                    <flux:menu.item icon="pencil-square">Editar pedido</flux:menu.item>
                                </flux:modal.trigger>

                                <flux:modal.trigger name="delete-order-{{ $order->id }}">
                                    <flux:menu.item variant="danger" icon="trash">Eliminar pedido</flux:menu.item>
                                </flux:modal.trigger>
                            </flux:menu>
                        </flux:dropdown>

                        <!-- Update sumary modal -->
                        <livewire:orders.edit :$order wire:key="edit-order-{{ $order->id }}" />

                        <!-- Delete order modal -->
                        <livewire:orders.delete :$order wire:key="delete-order-{{ $order->id }}" />
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
