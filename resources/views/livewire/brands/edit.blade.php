<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Brand;

new class extends Component {
    public ?Brand $brand;

    public $name;
    public $description;

    #[On('editBrand')]
    public function openEditBrandModal($id)
    {
        $this->brand = Brand::findOrFail($id);

        $this->authorize('edit', $this->brand);

        $this->name = $this->brand->name;
        $this->description = $this->brand->description;

        $this->modal('edit-brand')->show();
    }

    public function closeEditBrandModal()
    {
        $this->brand = null;
        $this->name = '';
        $this->description = '';
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
            'description' => $this->description,
        ]);

        $this->modal('edit-brand')->close();

        $this->dispatch('brandUpdated');
        Flux::toast(heading: 'Marca actualizada', text: 'La marca fue actualizada exitosamente', variant: 'success');
    }
};
?>

<form wire:submit.prevent="updateBrand">
    <flux:modal name="edit-brand" class="md:w-auto space-y-6">
        <div>
            <flux:heading size="lg">Editar marca</flux:heading>
            <flux:text class="mt-2">Actualizá los datos de la marca</flux:text>
        </div>

        <flux:input placeholder="Nombre de la marca" wire:model="name" label="Nombre" required autofocus />

        <flux:textarea label="Descripción" badge="Opcional" placeholder="Descripción de la marca" wire:model="description"
            rows="3" />

        <div class="flex gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="ghost">Cancelar</flux:button>
            </flux:modal.close>

            <flux:button variant="primary" type="submit">Actualizar</flux:button>
        </div>
    </flux:modal>
</form>
