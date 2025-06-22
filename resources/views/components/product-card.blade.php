<article class="relative">
    {{-- Botón corazón en la esquina superior derecha --}}
    <div class="absolute top-2 right-2 z-10">
        {{-- <button class="bg-white/80 hover:bg-white p-2 rounded-full shadow"> --}}
        <flux:icon.heart />
        {{-- </button> --}}
    </div>

    <a href="{{ route('products.show', $product->slug) }}" wire:navigate
        class="block w-full aspect-square overflow-hidden bg-gray-100 mb-4">
        <img src="{{ $product->featuredImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
    </a>

    {{-- Precio --}}
    @if ($product->discount_price)
        <div class="flex items-center gap-2">
            <flux:heading class="text-red-600 !mb-0" size="lg">
                <strong>${{ $product->discount_price }} UYU</strong>
            </flux:heading>
            <flux:subheading class="line-through">${{ $product->price }} UYU</flux:subheading>
        </div>
    @else
        <flux:heading size="lg"><strong>${{ $product->price }} UYU</strong></flux:heading>
    @endif

    {{-- Nombre y categoría --}}
    <div class="mt-2">
        <flux:heading class="!mb-1" size="lg">{{ Str::ucfirst($product->name) }}</flux:heading>
        <flux:subheading>
            <flux:link variant="subtle" href="{{ route('categories.show', $product->category->slug) }}" wire:navigate>
                {{ Str::ucfirst($product->category->name) }}</flux:link>
        </flux:subheading>

    </div>
</article>
