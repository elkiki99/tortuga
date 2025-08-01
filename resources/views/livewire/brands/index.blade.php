<?php

use Livewire\Attributes\{Layout, Title, Computed, On};
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Brand;

new #[Layout('components.layouts.dashboard')] #[Title('Marcas • Tortuga')] class extends Component {
    use WithPagination;

    public $sortBy = 'name';
    public $sortDirection = 'asc';

    #[Url]
    public $search = '';

    public function mount()
    {
        $this->authorize('viewAny', Brand::class);
    }

    #[On('brandCreated')]
    #[On('brandUpdated')]
    #[On('brandDeleted')]
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
    public function brands()
    {
        return Brand::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);
    }
}; ?>

<div class="space-y-6">
    <div class="relative w-full">
        <flux:heading size="xl" level="1">{{ __('Marcas') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Administra las marcas de tu tienda') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex items-center gap-4">
        <flux:modal.trigger name="create-brand">
            <flux:button size="sm" variant="primary" icon="plus">
                <span class="hidden sm:inline">Agregar marca</span>
                <span class="inline sm:hidden">Agregar</span>
            </flux:button>
        </flux:modal.trigger>

        <div class="w-full">
            <flux:input wire:model.live="search" size="sm" variant="filled" placeholder="Buscar por nombre..."
                icon="magnifying-glass" />
        </div>
    </div>

    <flux:table :paginate="$this->brands">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                wire:click="sort('name')">
                Nombre
            </flux:table.column>

            <flux:table.column>Descripción</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($this->brands as $brand)
                <flux:table.row wire:key="{{ $brand->id }}">
                    <flux:table.cell variant="strong">
                        {{ Str::ucfirst($brand->name) }}
                    </flux:table.cell>

                    <flux:table.cell>
                        <span class="inline sm:hidden">
                            {{ $brand->description ? Str::of($brand->description)->ucfirst()->limit(15) : 'Sin descripción' }}
                        </span>
                        <span class="hidden sm:inline md:hidden">
                            {{ $brand->description ? Str::of($brand->description)->ucfirst()->limit(40) : 'Sin descripción' }}
                        </span>
                        <span class="hidden md:inline xl:hidden">
                            {{ $brand->description ? Str::of($brand->description)->ucfirst()->limit(50) : 'Sin descripción' }}
                        </span>
                        <span class="hidden xl:inline">
                            {{ $brand->description ? Str::of($brand->description)->ucfirst()->limit(80) : 'Sin descripción' }}
                        </span>
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="flex justify-start w-full">
                            <flux:dropdown class="ml-auto mr-3">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                    inset="top bottom">
                                </flux:button>
                                <flux:menu>
                                    <flux:menu.item icon="pencil-square"
                                        wire:click="$dispatchTo('brands.edit', 'editBrand', { id: {{ $brand->id }} })">
                                        Editar marca</flux:menu.item>

                                    <flux:menu.item variant="danger" icon="trash"
                                        wire:click="$dispatchTo('brands.delete', 'deleteBrand', { id: {{ $brand->id }} })">
                                        Eliminar marca</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="2" class="text-center">
                        @if ($this->search != '')
                            No hay marcas para la búsqueda "{{ $this->search }}"
                        @else
                            No hay marcas
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @if ($this->search != '' && $this->brands->isEmpty())
        <div class="flex justify-center mt-6">
            <flux:icon.magnifying-glass variant="solid" class="size-48 dark:text-zinc-700 text-zinc-100" />
        </div>
    @endif

    <!-- Create brand modal -->
    <livewire:brands.create />

    <!-- Update brand modal -->
    <livewire:brands.edit />

    <!-- Delete brand modal -->
    <livewire:brands.delete />
</div>
