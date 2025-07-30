<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Product;

new class extends Component {
    public ?Product $product;

    #[On('deleteProduct')]
    public function openDeleteProductModal($id)
    {
        $this->product = Product::findOrFail($id);
        
        $this->authorize('delete', $this->product);

        $this->modal('delete-product')->show();
    }

    public function deleteProduct()
    {
        $this->authorize('delete', $this->product);

        $this->product->delete();

        Flux::toast(variant: 'danger', heading: 'Producto eliminado', text: 'El producto fue eliminado exitosamente');

        $url = request()->header('Referer');

        if ($url === url()->route('products.index')) {
            $this->dispatch('productDeleted');
        } else {
            $this->redirectRoute('products.index', navigate: true);
        }

        $this->modal('delete-product')->close();
    }
}; ?>

<form wire:submit.prevent="deleteProduct">
    <flux:modal name="delete-product" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">¿Eliminar producto?</flux:heading>

            <flux:subheading>
                Esta acción eliminará permanentemente el producto <strong>{{ Str::ucfirst($product?->name) }}</strong>.
                ¿Deseas
                continuar?
            </flux:subheading>
        </div>

        <div class="flex gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="ghost">Cancelar</flux:button>
            </flux:modal.close>

            <flux:button variant="danger" type="submit">Eliminar</flux:button>
        </div>
    </flux:modal>
</form>
