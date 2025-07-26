<?php

use Livewire\Volt\Component;
use App\Models\Brand;
// use App\Helpers\Slug;

new class extends Component {
    public $name;
    public $slug;
    public $description;
    // public $logo_path;

    public function createBrand()
    {
        $this->authorize('create', Brand::class);

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        Brand::create([
            'name' => $this->name,
            // 'slug' => Slug::generate($this->name, Brand::class),
            'description' => $this->description,
        ]);

        Flux::modals()->close();
        $this->reset(['name', 'description']);

        $this->dispatch('brandCreated');
        Flux::toast(heading: 'Marca creada', text: 'La marca fue creada exitosamente', variant: 'success');
    }
}; ?>

<flux:modal name="create-brand" class="md:w-auto">
    <form wire:submit.prevent="createBrand" class="space-y-6">
        <div>
            <flux:heading size="lg">Nueva marca</flux:heading>
            <flux:text class="mt-2">Agrega una nueva marca a tu tienda</flux:text>
        </div>

        <flux:input placeholder="Nombre de la marca" wire:model="name" label="Nombre" autofocus required />

        <flux:textarea label="Descripción" badge="Opcional" placeholder="Descripción de la marca"
            wire:model="description" rows="3" />

        <div class="flex justify-end gap-2">
            <flux:button type="button" variant="ghost" x-on:click="$flux.modals().close()">Cancelar</flux:button>
            <flux:button type="submit" variant="primary">Crear</flux:button>
        </div>
    </form>
</flux:modal>
