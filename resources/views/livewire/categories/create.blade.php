<?php

use Livewire\Volt\Component;
use App\Models\Category;
use App\Helpers\Slug;

new class extends Component {
    public $name;
    public $slug;
    public $description;
    public $parent_id = null;

    public function createCategory()
    {
        $this->authorize('create', Category::class);

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create([
            'name' => $this->name,
            'slug' => Slug::generate($this->name, Category::class),
            'description' => $this->description,
            'parent_id' => $this->parent_id,
        ]);

        Flux::modals()->close();
        $this->reset(['name', 'description', 'parent_id']);

        $this->dispatch('categoryCreated');
        Flux::toast(heading: 'Categoría creada', text: 'La categoría fue creada exitosamente', variant: 'success');
    }
}; ?>

<flux:modal name="create-category" class="md:w-auto">
    <form wire:submit.prevent="createCategory" class="space-y-6">
        <div>
            <flux:heading size="lg">Nueva categoría</flux:heading>
            <flux:text class="mt-2">Agrega una nueva categoría a tu tienda</flux:text>
        </div>

        <flux:input placeholder="Nombre de la categoría" wire:model="name" label="Nombre" autofocus required />

        <flux:textarea label="Descripción" badge="Opcional" placeholder="Descripción de la categoría"
            wire:model="description" rows="3" />

        <flux:select wire:model="parent_id" label="Categoría padre" badge="Opcional" variant="listbox" searchable>
            <flux:select.option selected value="">Categoría principal</flux:select.option>

            @forelse(Category::whereNull('parent_id')->orderBy('name')->get() as $cat)
                <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
            @empty
                <flux:select.option disabled>No hay categorías principales</flux:select.option>
            @endforelse
        </flux:select>

        <div class="flex justify-end gap-2">
            <flux:button type="button" variant="ghost" x-on:click="$flux.modals().close()">Cancelar</flux:button>
            <flux:button type="submit" variant="primary">Crear</flux:button>
        </div>
    </form>
</flux:modal>
