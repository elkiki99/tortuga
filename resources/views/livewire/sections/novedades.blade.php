<section class="min-h-screen max-w-7xl mx-auto mt-6 px-4 sm:px-6 lg:px-8 my-12">
    <flux:heading size="xl">Novedades</flux:heading>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-6">
        @foreach (App\Models\Product::take(12)->get() as $product)
            <x-product-card :product="$product" />
        @endforeach
    </div>
</section>
