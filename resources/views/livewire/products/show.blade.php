<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;

new #[Layout('components.layouts.app')] class extends Component {
    public Product $product;
    public $relatedProducts;
    public $complete_look;
    public $images;

    public function mount(Product $product)
    {
        $this->product = $product;

        $this->images = $product->images->take(4);

        $this->relatedProducts = Product::where('in_stock', true)->where('category_id', $product->category_id)->where('id', '!=', $product->id)->inRandomOrder()->take(4)->get();

        $this->complete_look = Product::where('in_stock', true)->where('category_id', '!=', $product->category_id)->where('id', '!=', $product->id)->inRandomOrder()->take(4)->get();
    }

    public function render(): mixed
    {
        return view('livewire.products.show')->title($this->product->name . ' • Tortuga Second Hand');
    }
}; ?>

<div class="min-h-screen max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 mb-12">
    <section>
        @include('livewire.partials.breadcrumb')

        <div class="flex flex-col lg:flex-row gap-6">
            <div class="flex gap-4 lg:w-3/4">
                <div class="flex flex-col gap-4 w-1/6">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="flex-1 aspect-square bg-gray-100 overflow-hidden">
                            @if ($i < $images->count())
                                <img src="{{ $images[$i]->url ?? 'https://via.placeholder.com/150?text=IMG+' . $images[$i]->id }}"
                                    alt="{{ $images[$i]->alt_text ?? '' }}" class="object-cover w-full h-full">
                            @else
                                <img src="https://via.placeholder.com/150?text=Placeholder+{{ $i + 1 }}"
                                    alt="" class="object-cover w-full h-full">
                            @endif
                        </div>
                    @endfor
                </div>

                {{-- Imagen principal --}}
                <div class="flex-1 bg-gray-100 flex items-center justify-center aspect-square overflow-hidden relative">
                    {{-- <div class="absolute top-4 right-4 z-10">
                        <livewire:wishlist.add :product="$product" :key="'wishlist-add-' . $product->id" />
                    </div> --}}
                    <img src="{{ $product->featuredImage ?? 'https://via.placeholder.com/640x640?text=Sin+imagen' }}"
                        alt="{{ $product->name }}" class="object-contain">
                </div>
            </div>

            {{-- Columna derecha: Solo título y subtítulo --}}
            <div class="lg:w-1/2">
                <flux:heading size="xl" level="1">{{ Str::ucfirst($product->name) }}</flux:heading>

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
                        Hasta 12 cuotas de ${{ number_format($product->discount_price / 12, 2) }} UYU
                    </flux:subheading>
                @else
                    <flux:heading size="lg">
                        <strong>${{ $product->price }} UYU</strong>
                    </flux:heading>

                    <flux:subheading class="text-gray-500">
                        Hasta 12 cuotas de ${{ number_format($product->price / 12, 2) }} UYU
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
                            Podes pagar hasta en <strong>12 cuotas sin recargo</strong> con mercadopago.
                        </flux:callout.text>
                    </flux:callout>

                    <flux:link href="mailto:{{ config('mail.from.address') }}">¿Querés reservar esta prenda? Mandanos
                        un mail</flux:link>
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
