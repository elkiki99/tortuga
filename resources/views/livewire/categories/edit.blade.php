<?php

use Livewire\Volt\Component;
use App\Models\Category;

new class extends Component {
    public Category $category;

    public $name;
    public $description;
    public $parent_id = null;

    public function mount(Category $category)
    {
        $this->category = $category;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->parent_id = $category->parent_id;
    }

    public function updateCategory()
    {
        $this->authorize('edit', $this->category);

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $this->category->update([
            'name' => $this->name,
            'slug' => \Str::slug($this->name),
            'description' => $this->description,
            'parent_id' => $this->parent_id ?: null,
        ]);

        Flux::modals()->close();
        $this->dispatch('categoryUpdated');

        Flux::toast(heading: 'Categoría actualizada', text: 'La categoría fue actualizada exitosamente', variant: 'success');
    }
}; ?>

<flux:modal name="edit-category-{{ $category->id }}" class="md:w-auto">
    <form wire:submit.prevent="updateCategory" class="space-y-6">
        <div>
            <flux:heading size="lg">Editar categoría</flux:heading>
            <flux:text class="mt-2">Actualizá los datos de la categoría</flux:text>
        </div>

        <flux:input placeholder="Nombre de la categoría" wire:model="name" label="Nombre" required />

        <flux:textarea label="Descripción" badge="Opcional" placeholder="Descripción de la categoría"
            wire:model="description" rows="3" />

        @if ($category->parent_id)
            <flux:badge color="yellow" size="sm">Subcategoría de {{ $category->parent->name }}</flux:badge>
        @else
            <flux:badge color="green" size="sm">Categoría principal</flux:badge>
        @endif

        <div class="flex justify-end gap-2">
            <flux:button type="button" variant="ghost" x-on:click="$flux.modals().close()">Cancelar</flux:button>
            <flux:button type="submit" variant="primary">Actualizar</flux:button>
        </div>
    </form>
</flux:modal>
