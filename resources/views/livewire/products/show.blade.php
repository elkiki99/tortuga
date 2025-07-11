<?php

use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\{Layout, On};
use Livewire\Volt\Component;
use App\Models\Product;

new #[Layout('components.layouts.app')] class extends Component {
    public Product $product;
    public $relatedProducts;
    public $complete_look;
    public $images;

    public function mount(Product $product)
    {
        $this->product = $product->load([
            'featuredImage', // Load the specific hasOne relationship
            'images' => function ($query) {
                // Load non-featured images for thumbnails
                $query->where('is_featured', false)->take(4);
            },
            'category',
            'brand',
        ]);

        // $this->images is already loaded with non-featured ones
        $this->images = $this->product->images; // This now contains the filtered non-featured images from eager loading

        $this->relatedProducts = Product::where('in_stock', true)->where('category_id', $product->category_id)->where('id', '!=', $product->id)->inRandomOrder()->take(4)->get();

        $this->complete_look = Product::where('in_stock', true)->where('category_id', '!=', $product->category_id)->where('id', '!=', $product->id)->inRandomOrder()->take(4)->get();
    }

    #[On('productUpdated')]
    public function updatedProduct()
    {
        $this->dispatch('$refresh');
    }

    public function render(): mixed
    {
        return view('livewire.products.show')->title($this->product->name . ' • Tortuga');
    }
}; ?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 space-y-12 mb-12">
    <section>
        @include('livewire.partials.breadcrumb')

        <div class="flex flex-col lg:flex-row gap-6">
            <div class="flex gap-2 lg:w-3/4">
                {{-- Small image thumbnails --}}
                <div class="flex flex-col gap-2 w-1/6">
                    @php
                        // Ensure we have a collection of non-featured images for the thumbnails
                        // $this->images already contains non-featured ones due to mount method
                        $thumbnailImages = $this->images; // Renaming for clarity in the loop
                        $totalThumbnails = 4;
                    @endphp

                    @for ($i = 0; $i < $totalThumbnails; $i++)
                        <div class="flex-1 rounded-sm aspect-square bg-gray-100 overflow-hidden">
                            @if (isset($thumbnailImages[$i]) && $thumbnailImages[$i]->path)
                                <img src="{{ Storage::url($thumbnailImages[$i]->path) }}"
                                    alt="{{ $thumbnailImages[$i]->alt_text ?? $this->product->name . ' - Imagen ' . ($i + 1) }}"
                                    class="object-cover w-full h-full">
                            @else
                                <img src="https://via.placeholder.com/150?text=Placeholder+{{ $i + 1 }}"
                                    alt="{{ $this->product->name . ' - Miniatura ' . ($i + 1) }}"
                                    class="object-cover w-full h-full">
                            @endif
                        </div>
                    @endfor
                </div>

                {{-- Main featured image --}}
                <div
                    class="flex-1 bg-gray-100 flex items-center rounded-sm justify-center aspect-square overflow-hidden relative">
                    @if ($this->product->featuredImage)
                        {{-- Check if the RELATIONSHIP exists --}}
                        <img src="{{ Storage::url($this->product->featuredImage->path) }}"
                            alt="{{ $this->product->featuredImage->alt_text ?? $this->product->name . ' - Imagen principal' }}"
                            class="object-contain w-full h-full">
                    @else
                        <img src="https://via.placeholder.com/640x640?text=Sin+imagen+principal"
                            alt="{{ $this->product->name . ' - Sin imagen principal' }}"
                            class="object-contain w-full h-full">
                    @endif
                </div>
            </div>

            <div class="lg:w-1/2">
                <div class="flex items-center gap-4">
                    <flux:heading size="xl" level="1">{{ Str::ucfirst($product->name) }}</flux:heading>

                    @if(Auth::user()->isAdmin())
                        <flux:modal.trigger name="edit-product-{{ $product->id }}">
                            <flux:button icon="pencil" size="sm" variant="ghost" />
                        </flux:modal.trigger>

                        <livewire:products.edit :$product wire:key="edit-product-{{ $product->id }}" />
                    @endif
                </div>

                <flux:subheading size="lg" class="mb-6">
                    <flux:link variant="subtle" href="{{ route('categories.show', $product->category->slug) }}"
                        wire:navigate>
                        {{ Str::ucfirst($product->category->name) }}</flux:link>
                </flux:subheading>

                @if ($product->discount_price)
                    <flux:subheading class="line-through">
                        ${{ $product->price }} UYU
                    </flux:subheading>

                    <flux:heading size="lg">
                        <strong>${{ $product->discount_price }} UYU</strong>
                    </flux:heading>

                    <flux:subheading class="text-gray-500">
                        Hasta 6 cuotas de ${{ number_format($product->discount_price / 6, 2) }} UYU
                    </flux:subheading>
                @else
                    <flux:heading size="lg">
                        <strong>${{ $product->price }} UYU</strong>
                    </flux:heading>

                    <flux:subheading class="text-gray-500">
                        Hasta 6 cuotas de ${{ number_format($product->price / 6, 2) }} UYU
                    </flux:subheading>
                @endif

                <div class="mt-6 space-y-6">    
                    <flux:text>
                        {{ Str::ucfirst($product->description) }}
                    </flux:text>

                    @if ($product->brand)
                        <flux:heading>
                            {{ Str::ucfirst($product->brand->name) }}
                        </flux:heading>
                    @endif

                    <div class="flex flex-grow items-center gap-4">
                        <livewire:cart.add :product="$product" />
                        <livewire:wishlist.add :product="$product" />
                    </div>

                    <flux:callout icon="credit-card">
                        <flux:callout.heading>Pago en cuotas</flux:callout.heading>
                        <flux:callout.text>
                            Podes pagar hasta en <strong>6 cuotas sin recargo</strong> con mercadopago.
                        </flux:callout.text>
                    </flux:callout>

                    <flux:link href="mailto:{{ config('mail.from.address') }}">¿Querés reservar esta prenda? Mandanos
                        un mail</flux:link>
                    
                    @if(Auth::user()->isAdmin())
                        <div class="mt-6">
                            <flux:modal.trigger name="delete-product-{{ $product->id }}">
                                <flux:badge as="button" color="red" icon="trash">Eliminar producto</flux:button>
                            </flux:modal.trigger>

                            <livewire:products.delete :$product wire:key="delete-product-{{ $product->id }}" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section>
            <flux:heading size="xl">Más {{ Str::ucfirst($product->category->name) }}</flux:heading>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-6">
                @foreach ($relatedProducts as $related_product)
                    <livewire:components.product-card wire:key="product-{{ $product->id }}" :product="$related_product" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($complete_look->isNotEmpty())
        <section>
            <flux:heading size="xl">Completa tu look</flux:heading>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-6">
                @foreach ($complete_look as $cl_product)
                    <livewire:components.product-card wire:key="product-{{ $product->id }}" :product="$cl_product" />
                @endforeach
            </div>
        </section>
    @endif
</div>
