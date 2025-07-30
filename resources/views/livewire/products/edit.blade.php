<?php

use Livewire\WithFileUploads;
use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Helpers\Slug;

new class extends Component {
    use WithFileUploads;

    public ?Product $product = null;

    public $name;
    public $description;
    public $price;
    public $discount_price;
    public $size;
    public $in_stock;
    public $category_id;
    public $brand_id;
    public $originalSlug;

    public $categories = [];
    public $brands = [];

    public $featured_image;
    public array $attachments = [];
    public array $imagesToDelete = [];
    public array $visibleImages = [];

    #[On('editProduct')]
    public function openEditProductModal($id)
    {
        $this->product = Product::with(['category', 'brand', 'images'])->findOrFail($id);

        $this->authorize('edit', $this->product);

        $this->name = $this->product->name;
        $this->description = $this->product->description;
        $this->price = $this->product->price;
        $this->discount_price = $this->product->discount_price;
        $this->size = $this->product->size;
        $this->in_stock = (bool) $this->product->in_stock;
        $this->category_id = $this->product->category_id;
        $this->brand_id = $this->product->brand_id;
        $this->visibleImages = $this->product->images->pluck('id')->toArray();
        $this->originalSlug = $this->product->slug;

        $this->categories = Category::query()
            ->whereNotNull('parent_id')
            ->orWhere(function ($query) {
                $query->whereNull('parent_id')->doesntHave('children');
            })
            ->orderBy('name')
            ->get();

        $this->brands = Brand::all();

        $this->modal('edit-product')->show();
    }

    public function closeEditProductModal()
    {
        $this->product = null;
        $this->categories = [];
        $this->brands = [];
    }

    public function updateProduct()
    {
        $this->authorize('edit', $this->product);

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1023',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lte:price',
            'size' => 'nullable|string|max:50',
            'in_stock' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'featured_image' => 'nullable|image|max:2048|mimes:png,jpg,jpeg,webp',
            'attachments.*' => 'nullable|image|max:2048|mimes:png,jpg,jpeg,webp',
        ]);

        $this->product->update([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'slug' => Slug::generate($this->name, Product::class, $this->product->id),
            'discount_price' => $this->discount_price,
            'size' => $this->size,
            'in_stock' => $this->in_stock,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
        ]);

        $wasSlugChanged = $this->originalSlug !== $this->product->slug;

        if ($this->featured_image) {
            if ($this->product->featuredImage) {
                $this->product->featuredImage->delete();
            }
            $featuredPath = $this->featured_image->store('products', 'public');

            $this->product->images()->create([
                'path' => $featuredPath,
                'alt_text' => $this->name . '_featured',
                'is_featured' => true,
            ]);
        }

        foreach ($this->attachments as $key => $attachment) {
            $path = $attachment->store('products', 'public');

            $this->product->images()->create([
                'path' => $path,
                'alt_text' => $this->name . '_' . uniqid(),
                'is_featured' => false,
            ]);
        }
        $this->attachments = [];

        foreach ($this->imagesToDelete as $imageId) {
            $image = $this->product->images()->find($imageId);

            if ($image) {
                \Storage::disk('public')->delete($image->path);
                $image->delete();
            }
        }
        $this->imagesToDelete = [];
        $this->visibleImages = $this->product->images->pluck('id')->toArray();

        $this->modal('edit-product')->close();

        $url = request()->header('Referer');
        $path = parse_url($url, PHP_URL_PATH);

        if (Str::startsWith($path, '/productos/') && $path !== '/productos' && $wasSlugChanged) {
            $this->redirectRoute('products.show', $this->product->slug, navigate: true);
        } else {
            $this->dispatch('productUpdated');
        }

        Flux::toast(heading: 'Producto actualizado', text: 'El producto fue actualizado exitosamente', variant: 'success');
    }

    public function removeImage($type, $key)
    {
        if ($type === 'db') {
            if (!in_array($key, $this->imagesToDelete)) {
                $this->imagesToDelete[] = $key;
            }
            $this->visibleImages = array_values(array_filter($this->visibleImages, fn($id) => $id !== $key));
        }

        if ($type === 'temp') {
            unset($this->attachments[$key]);
            $this->attachments = array_values($this->attachments);
        }
    }
}; ?>

<form wire:submit.prevent="updateProduct">
    <flux:modal name="edit-product" wire:close="closeEditProductModal" class="md:w-auto space-y-6">
        <div>
            <flux:heading size="lg">Editar producto</flux:heading>
            <flux:text class="mt-2">Modifica los datos del producto</flux:text>
        </div>

        <flux:input label="Nombre" wire:model="name" placeholder="Nombre del producto" required autofocus />

        <flux:textarea label="Descripción" wire:model="description" rows="3"
            placeholder="Descripción del producto" />

        <flux:field>
            <flux:label>Precio</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>UYU</flux:input.group.prefix>
                <flux:input type="number" wire:model="price" required min="0" />
            </flux:input.group>
            <flux:error name="price" />
        </flux:field>

        <flux:field>
            <flux:label badge="Opcional">Precio con descuento</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>UYU</flux:input.group.prefix>
                <flux:input type="number" wire:model="discount_price" min="0" />
            </flux:input.group>
            <flux:error name="discount_price" />
        </flux:field>

        <flux:input label="Tamaño" wire:model="size" badge="Opcional" placeholder="XS" />

        <flux:select wire:model="category_id" required label="Categoría" variant="listbox" searchable
            placeholder="Selecciona una categoría">
            @forelse ($categories as $category)
                <flux:select.option value="{{ $category->id }}">
                    {{ Str::ucfirst($category->parent?->name) ? Str::ucfirst($category?->parent->name) . ' - ' : '' }}{{ Str::ucfirst($category->name) }}
                </flux:select.option>
            @empty
                <flux:select.option disabled>No hay categorías</flux:select.option>
            @endforelse
        </flux:select>

        <flux:select wire:model="brand_id" label="Marca" badge="Opcional" variant="listbox" searchable
            placeholder="Selecciona una marca">
            @forelse ($brands as $brand)
                <flux:select.option value="{{ $brand->id }}">{{ $brand->name }}</flux:select.option>
            @empty
                <flux:select.option disabled>No hay marcas</flux:select.option>
            @endforelse
        </flux:select>

        <flux:input label="Nueva imagen destacada" accept="image/png, image/jpeg, image/jpg, image/webp" type="file"
            wire:model="featured_image" />

        @if ($featured_image)
            <img src="{{ $featured_image->temporaryUrl() }}">
        @elseif ($product && $product->featuredImage)
            <img src="{{ Storage::url($product->featuredImage->path) }}" alt="{{ $product->featuredImage->alt_text }}">
        @endif

        <flux:input label="Agregar más imágenes" accept="image/png, image/jpeg, image/jpg, image/webp" badge="Opcional"
            type="file" wire:model="attachments" multiple />

        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            @if ($product)
                @foreach ($product->images as $img)
                    @if (in_array($img->id, $visibleImages))
                        <div class="relative">
                            <div class="absolute top-2 right-2">
                                <flux:button variant="ghost" icon="x-mark"
                                    wire:click="removeImage('db', {{ $img->id }})" />
                            </div>
                            <img class="w-full" src="{{ Storage::url($img->path) }}" alt="{{ $img->alt_text }}">
                        </div>
                    @endif
                @endforeach
            @endif

            @foreach ($attachments as $index => $img)
                <div class="relative">
                    <div class="absolute top-2 right-2">
                        <flux:button variant="ghost" icon="x-mark"
                            wire:click="removeImage('temp', {{ $index }})" />
                    </div>
                    <img class="w-full" src="{{ $img->temporaryUrl() }}">
                </div>
            @endforeach
        </div>

        <flux:switch label="En stock" wire:model.live="in_stock" />

        <div class="flex gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="ghost">Cancelar</flux:button>
            </flux:modal.close>

            <flux:button variant="primary" type="submit">Actualizar</flux:button>
        </div>
    </flux:modal>
</form>
