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
        <flux:subheading size="lg" class="mb-6">{{ __('Administra los categorías de tu tienda') }}
        </flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    <div class="flex items-center gap-4">
        <flux:modal.trigger name="create-category">
            <flux:button size="sm" variant="primary" icon="plus">Agregar categoría</flux:button>
        </flux:modal.trigger>

        <div class="w-full">
            <flux:input wire:model.live="search" size="sm" variant="filled" placeholder="Buscar..."
                icon="magnifying-glass" />
        </div>

        <flux:tabs variant="segmented" class="w-auto! ml-2" size="sm">
            <flux:tab icon="list-bullet" icon:variant="outline" />
            <flux:tab icon="squares-2x2" icon:variant="outline" />
        </flux:tabs>
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

                    <flux:table.cell>{{ Str::of($category->description)->ucfirst()->limit(50) }}</flux:table.cell>

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
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom">
                            </flux:button>
                            <flux:menu>
                                <flux:menu.item href="{{ route('categories.show', $category->slug) }}" wire:navigate icon-trailing="chevron-right">Ver categoría</flux:menu.item>
                                <flux:menu.separator />

                                <flux:modal.trigger name="edit-category-{{ $category->id }}">
                                    <flux:menu.item icon="pencil-square">Editar categoría</flux:menu.item>
                                </flux:modal.trigger>

                                <flux:modal.trigger name="delete-category-{{ $category->id }}">
                                    <flux:menu.item variant="danger" icon="trash">Eliminar categoría</flux:menu.item>
                                </flux:modal.trigger>
                            </flux:menu>
                        </flux:dropdown>

                        <!-- Update sumary modal -->
                        {{-- <livewire:categories.edit :$category wire:key="edit-category-{{ $category->id }}" />

                        <!-- Delete category modal -->
                        <livewire:categories.delete :$category wire:key="delete-category-{{ $category->id }}" /> --}}
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center">
                        @if ($search != '')
                            No hay categorías para la búsqueda "{{ $search }}"
                        @else
                            No hay categorías
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <livewire:categories.create />
</div>
