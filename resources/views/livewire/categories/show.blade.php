<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, On};
use Livewire\WithPagination;
use App\Models\Category;
use App\Models\Product;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public Category $category;

    public ?int $selectedSubcategoryId = null;
    public string $sortBy = 'newest';

    #[On('categoryUpdated')]
    public function refreshPage()
    {
        $this->dispatch('$refresh');
    }

    #[On('subcategorySelected')]
    public function setSubcategory(?int $subcategoryId)
    {
        $this->selectedSubcategoryId = $subcategoryId;
        $this->resetPage();
    }

    public function mount(Category $category)
    {
        $this->category = $category;
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        $query = Product::query()
            ->where('in_stock', true)
            ->when(
                $this->selectedSubcategoryId,
                function ($query) {
                    $query->where('category_id', $this->selectedSubcategoryId);
                },
                function ($query) {
                    if ($this->category->children()->exists()) {
                        $childIds = $this->category->children()->pluck('id');
                        $query->whereIn('category_id', $childIds);
                    } else {
                        $query->where('category_id', $this->category->id);
                    }
                },
            );

        match ($this->sortBy) {
            'oldest' => $query->oldest(),
            'cheapest' => $query->orderByRaw('COALESCE(discount_price, price) ASC'),
            'priciest' => $query->orderByRaw('COALESCE(discount_price, price) DESC'),
            default => $query->latest(),
        };

        $products = $query->paginate(12);

        return view('livewire.categories.show', compact('products'))->title(Str::ucfirst($this->category->name) . ' • Tortuga Second Hand');
    }
}; ?>

<div>
    <section class="min-h-screen container mx-auto px-4 sm:px-6 lg:px-8 mb-12">
        <div class="flex items-center justify-between">
            @include('livewire.partials.breadcrumb')

            <div class="flex items-center gap-4">
                <flux:select wire:model.live="sortBy" variant="listbox" class="sm:max-w-fit">
                    <x-slot name="trigger">
                        <flux:select.button size="sm">
                            <flux:icon.arrows-up-down variant="micro" class="mr-2 text-zinc-400" />
                            <flux:select.selected />
                        </flux:select.button>
                    </x-slot>
                    <flux:select.option value="newest">Más reciente</flux:select.option>
                    <flux:select.option value="oldest">Más antiguo</flux:select.option>
                    <flux:select.option value="cheapest">Precio más bajo</flux:select.option>
                    <flux:select.option value="priciest">Precio más alto</flux:select.option>
                </flux:select>

                @if ($category->parent_id == null)
                    <flux:modal.trigger name="more-filters-{{ $category->id }}">
                        <flux:button variant="primary" size="sm">Más filtros</flux:button>
                    </flux:modal.trigger>

                    <livewire:modals.more-filters :$category />
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="flex items-start justify-between gap-24">
                <div>
                    <div class="flex items-center gap-4 mb-2">
                        <flux:heading size="xl" level="1">{{ Str::ucfirst($category->name) }}</flux:heading>

                        @can('edit', $category)
                            <flux:button
                                wire:click="$dispatchTo('categories.edit', 'editCategory', { id: {{ $category->id }} })"
                                icon="pencil" size="sm" variant="ghost" />

                            <!-- Update category modal -->
                            <livewire:categories.edit />
                        @endcan
                    </div>

                    @if ($category->description)
                        <flux:subheading>
                            {{ Str::ucfirst($category->description) }}</strong>
                        </flux:subheading>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($products as $product)
                    <livewire:components.product-card wire:key="product-{{ $product->id }}" :product="$product" />
                @empty
                    <flux:text>No hay productos en esta categoría.</flux:text>
                @endforelse
            </div>

            @if ($products->hasPages())
                <flux:pagination :paginator="$products" />
            @endif
        </div>
    </section>
</div>
