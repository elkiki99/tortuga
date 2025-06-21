<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};
use App\Models\Product;

new #[Layout('components.layouts.app')] class extends Component {
    public Product $product;

    public function mount(Product $product)
    {
        $this->product = $product;
    }

    // Title
    public function render(): mixed
    {
        return view('livewire.products.show', [
            // 'product' => $this->product,
        ])->title($this->product->name . ' • Tortuga Second Hand');
    }
}; ?>

<section class="min-h-screen max-w-7xl mx-auto mt-6 px-4 sm:px-6 lg:px-8 my-12">
    @include('livewire.partials.breadcrumb')

    <div class="flex flex-col lg:flex-row gap-6 mt-6">
        <div class="flex gap-4 lg:w-1/2">
            {{-- Miniaturas a la izquierda --}}
            <div class="flex flex-col gap-4 w-1/6">
                @for ($i = 0; $i < 4; $i++)
                    <div class="flex-1 aspect-square bg-gray-200 overflow-hidden">
                        <img src="https://via.placeholder.com/150?text=IMG+{{ $i + 1 }}"
                            alt="Miniatura {{ $i + 1 }}" class="object-cover">
                    </div>
                @endfor
            </div>

            {{-- Imagen principal --}}
            <div class="flex-1 bg-gray-100 flex items-center justify-center aspect-square overflow-hidden">
                <img src="{{ $product->image_url ?? 'https://via.placeholder.com/640x640?text=Sin+imagen' }}"
                    alt="{{ $product->name }}" class="object-contain">
            </div>
        </div>

        {{-- Columna derecha: Solo título y subtítulo --}}
        <div class="lg:w-1/2">
            <flux:heading size="xl" level="1">{{ $product->name }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">{{ $product->category->name }}
            </flux:subheading>

            @if ($product->discount_price)
                <flux:subheading class="line-through" size="lg">
                    ${{ $product->price }} UYU
                </flux:subheading>

                <flux:heading size="lg">
                    ${{ $product->discount_price }} UYU
                </flux:heading>

                <flux:subheading class="text-gray-500">
                    Hasta 12 cuotas de ${{ number_format($product->discount_price / 12, 2) }} UYU
                </flux:subheading>
            @else
                <flux:heading size="lg">
                    ${{ $product->price }} UYU
                </flux:heading>

                <flux:subheading class="text-gray-500">
                    Hasta 12 cuotas de ${{ number_format($product->price / 12, 2) }} UYU
                </flux:subheading>
            @endif

            <div class="mt-6 space-y-6">
                <flux:text>
                    {{ $product->description }}
                </flux:text>

                @if ($product->brand)
                    <flux:heading>
                        {{ $product->brand->name }}
                    </flux:heading>
                @endif

                <flux:button variant="primary" class="!rounded-full w-full" icon="shopping-cart">Agregar al carrito
                </flux:button>

                <flux:callout icon="credit-card">
                    <flux:callout.heading>Pago en cuotas</flux:callout.heading>
                    <flux:callout.text>
                        Podes pagar hasta en 12 cuotas sin recargo con mercado pago o tarjeta de crédito.
                        {{-- <flux:callout.link href="#">Learn more</flux:callout.link> --}}
                    </flux:callout.text>
                </flux:callout>
            </div>
        </div>
    </div>
</section>
