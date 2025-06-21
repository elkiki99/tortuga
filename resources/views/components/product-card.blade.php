<article class="relative space-y-2">
    {{-- Botón corazón en la esquina superior derecha --}}
    <div class="absolute top-2 right-2 z-10">
        {{-- <button class="bg-white/80 hover:bg-white p-2 rounded-full shadow"> --}}
        <flux:icon.heart />
        {{-- </button> --}}
    </div>

    {{-- Imagen destacada del producto --}}
    @php
        $image = \App\Models\Image::where('product_id', $product->id)->where('is_featured', true)->first();
    @endphp

    @if ($image)
        <a href="{{ route('products.show', $product->slug) }}" wire:navigate
            class="block w-full aspect-square overflow-hidden mb-4 border border-black">
            <img src="{{ $image->path }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
        </a>
    @else
        <a href="{{ route('products.show', $product->slug) }}" wire:navigate
            class="block w-full aspect-square overflow-hidden mb-4 border border-black">
            <img src="https://via.placeholder.com/640x480?text=Sin+imagen" alt="Sin imagen"
                class="w-full h-full object-cover">
        </a>
    @endif
    {{-- Precio --}}
    <flux:heading size="lg">${{ $product->price }} UYU</flux:heading>

    {{-- Nombre y categoría --}}
    <div>
        <flux:heading>{{ Str::ucfirst($product->name) }}</flux:heading>
        <flux:subheading>{{ Str::ucfirst($product->category->name) }}</flux:subheading>
    </div>
</article>
