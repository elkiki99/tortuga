<?php

use Livewire\Volt\Component;
use App\Models\Brand;

new class extends Component {
    public ?Brand $brand;

    public function mount(Brand $brand)
    {
        $this->brand = $brand;
    }

    public function deleteBrand()
    {
        $this->authorize('delete', $this->brand);
        
        $this->brand->delete();

        Flux::toast(variant: 'danger', heading: 'Marca eliminada', text: 'La marca fue eliminada exitosamente');

        // $url = request()->header('Referer');

        // if ($url === url()->route('brands.index')) {
            $this->dispatch('brandDeleted');
        // } else {
        //     $this->redirectRoute('brands.index', navigate: true);
        // }

        Flux::modals()->close();
    }
}; ?>

<form wire:submit.prevent="deleteBrand">
    <flux:modal name="delete-brand-{{ $brand->id }}" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">¿Eliminar marca?</flux:heading>

                <flux:subheading>
                    Esta acción eliminará permanentemente la marca <strong>{{ Str::ucfirst($brand->name) }}</strong>. ¿Deseas continuar?
                </flux:subheading>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit">Eliminar marca</flux:button>
            </div>
        </div>
    </flux:modal>
</form>
