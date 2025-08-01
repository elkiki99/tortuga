<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Category;
use App\Helpers\Slug;

new class extends Component {
    public ?Category $category;

    public $name;
    public $description;
    public $parent_id = null;
    public $originalSlug;

    #[On('editCategory')]
    public function openEditCategoryModal($id)
    {
        $this->category = Category::findOrFail($id);

        $this->authorize('edit', $this->category);

        $this->name = $this->category->name;
        $this->description = $this->category->description;
        $this->parent_id = $this->category->parent_id;
        $this->originalSlug = $this->category->slug;

        $this->modal('edit-category')->show();
    }

    public function closeEditCategoryModal()
    {
        $this->category = null;
        $this->name = '';
        $this->description = '';
        $this->parent_id = null;
    }

    public function updateCategory()
    {
        $this->authorize('edit', $this->category);

        if($this->category->slug == 'sin-categoria') {
            Flux::toast(heading: 'No se puede editar', text: 'La categoría "Sin categoría" no puede ser editada', variant: 'warning');
            return;
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $this->category->update([
            'name' => $this->name,
            'slug' => Slug::generate($this->name, Category::class, $this->category->id),
            'description' => $this->description,
            'parent_id' => $this->parent_id ?: null,
        ]);

        $wasSlugChanged = $this->originalSlug !== $this->category->slug;

        $this->modal('edit-category')->close();

        $url = request()->header('Referer');
        $path = parse_url($url, PHP_URL_PATH);

        if (Str::startsWith($path, '/categorias/') && $path !== '/categorias' && $wasSlugChanged) {
            $this->redirectRoute('categories.show', $this->category->slug, navigate: true);
        } else {
            $this->dispatch('categoryUpdated');
        }
        
        Flux::toast(heading: 'Categoría actualizada', text: 'La categoría fue actualizada exitosamente', variant: 'success');
    }
}; ?>

<form wire:submit.prevent="updateCategory">
    <flux:modal name="edit-category" wire:close="closeEditCategoryModal" class="md:w-auto space-y-6">
        <div>
            <flux:heading size="lg">Editar categoría</flux:heading>
            <flux:text class="mt-2">Actualizá los datos de la categoría</flux:text>
        </div>

        <flux:input  placeholder="Nombre de la categoría" wire:model="name" label="Nombre" required autofocus />

        <flux:textarea label="Descripción" badge="Opcional" placeholder="Descripción de la categoría"
            wire:model="description" rows="3" />

        @if ($category && $category->parent_id)
            <flux:badge color="yellow" size="sm">Subcategoría de {{ $category->parent->name }}</flux:badge>
        @else
            <flux:badge color="green" size="sm">Categoría principal</flux:badge>
        @endif

        <div class="flex gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="ghost">Cancelar</flux:button>
            </flux:modal.close>

            <flux:button variant="primary" type="submit">Actualizar</flux:button>
        </div>
    </flux:modal>
</form>
