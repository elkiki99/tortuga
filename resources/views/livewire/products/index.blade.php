<?php

use Livewire\Attributes\{Layout, Title, Computed, On};
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Product;

new #[Layout('components.layouts.dashboard')] #[Title('Productos • Tortuga')] class extends Component {
    use WithPagination;

    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $search = '';

    #[On('productCreated')]
    #[On('productUpdated')]
    #[On('productDeleted')]
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
    public function products()
    {
        return Product::query()
            ->search($this->search)
            ->when(
                $this->sortBy === 'price',
                function ($query) {
                    $query->orderByRaw("COALESCE(discount_price, price) {$this->sortDirection}");
                },
                function ($query) {
                    $query->orderBy($this->sortBy, $this->sortDirection);
                },
            )
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
        <flux:modal.trigger name="create-product">
            <flux:button size="sm" variant="primary" icon="plus">
                <span class="hidden sm:inline">Agregar producto</span>
                <span class="inline sm:hidden">Agregar</span>
            </flux:button>
        </flux:modal.trigger>
        <div class="w-full">
            <flux:input wire:model.live="search" size="sm" variant="filled"
                placeholder="Buscar por nombre o categoría..." icon="magnifying-glass" />
        </div>
    </div>

    <flux:table :paginate="$this->products">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                wire:click="sort('name')">Nombre</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'price'" :direction="$sortDirection"
                wire:click="sort('price')">
                Precio</flux:table.column>
            <flux:table.column>Categoría</flux:table.column>
            <flux:table.column class="hidden xl:table-cell">Marca</flux:table.column>
            <flux:table.column class="hidden md:table-cell" sortable :sorted="$sortBy === 'created_at'"
                :direction="$sortDirection" wire:click="sort('created_at')">Creación</flux:table.column>
            <flux:table.column class="hidden lg:table-cell">En stock</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->products as $product)
                <flux:table.row wire:key="{{ $product->id }}">
                    <flux:table.cell variant="strong">
                        <flux:text>
                            <flux:link variant="ghost" wire:navigate
                                href="{{ route('products.show', $product->slug) }}">
                                {{ Str::of($product->name)->ucfirst()->limit(15) }}
                            </flux:link>
                        </flux:text>
                    </flux:table.cell>

                    @php
                        $price = $product->discount_price ?? $product->price;
                    @endphp

                    <flux:table.cell class="whitespace-nowrap">${{ number_format($price, 2, ',', '.') }}&nbsp;UYU
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge size="sm" inset="top bottom">
                            <flux:link variant="subtle" wire:navigate
                                href="{{ route('categories.show', $product->category->slug) }}">
                                {{ Str::ucfirst($product->category->name) }}
                            </flux:link>
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="hidden xl:table-cell">{{ Str::ucfirst($product->brand->name) }}
                    </flux:table.cell>

                    <flux:table.cell class="hidden md:table-cell">{{ $product->created_at->format('d/m/Y') }}
                    </flux:table.cell>

                    <flux:table.cell class="hidden lg:table-cell">
                        @if ($product->in_stock == true)
                            <flux:badge color="green" size="sm" inset="top bottom">
                                Si
                            </flux:badge>
                        @else
                            <flux:badge color="red" size="sm" inset="top bottom">
                                No
                            </flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom">
                            </flux:button>
                            <flux:menu>
                                <flux:menu.item href="{{ route('products.show', $product->slug) }}" wire:navigate
                                    icon-trailing="chevron-right">Ver producto</flux:menu.item>
                                <flux:menu.separator />

                                <flux:modal.trigger name="edit-product-{{ $product->id }}">
                                    <flux:menu.item icon="pencil-square">Editar producto</flux:menu.item>
                                </flux:modal.trigger>

                                <flux:modal.trigger name="delete-product-{{ $product->id }}">
                                    <flux:menu.item variant="danger" icon="trash">Eliminar producto</flux:menu.item>
                                </flux:modal.trigger>
                            </flux:menu>
                        </flux:dropdown>

                        <!-- Update sumary modal -->
                        <livewire:products.edit :$product wire:key="edit-product-{{ $product->id }}" />

                        <!-- Delete product modal -->
                        <livewire:products.delete :$product wire:key="delete-product-{{ $product->id }}" />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center">
                        @if ($this->search != '')
                            No hay productos para la búsqueda "{{ $this->search }}"
                        @else
                            No hay productos
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @if ($this->search != '' && $this->products->isEmpty())
        <div class="flex justify-center mt-6">
            <flux:icon.magnifying-glass variant="solid" class="size-48 dark:text-zinc-700 text-zinc-100" />
        </div>
    @endif

    <livewire:products.create />
</div>
