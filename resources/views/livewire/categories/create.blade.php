<?php

use Livewire\Volt\Component;
use App\Models\Category;

new class extends Component {
    public $name;
    public $slug;
    public $description;
    public $parent_id = null;

    public function createCategory()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create([
            'name' => $this->name,
            'slug' => \Str::slug($this->name),
            'description' => $this->description,
            'parent_id' => $this->parent_id,
        ]);

        $this->reset(['name', 'description', 'parent_id']);

        Flux::toast(heading: 'Categoría creada', text: 'La categoría fue creada exitosamente', variant: 'success');
        $this->dispatch('categoryCreated');
        Flux::modals()->close();
    }
}; ?>

<flux:modal name="create-category">
    <form wire:submit.prevent="createCategory" class="space-y-4">
        <flux:heading size="lg">Nueva categoría</flux:heading>

        <flux:input wire:model="name" label="Nombre" required />

        <flux:textarea wire:model="description" label="Descripción" />

        <flux:select wire:model="parent_id" label="Categoría padre (opcional)" placeholder="Ninguna">
            <flux:select.option value="">-- Sin padre (categoría principal) --</flux:select.option>

            @foreach (\App\Models\Category::whereNull('parent_id')->orderBy('name')->get() as $cat)
                <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
            @endforeach
        </flux:select>

        <div class="flex justify-end gap-2">
            <flux:button type="button" variant="ghost" x-on:click="$flux.modals().close()">Cancelar</flux:button>
            <flux:button type="submit" variant="primary">Crear</flux:button>
        </div>
    </form>
</flux:modal>
