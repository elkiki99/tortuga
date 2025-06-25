<div>
    @php
        $products = App\Models\Product::where('in_stock', true)->take(12)->get();
    @endphp

    @if ($products->isNotEmpty())
        <section class="container mx-auto px-4 mt-6 md:px-6 lg:px-8 my-12">
            <flux:heading size="xl">Novedades</flux:heading>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-6">
                @foreach ($products as $product)
                    <livewire:components.product-card wire:key="product-{{ $product->id }}" :product="$product" />
                @endforeach
            </div>
        </section>
    @endif
</div>
