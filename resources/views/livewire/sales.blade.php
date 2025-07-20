<?php

use Livewire\Attributes\{Layout, Title};
use Livewire\Volt\Component;
use App\Models\Stats;

new #[Layout('components.layouts.dashboard')] #[Title('Ventas • Tortuga')] class extends Component {
    public array $data = [];
    public string $range = 'month';

    public function mount()
    {
        $this->updateData();
    }

    public function updatedRange($value)
    {
        $this->updateData();
    }

    public function updateData()
    {
        $query = Stats::query();

        if ($this->range === 'month') {
            $query->where('date', '>=', now()->subMonth()->toDateString());
        } elseif ($this->range === 'year') {
            $query->where('date', '>=', now()->subYear());
        }

        $this->data = $query
            ->orderBy('date')
            ->get()
            ->map(
                fn($stat) => [
                    'date' => \Carbon\Carbon::parse($stat->date)->format('Y-m-d'),
                    'revenue' => $stat->total_revenue,
                ],
            )
            ->values()
            ->toArray();

        if (count($this->data) === 1) {
            $only = $this->data[0];
            $prevDate = \Carbon\Carbon::parse($only['date'])->subDay()->format('Y-m-d');

            array_unshift($this->data, [
                'date' => $prevDate,
                'revenue' => 0,
            ]);
        }
    }
}; ?>

<div class="space-y-6">
    <div>
        <div class="flex items-center gap-2">
            <div class="relative w-full">
                <flux:heading size="xl" level="1">Ventas</flux:heading>
                <flux:subheading size="lg" class="mb-6">Visualiza el histórico de ventas de tu tienda
                </flux:subheading>
            </div>

            <flux:select wire:model.live="range" variant="listbox" class="max-w-fit" align="end">
                <x-slot name="trigger">
                    <flux:select.button size="sm">
                        <flux:icon.arrows-up-down variant="micro" class="mr-2 text-zinc-400" />
                        <flux:select.selected />
                    </flux:select.button>
                </x-slot>

                <flux:select.option value="month">Último mes</flux:select.option>
                <flux:select.option value="year">Último año</flux:select.option>
                <flux:select.option value="all">Histórico</flux:select.option>
            </flux:select>
        </div>

        <flux:separator variant="subtle" />
    </div>

    @if (count($data) < 2)
        @if ($range != 'all')
            <flux:text>No hay ventas registradas en el último {{ $range == 'month' ? 'mes' : 'año' }}.</flux:text>
        @else
            <flux:text>No hay ventas registradas.</flux:text>
        @endif
    @else
        <flux:chart wire:model="data" class="aspect-1/1 md:aspect-2/1 xl:aspect-3/1">
            <flux:chart.svg>
                <flux:chart.line field="revenue" class="text-emerald-500 dark:text-emerald-400" />
                        <flux:chart.point field="revenue" class="text-emerald-500 dark:text-emerald-400" />

                <flux:chart.axis axis="x" field="date">
                    <flux:chart.axis.line />
                    <flux:chart.axis.tick />
                </flux:chart.axis>
                <flux:chart.axis axis="y">
                    <flux:chart.axis.grid />
                    <flux:chart.axis.tick />
                </flux:chart.axis>
                <flux:chart.cursor />
            </flux:chart.svg>

            <flux:chart.tooltip>
                <flux:chart.tooltip.heading field="date"
                    :format="['year' => 'numeric', 'month' => 'numeric', 'day' => 'numeric']" />
                <flux:chart.tooltip.value
                    :format="['useGrouping' => true, 'minimumFractionDigits' => 2, 'maximumFractionDigits' => 2]"
                    field="revenue" label="Ingresos (UYU)" />
            </flux:chart.tooltip>
        </flux:chart>
    @endif
</div>
