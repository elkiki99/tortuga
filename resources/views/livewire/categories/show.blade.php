<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Category;
use App\Models\Product;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public Category $category;

    public function mount(Category $category)
    {
        $this->category = $category;
    }

    public function render(): mixed
    {
        if ($this->category->children()->exists()) {
            $childIds = $this->category->children()->pluck('id');

            $products = Product::whereIn('category_id', $childIds)->where('in_stock', true)->latest()->paginate(12);
        } else {
            $products = Product::where('category_id', $this->category->id)->where('in_stock', true)->latest()->paginate(12);
        }

        return view('livewire.categories.show', compact('products'))->title($this->category->name . ' • Tortuga Second Hand');
    }
}; ?>

<div>
    <section class="min-h-screen container mx-auto px-4 sm:px-6 lg:px-8 mb-12">
        <div class="flex items-center justify-between">
            @include('livewire.partials.breadcrumb')

            <div class="flex items-center gap-4">
                <flux:select variant="listbox" class="sm:max-w-fit" align="end">
                    <x-slot name="trigger">
                        <flux:select.button size="sm">
                            <flux:icon.arrows-up-down variant="micro" class="mr-2 text-zinc-400" />
                            <flux:select.selected />
                        </flux:select.button>
                    </x-slot>
                    <flux:select.option value="newest" selected>Más reciente</flux:select.option>
                    <flux:select.option value="oldest">Más antiguo</flux:select.option>
                    <flux:select.option value="cheapest">Precio más bajo</flux:select.option>
                    <flux:select.option value="priciest">Precio más alto</flux:select.option>
                </flux:select>

                <flux:modal.trigger name="more-filters-{{ $category->id }}">
                    <flux:button variant="primary" size="sm">Más filtros</flux:button>
                </flux:modal.trigger>

                <livewire:modals.more-filters :$category />
            </div>
        </div>

        <div class="space-y-6">
            <div class="flex items-start justify-between gap-24">
                <div>
                    <flux:heading size="xl" level="1">
                        {{ Str::ucfirst($category->name) }}
                    </flux:heading>
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
