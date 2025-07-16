<?php

use Livewire\Attributes\{Layout, Title, Computed};
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Order;

new #[Layout('components.layouts.dashboard')] #[Title('Apariencia • Tortuga')] class extends Component {
    use WithPagination;

    #[Computed]
    public function orders()
    {
        return Auth::user()->orders()->paginate(12);
    }
}; ?>

<section class="w-full">
    @include('livewire.partials.settings-heading')

    <x-settings.layout :heading="__('Mis pedidos')" :subheading="__('Listado de tus pedidos recientes')">
        <flux:table :paginate="$this->orders">
            <flux:table.columns>
                <flux:table.column>Código</flux:table.column>
                <flux:table.column>Total</flux:table.column>
                <flux:table.column>Fecha</flux:table.column>
                <flux:table.column>Estado</flux:table.column>
                <flux:table.column>Método</flux:table.column>
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

                        <flux:table.cell class="whitespace-nowrap">
                            ${{ number_format($order->total, 2, ',', '.') }}&nbsp;UYU
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
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center">
                            No hay órdenes registradas aún.
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </x-settings.layout>
</section>
