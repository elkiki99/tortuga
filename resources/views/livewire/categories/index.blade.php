<?php

use Livewire\Attributes\{Layout, Title, Computed, On};
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Category;

new #[Layout('components.layouts.dashboard')] #[Title('Categorías • Tortuga')] class extends Component {
    use WithPagination;

    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $search = '';

    public function mount()
    {
        $this->authorize('viewAny', Category::class);
    }

    #[On('categoryCreated')]
    #[On('categoryUpdated')]
    #[On('categoryDeleted')]
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
    public function categories()
    {
        return Category::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);
    }
}; ?>

<div class="space-y-6">
    <div class="relative w-full">
        <flux:heading size="xl" level="1">{{ __('Categorías') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Administra las categorías de tu tienda') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex items-center gap-4">
        <flux:modal.trigger name="create-category">
            <flux:button size="sm" variant="primary" icon="plus">
                <span class="hidden sm:inline">Agregar categoría</span>
                <span class="inline sm:hidden">Agregar</span>
            </flux:button>
        </flux:modal.trigger>

        <div class="w-full">
            <flux:input wire:model.live="search" size="sm" variant="filled" placeholder="Buscar por nombre..."
                icon="magnifying-glass" />
        </div>
    </div>

    <flux:table :paginate="$this->categories">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                wire:click="sort('name')">
                Nombre
            </flux:table.column>

            <flux:table.column>Descripción</flux:table.column>
            <flux:table.column>Tipo</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($this->categories as $category)
                <flux:table.row wire:key="{{ $category->id }}">
                    <flux:table.cell variant="strong">
                        <flux:text>
                            <flux:link variant="ghost" wire:navigate
                                href="{{ route('categories.show', $category->slug) }}">
                                {{ Str::ucfirst($category->name) }}
                            </flux:link>
                        </flux:text>
                    </flux:table.cell>

                    <flux:table.cell>
                        <span class="inline sm:hidden">
                            {{ $category->description ? Str::of($category->description)->ucfirst()->limit(15) : 'Sin descripción' }}
                        </span>
                        <span class="hidden sm:inline md:hidden">
                            {{ $category->description ? Str::of($category->description)->ucfirst()->limit(40) : 'Sin descripción' }}
                        </span>
                        <span class="hidden md:inline xl:hidden">
                            {{ $category->description ? Str::of($category->description)->ucfirst()->limit(50) : 'Sin descripción' }}
                        </span>
                        <span class="hidden xl:inline">
                            {{ $category->description ? Str::of($category->description)->ucfirst()->limit(80) : 'Sin descripción' }}
                        </span>
                    </flux:table.cell>

                    <flux:table.cell>
                        @if ($category->parent_id === null)
                            <flux:badge color="green" size="sm" inset="top bottom">
                                Principal
                            </flux:badge>
                        @else
                            <flux:badge color="yellow" size="sm" inset="top bottom">
                                Secundaria
                            </flux:badge>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="flex justify-start w-full">
                            <flux:dropdown class="ml-auto mr-3">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"
                                    inset="top bottom">
                                </flux:button>
                                <flux:menu>
                                    <flux:menu.item href="{{ route('categories.show', $category->slug) }}" wire:navigate
                                        icon-trailing="chevron-right">Ver categoría</flux:menu.item>
                                    <flux:menu.separator />

                                    <flux:modal.trigger name="edit-category-{{ $category->id }}">
                                        <flux:menu.item icon="pencil-square">Editar categoría</flux:menu.item>
                                    </flux:modal.trigger>

                                    <flux:modal.trigger name="delete-category-{{ $category->id }}">
                                        <flux:menu.item variant="danger" icon="trash">Eliminar categoría
                                        </flux:menu.item>
                                    </flux:modal.trigger>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                        
                        <!-- Update sumary modal -->
                        <livewire:categories.edit :$category wire:key="edit-category-{{ $category->id }}" />

                        <!-- Delete category modal -->
                        <livewire:categories.delete :$category wire:key="delete-category-{{ $category->id }}" />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="3" class="text-center">
                        @if ($this->search != '')
                            No hay categorías para la búsqueda "{{ $this->search }}"
                        @else
                            No hay categorías
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @if ($this->search != '' && $this->categories->isEmpty())
        <div class="flex justify-center mt-6">
            <flux:icon.magnifying-glass variant="solid" class="size-48 dark:text-zinc-700 text-zinc-100" />
        </div>
    @endif

    <livewire:categories.create />
</div>
