<?php

use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\{Layout, On};
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use App\Models\Product;

new #[Layout('components.layouts.app')] class extends Component {
    public Product $product;
    public $relatedProducts;
    public $complete_look;
    public $images;

    public function mount(Product $product)
    {
        $this->authorize('view', $product);

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
        return view('livewire.products.show')->title(Str::ucfirst($this->product->name) . ' • Tortuga');
    }
}; ?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 space-y-12 mb-12">
    <section>
        @include('livewire.partials.breadcrumb')

        <div class="flex flex-col lg:flex-row gap-6">
            <div x-data="{ featured: '{{ Storage::url($this->product->featuredImage?->path) }}' }" class="flex gap-0 lg:w-3/5">
                @if ($images->count() > 0)
                    <div class="flex flex-col gap-0 w-1/8">
                        @forelse ($images as $thumb)
                            <div class="w-full hover:cursor-zoom-in aspect-square first:rounded-tl-sm last:rounded-bl-sm bg-gray-100 overflow-hidden pr-2 pb-2 bg-white dark:bg-zinc-800"
                                @mouseenter="featured = '{{ Storage::url($thumb->path) }}'"
                                @mouseleave="featured = '{{ Storage::url($this->product->featuredImage?->path) }}'">

                                <img src="{{ Storage::url($thumb->path) }}"
                                    alt="{{ $thumb->alt_text ?? $this->product->name }}"
                                    class="object-cover w-full h-full @if (!$this->product->in_stock) filter blur-xs @endif">
                            </div>
                        @empty
                        @endforelse
                    </div>
                @endif

                <div
                    class="flex-1 aspect-square bg-gray-100 flex items-center justify-center rounded-tr-sm rounded-bl-sm rounded-br-sm overflow-hidden relative">
                    @if ($this->product->featuredImage)
                        <img :src="featured"
                            alt="{{ $this->product->featuredImage->alt_text ?? $this->product->name }}"
                            class="object-contain w-full h-full @if (!$this->product->in_stock) filter blur-xs @endif">
                    @endif

                    @if (!$this->product->in_stock)
                        <div class="absolute top-0 left-0 w-full text-center py-2 bg-red-600 bg-opacity-50">
                            <span class="text-white font-semibold text-lg select-none">VENDIDO</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Product Info --}}
            <div class="lg:w-1/2">
                <div class="flex items-center gap-4">
                    <flux:heading size="xl" level="1">{{ Str::ucfirst($product->name) }}</flux:heading>

                    @can('edit', $product)
                        <flux:modal.trigger name="edit-product-{{ $product->id }}">
                            <flux:button icon="pencil" size="sm" variant="ghost" />
                        </flux:modal.trigger>

                        <livewire:products.edit :$product wire:key="edit-product-{{ $product->id }}" />
                    @endcan
                </div>

                <flux:subheading size="lg" class="mb-4">
                    <flux:link variant="subtle" href="{{ route('categories.show', $product->category->slug) }}"
                        wire:navigate>
                        {{ Str::ucfirst($product->category->name) }}</flux:link>
                </flux:subheading>

                @if ($product->discount_price)
                    <flux:subheading class="line-through">
                        ${{ number_format($product->price, 2, ',', '.') }} UYU
                    </flux:subheading>

                    <flux:heading size="xl">
                        <strong>${{ number_format($product->discount_price, 2, ',', '.') }} UYU</strong>
                    </flux:heading>

                    <flux:subheading class="text-gray-500">
                        Hasta 6 cuotas de ${{ number_format($product->discount_price / 6, 2, ',', '.') }} UYU
                    </flux:subheading>
                @else
                    <flux:heading size="xl">
                        <strong>${{ number_format($product->price, 2, ',', '.') }} UYU</strong>
                    </flux:heading>

                    <flux:subheading class="text-gray-500">
                        Hasta 6 cuotas de ${{ number_format($product->price / 6, 2, ',', '.') }} UYU
                    </flux:subheading>
                @endif

                <div class="mt-6 space-y-6">
                    <flux:text>
                        {{ Str::ucfirst($product->description) }}
                    </flux:text>

                    <div class="space-y-4">
                        @if ($product->brand)
                            <flux:heading>
                                {{ Str::ucfirst($product->brand->name) }}
                            </flux:heading>
                        @endif

                        @if ($product->size)
                            <flux:card class="w-auto py-2 px-4 inline-block">
                                <div class="flex items-center gap-2">
                                    <flux:heading>{{ $product->size }}</flux:heading>
                                    <flux:icon.tag variant="micro" />
                                </div>
                            </flux:card>
                        @endif
                    </div>

                    <div class="flex flex-grow items-center gap-4">
                        <livewire:cart.add :product="$product" />

                        @can('add', \App\Models\Wishlist::class)
                            <livewire:wishlist.add :product="$product" />
                        @endcan
                    </div>

                    <flux:callout icon="credit-card">
                        <flux:callout.heading>Pago en cuotas</flux:callout.heading>
                        <flux:callout.text>
                            Podes pagar hasta en <strong>6 cuotas sin recargo</strong> con mercadopago.
                        </flux:callout.text>
                    </flux:callout>

                    <flux:link href="mailto:{{ config('mail.from.address') }}">¿Querés reservar esta prenda? Mandanos
                        un mail</flux:link>

                    @can('delete', $product)
                        <div class="mt-6">
                            <flux:modal.trigger name="delete-product-{{ $product->id }}">
                                <flux:badge as="button" color="red" icon="trash">Eliminar producto</flux:button>
                            </flux:modal.trigger>

                            <livewire:products.delete :$product wire:key="delete-product-{{ $product->id }}" />
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section>
            <flux:heading size="xl">Más {{ Str::ucfirst($product->category->name) }}</flux:heading>

            <div x-data="productCarousel()" x-init="init()" class="relative mt-6">
                <!-- Scroll wrapper -->
                <div x-ref="container" class="flex gap-2 overflow-x-hidden scroll-smooth snap-x snap-mandatory">
                    @foreach ($relatedProducts as $related_product)
                        <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/3 xl:w-1/4 snap-start pr-4">
                            <livewire:components.product-card wire:key="related-{{ $related_product->id }}"
                                :product="$related_product" />
                        </div>
                    @endforeach
                </div>

                <!-- Botón izquierda -->
                <button @click="scrollLeft()" class="absolute top-[-10%] left-0 h-full px-2 z-10 flex items-center"
                    :class="{ 'hidden': !canScrollLeft }" aria-label="Scroll izquierda">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <!-- Botón derecha -->
                <button @click="scrollRight()" class="absolute top-[-10%] right-0 h-full px-2 z-10 flex items-center"
                    :class="{ 'hidden': !canScrollRight }" aria-label="Scroll derecha">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </section>
    @endif

    @if ($complete_look->isNotEmpty())
        <section>
            <flux:heading size="xl">Completa tu look</flux:heading>

            <div x-data="productCarousel()" x-init="init()" class="relative mt-6">
                <!-- Scroll wrapper -->
                <div x-ref="container" class="flex gap-2 overflow-x-hidden scroll-smooth snap-x snap-mandatory">
                    @foreach ($complete_look as $cl_product)
                        <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/3 xl:w-1/4 snap-start pr-4">
                            <livewire:components.product-card wire:key="look-{{ $cl_product->id }}"
                                :product="$cl_product" />
                        </div>
                    @endforeach
                </div>

                <!-- Botones igual que arriba -->
                <button @click="scrollLeft()" class="absolute top-[-10%] left-0 h-full px-2 z-10 flex items-center"
                    :class="{ 'hidden': !canScrollLeft }" aria-label="Scroll izquierda">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button @click="scrollRight()" class="absolute top-[-10%] right-0 h-full px-2 z-10 flex items-center"
                    :class="{ 'hidden': !canScrollRight }" aria-label="Scroll derecha">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </section>
    @endif

    <!-- Script compartido -->
    <script>
        function productCarousel() {
            return {
                canScrollLeft: false,
                canScrollRight: false,
                init() {
                    this.updateButtons()
                    this.$refs.container.addEventListener('scroll', () => this.updateButtons())
                    window.addEventListener('resize', () => this.updateButtons())
                },
                scrollLeft() {
                    this.$refs.container.scrollBy({
                        left: -this.$refs.container.clientWidth,
                        behavior: 'smooth'
                    })
                },
                scrollRight() {
                    this.$refs.container.scrollBy({
                        left: this.$refs.container.clientWidth,
                        behavior: 'smooth'
                    })
                },
                updateButtons() {
                    const el = this.$refs.container
                    this.canScrollLeft = el.scrollLeft > 0
                    this.canScrollRight = el.scrollLeft + el.clientWidth < el.scrollWidth - 1
                }
            }
        }
    </script>
</div>
