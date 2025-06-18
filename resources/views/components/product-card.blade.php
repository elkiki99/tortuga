<article class="relative space-y-2">
    {{-- Botón corazón en la esquina superior derecha --}}
    <div class="absolute top-2 right-2 z-10">
        <button class="bg-white/80 hover:bg-white p-2 rounded-full shadow">
            <flux:icon.heart class="w-5 h-5 text-pink-500" />
        </button>
    </div>

    {{-- Imagen destacada del producto --}}
    @php
        $image = \App\Models\Image::where('product_id', $product->id)
            ->where('is_featured', true)
            ->first();
    @endphp

    @if ($image)
        <img src="{{ $image->path }}" alt="{{ $product->name }}" class="w-full h-48 object-cover mb-4">
    @else
        <img src="https://via.placeholder.com/640x480?text=Sin+imagen" alt="Sin imagen" class="w-full h-48 object-cover mb-4">
    @endif

    {{-- Precio --}}
    <flux:heading size="lg">${{ $product->price }} UYU</flux:heading>

    {{-- Nombre y categoría --}}
    <div>
        <flux:heading>{{ Str::ucfirst($product->name) }}</flux:heading>
        <flux:subheading>{{ Str::ucfirst($product->category->name) }}</flux:subheading>
    </div>
</article>