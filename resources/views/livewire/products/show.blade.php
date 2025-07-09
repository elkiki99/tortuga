<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use Illuminate\Support\Facades\Storage; // Make sure this is present

new #[Layout('components.layouts.app')] class extends Component {
    public Product $product;
    public $relatedProducts;
    public $complete_look;
    public $images;
    // No need for a separate $featuredImage public property if we always access via $product->featuredImage
    // public $featuredImage; // Remove this if you go with Option 1

    public function mount(Product $product)
    {
        // Eager load the specific featuredImage relation, and other images/relations
        $this->product = $product->load([
            'featuredImage', // Load the specific hasOne relationship
            'images' => function ($query) {
                // Load non-featured images for thumbnails
                $query->where('is_featured', false)->take(4);
            },
            'category',
            'brand',
        ]);

        // No need to set $this->featuredImage if you access via $this->product->featuredImage directly
        // $this->featuredImage = $this->product->images->where('is_featured', true)->first(); // Remove this line

        // $this->images is already loaded with non-featured ones
        $this->images = $this->product->images; // This now contains the filtered non-featured images from eager loading

        $this->relatedProducts = Product::where('in_stock', true)->where('category_id', $product->category_id)->where('id', '!=', $product->id)->inRandomOrder()->take(4)->get();

        $this->complete_look = Product::where('in_stock', true)->where('category_id', '!=', $product->category_id)->where('id', '!=', $product->id)->inRandomOrder()->take(4)->get();
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
