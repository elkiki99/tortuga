<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Brand;

new class extends Component {
    public ?Brand $brand;

    #[On('deleteBrand')]
    public function openDeleteBrandModal($id)
    {
        $this->brand = Brand::findOrFail($id);

        $this->authorize('delete', $this->brand);

        $this->modal('delete-brand')->show();
    }

    public function deleteBrand()
    {
        $this->authorize('delete', $this->brand);

        $this->brand->delete();

        Flux::toast(variant: 'danger', heading: 'Marca eliminada', text: 'La marca fue eliminada exitosamente');

        $this->dispatch('brandDeleted');

        $this->modal('delete-brand')->close();
    }
}; ?>

<form wire:submit.prevent="deleteBrand">
    <flux:modal name="delete-brand" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">¿Eliminar marca?</flux:heading>

            <flux:subheading>
            Esta acción eliminará permanentemente la marca <strong>{{ Str::ucfirst($brand?->name) }}</strong>.
                ¿Deseas continuar?
            </flux:subheading>
        </div>

        <div class="flex gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="ghost">Cancelar</flux:button>
            </flux:modal.close>

            <flux:button variant="danger" type="submit">Eliminar marca</flux:button>
        </div>
    </flux:modal>
</form>
