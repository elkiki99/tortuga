<?php

use Livewire\Attributes\{Layout, Title, Computed};
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Product;

new #[Layout('components.layouts.dashboard')] #[Title('Productos • Tortuga')] class extends Component {
    use WithPagination;

    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

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
    public function products()
    {
        return Product::query()
            ->when($this->sortBy === 'price', function ($query) {
                $query->orderByRaw("COALESCE(discount_price, price) {$this->sortDirection}");
            }, function ($query) {
                $query->orderBy($this->sortBy, $this->sortDirection);
            })
            ->paginate(12);
    }
}; ?>

<div class="space-y-6">
    <div class="relative w-full">
        <flux:heading size="xl" level="1">{{ __('Productos') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Administra los productos de tu tienda') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex items-center gap-4">
        <flux:button size="sm" variant="primary" icon="plus">Nuevo producto</flux:button>

        <div class="w-full">
            <flux:input size="sm" variant="filled" placeholder="Buscar..." icon="magnifying-glass" />
        </div>

        <flux:tabs variant="segmented" class="w-auto! ml-2" size="sm">
            <flux:tab icon="list-bullet" icon:variant="outline" />
            <flux:tab icon="squares-2x2" icon:variant="outline" />
        </flux:tabs>
    </div>

    <flux:table :paginate="$this->products">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                wire:click="sort('name')">Nombre</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'price'" :direction="$sortDirection"
                wire:click="sort('price')">Precio</flux:table.column>
            <flux:table.column>Categoría</flux:table.column>
            <flux:table.column>Marca</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                wire:click="sort('created_at')">Creación</flux:table.column>
            <flux:table.column>En stock</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->products as $product)
                <flux:table.row :key="$product->id">
                    <flux:table.cell variant="strong" class="flex items-center gap-3">
                        <flux:avatar size="xs" src="{{ $product->featuredImage }}" />
                        {{ Str::ucfirst($product->name) }}
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">${{ $product->discount_price ?? $product->price }}UYU
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" inset="top bottom">
                            {{ Str::ucfirst($product->category->name) }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ Str::ucfirst($product->brand->name) }}</flux:table.cell>
                    <flux:table.cell>{{ $product->created_at->format('d/m/Y') }}</flux:table.cell>
                    <flux:table.cell>
                        @if ($product->in_stock == true)
                            <flux:badge size="sm" color="green" inset="top bottom">Si</flux:badge>
                        @else
                            <flux:badge size="sm" color="red" inset="top bottom">No</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom">
                        </flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
