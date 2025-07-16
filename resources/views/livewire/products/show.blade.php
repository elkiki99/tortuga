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
        if ($product->in_stock == false && (!Auth::check() || !Auth::user()->isAdmin())) {
            abort(404);
        }

        $this->product = $product->load([
            'featuredImage',
            'images' => function ($query) {
                $query->where('is_featured', false)->get();
            },
            'category',
            'brand',
        ]);

        $this->images = $this->product->images;

        $this->relatedProducts = Product::where('in_stock', true)->where('category_id', $product->category_id)->where('id', '!=', $product->id)->inRandomOrder()->get();

        $this->complete_look = Product::where('in_stock', true)->where('category_id', '!=', $product->category_id)->where('id', '!=', $product->id)->inRandomOrder()->get();
    }

    #[On('productUpdated')]
    public function updatedProduct()
    {
        $this->product->refresh();
        $this->images = $this->product->images()->where('is_featured', false)->get();
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
            <div class="flex gap-2 lg:w-3/5">
                <div class="flex flex-col gap-2 w-1/8">
                    @php
                        $thumbnailImages = $this->images;
                        $totalThumbnails = $this->images->count();
                    @endphp

                    @for ($i = 0; $i < $totalThumbnails; $i++)
                        <div class="w-full aspect-square rounded-sm bg-gray-100 overflow-hidden">
                            @if (isset($thumbnailImages[$i]) && $thumbnailImages[$i]->path)
                                <img src="{{ Storage::url($thumbnailImages[$i]->path) }}"
                                    alt="{{ $thumbnailImages[$i]->alt_text ?? $this->product->name . ' - Imagen ' . ($i + 1) }}"
                                    class="object-cover w-full h-full @if (!$this->product->in_stock) filter blur-xs @endif">
                            @else
                                <img src="https://via.placeholder.com/150?text=Placeholder+{{ $i + 1 }}"
                                    alt="{{ $this->product->name . '-m-' . ($i + 1) }}"
                                    class="object-cover w-full h-full @if (!$this->product->in_stock) filter blur-xs @endif">
                            @endif
                        </div>
                    @endfor
                </div>

                <div
                    class="flex-1 aspect-square bg-gray-100 flex items-center justify-center rounded-sm overflow-hidden relative">

                    @if ($this->product->featuredImage)
                        <img src="{{ Storage::url($this->product->featuredImage->path) }}"
                            alt="{{ $this->product->featuredImage->alt_text ?? $this->product->name }}"
                            class="object-contain w-full h-full @if (!$this->product->in_stock) filter blur-xs @endif">
                    @else
                        <img src="https://via.placeholder.com/640x640?text=Sin+imagen+principal"
                            alt="{{ $this->product->name }}"
                            class="object-contain w-full h-full @if (!$this->product->in_stock) filter blur-xs @endif">
                    @endif

                    @if (!$this->product->in_stock)
                        <div class="absolute top-0 left-0 w-full text-center py-2 bg-red-600 bg-opacity-50">
                            <span class="text-white font-semibold text-lg select-none">VENDIDO</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="lg:w-1/2">
                <div class="flex items-center gap-4">
                    <flux:heading size="xl" level="1">{{ Str::ucfirst($product->name) }}</flux:heading>

                    @auth
                        @if (Auth::user()->isAdmin())
                            <flux:modal.trigger name="edit-product-{{ $product->id }}">
                                <flux:button icon="pencil" size="sm" variant="ghost" />
                            </flux:modal.trigger>

                            <livewire:products.edit :$product wire:key="edit-product-{{ $product->id }}" />
                        @endif
                    @endauth
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

                    <flux:heading size="xl">
                        <strong>${{ $product->discount_price }} UYU</strong>
                    </flux:heading>

                    <flux:subheading class="text-gray-500">
                        Hasta 6 cuotas de ${{ number_format($product->discount_price / 6, 2) }} UYU
                    </flux:subheading>
                @else
                    <flux:heading size="xl">
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

                    @auth
                        @if (Auth::user()->isAdmin())
                            <div class="mt-6">
                                <flux:modal.trigger name="delete-product-{{ $product->id }}">
                                    <flux:badge as="button" color="red" icon="trash">Eliminar producto</flux:button>
                                </flux:modal.trigger>

                                <livewire:products.delete :$product wire:key="delete-product-{{ $product->id }}" />
                            </div>
                        @endif
                    @endauth
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
