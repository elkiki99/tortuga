<section class="min-h-screen container mx-auto px-4 mt-6 md:px-6 lg:px-8 my-12">
    <flux:heading size="xl">Novedades</flux:heading>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-6">
        @foreach (App\Models\Product::where('in_stock', true)->take(12)->get() as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
</section>