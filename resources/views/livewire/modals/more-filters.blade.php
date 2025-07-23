<?php

use Livewire\Volt\Component;
use App\Models\Category;

new class extends Component {
    public Category $category;
    public ?int $subcategorySelected = null;

    public function mount(Category $category)
    {
        $this->category = $category;
    }

    public function updatedSubcategorySelected($value)
    {
        $this->dispatch('subcategorySelected', subcategoryId: $value);
    }
}; ?>

<flux:modal name="more-filters-{{ $category->id }}" variant="flyout" class="max-w-sm !space-y-6">
    <div>
        <flux:heading size="lg">{{ Str::ucfirst($category->name) }}</flux:heading>
        <flux:subheading>Filtrar por subcategoría</flux:subheading>
    </div>
    
    <flux:radio.group wire:model.live="subcategorySelected">
        @foreach ($category->children as $child)
            <flux:radio name="subcategorySelected" value="{{ $child->id }}" label="{{ Str::ucfirst($child->name) }}"
                description="{{ $child->description ?? '' }}" />
        @endforeach

        @if ($category->children->isNotEmpty())
            <flux:radio name="subcategorySelected" value="" label="Todas"
                description="Mostrar todos los productos de esta categoría" />
        @endif
    </flux:radio.group>
</flux:modal>
