<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Category;

new class extends Component {
    public ?Category $category;

    #[On('deleteCategory')]
    public function openDeleteCategoryModal($id)
    {
        $this->category = Category::findOrFail($id);

        $this->authorize('delete', $this->category);

        $this->modal('delete-category')->show();
    }

    public function deleteCategory()
    {
        $this->authorize('delete', $this->category);

        if ($this->category->parent_id === null && $this->category->children()->exists()) {
            Flux::toast(variant: 'warning', heading: 'Error al eliminar categoría', text: 'No puedes eliminar esta categoría ya que tiene subcategorías existentes');
        } else {
            $uncategorized = \App\Models\Category::firstOrCreate(['slug' => 'sin-categoria'], ['name' => 'Sin categoría', 'description' => 'Productos sin categoría']);

            $this->category->products()->update(['category_id' => $uncategorized->id]);

            $this->category->delete();

            Flux::toast(variant: 'danger', heading: 'Categoría eliminada', text: 'La categoría fue eliminada exitosamente');

            $url = request()->header('Referer');

            if ($url === url()->route('categories.index')) {
                $this->dispatch('categoryDeleted');
            } else {
                $this->redirectRoute('categories.index', navigate: true);
            }

            $this->modal('delete-category')->close();
        }
    }
}; ?>

<form wire:submit.prevent="deleteCategory">
    <flux:modal name="delete-category" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">¿Eliminar categoría?</flux:heading>

                <flux:subheading>
                    Esta acción eliminará permanentemente la categoría
                    <strong>{{ Str::ucfirst($category?->name) }}</strong>. ¿Deseas
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
        </div>
    </flux:modal>
</form>
