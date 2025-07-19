<?php

use Livewire\Attributes\{Layout, Title, Computed, On};
use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Stats;
use App\Models\Order;
use App\Models\User;

new #[Layout('components.layouts.dashboard')] #[Title('Panel • Tortuga')] class extends Component {
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

    #[\Livewire\Attributes\Computed]
    public function stats()
    {
        $revenue = Stats::revenueTrend();
        $orders = Stats::ordersTrend();
        $userTrend = User::weeklyTrend();
        $productTrend = Product::weeklyTrend();

        return [
            [
                'title' => 'Ingresos totales',
                'value' => Stats::totalRevenue(),
                'trend' => $revenue['trend'] . '%',
                'trendUp' => $revenue['trendUp'],
            ],
            [
                'title' => 'Órdenes completadas',
                'value' => Stats::totalOrders(),
                'trend' => $orders['trend'] . '%',
                'trendUp' => $orders['trendUp'],
            ],
            [
                'title' => 'Usuarios activos',
                'value' => User::count(),
                ...User::weeklyTrend(),
            ],
            [
                'title' => 'Productos publicados',
                'value' => Product::count(),
                ...Product::weeklyTrend(),
            ],
        ];
    }

    #[Computed]
    public function orders()
    {
        return Order::latest()->take(5)->get();
    }
}; ?>

<div class="space-y-6">
    <div class="relative w-full">
        <flux:heading size="xl" level="1">{{ __('Panel de control') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">
            {{ __('Visualiza y gestiona la actividad de tu tienda en tiempo real') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <flux:heading size="lg">Estadísticas</flux:heading>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
            @foreach ($this->stats as $stat)
                <div class="relative rounded-lg px-6 py-4 bg-zinc-50 dark:bg-zinc-700">
                    <flux:subheading>{{ $stat['title'] }}</flux:subheading>
                    <flux:heading size="xl" class="mb-2">
                        {{ $stat['title'] === 'Ingresos totales'
                            ? '$' . number_format($stat['value'], 2, ',', '.') . ' UYU'
                            : $stat['value'] }}
                    </flux:heading>
                    <div
                        class="flex items-center gap-1 font-medium text-sm
                {{ $stat['trendUp'] ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                        <flux:icon :icon="$stat['trendUp'] ? 'arrow-trending-up' : 'arrow-trending-down'"
                            variant="micro" />
                        {{ $stat['trend'] }}
                    </div>
                </div>
            @endforeach
        </div>

        <flux:heading size="lg">Últimos pedidos</flux:heading>
        <div
            class="relative h-full px-4 flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <flux:table>
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
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                        inset="top bottom">
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
                                            <flux:menu.item variant="danger" icon="trash">Eliminar pedido
                                            </flux:menu.item>
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
                                No hay pedidos registrados aún.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</div>
