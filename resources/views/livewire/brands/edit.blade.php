<?php

use Livewire\Volt\Component;
use App\Models\Brand;
// use App\Helpers\Slug;

new class extends Component {
    public Brand $brand;

    public $name;
    public $description;

    public function mount(Brand $brand)
    {
        $this->brand = $brand;
        $this->name = $brand->name;
        $this->description = $brand->description;
    }

    public function updateBrand()
    {
        $this->authorize('edit', $this->brand);

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $this->brand->update([
            'name' => $this->name,
            // 'slug' => Slug::generate($this->name, Brand::class),
            'description' => $this->description,
        ]);

        Flux::modals()->close();

        $this->dispatch('brandUpdated');
        Flux::toast(heading: 'Marca actualizada', text: 'La marca fue actualizada exitosamente', variant: 'success');
    }
};
?>

<flux:modal name="edit-brand-{{ $brand->id }}" class="md:w-auto">
    <form wire:submit.prevent="updateBrand" class="space-y-6">
        <div>
            <flux:heading size="lg">Editar marca</flux:heading>
            <flux:text class="mt-2">Actualizá los datos de la marca</flux:text>
        </div>

        <flux:input placeholder="Nombre de la marca" wire:model="name" label="Nombre" required />

        <flux:textarea label="Descripción" badge="Opcional" placeholder="Descripción de la marca"
            wire:model="description" rows="3" />

        <div class="flex justify-end gap-2">
            <flux:button type="button" variant="ghost" x-on:click="$flux.modals().close()">Cancelar</flux:button>
            <flux:button type="submit" variant="primary">Actualizar</flux:button>
        </div>
    </form>
</flux:modal>
