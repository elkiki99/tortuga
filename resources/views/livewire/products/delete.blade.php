<?php

use Livewire\Volt\Component;
use App\Models\Product;

new class extends Component {
    public ?Product $product;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    public function deleteProduct()
    {
        $this->product->delete();

        Flux::toast(variant: 'danger', heading: 'Producto eliminado', text: 'El producto fue eliminado exitosamente');

        $url = request()->header('Referer');

        if ($url === url()->route('products.index')) {
            $this->dispatch('productDeleted');
        } else {
            $this->redirectRoute('products.index', navigate: true);
        }
        Flux::modals()->close();
    }
}; ?>

<form wire:submit.prevent="deleteProduct">
    <flux:modal name="delete-product-{{ $product->id }}" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">¿Eliminar producto?</flux:heading>

                <flux:subheading>
                    Esta acción eliminará permanentemente el producto <strong>{{ Str::ucfirst($product->name) }}</strong>. ¿Deseas
                    continuar?
                </flux:subheading>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Cancelar</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit">Eliminar producto</flux:button>
            </div>
        </div>
    </flux:modal>
</form>
