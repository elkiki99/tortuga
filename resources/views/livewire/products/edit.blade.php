<?php

use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;

new class extends Component {
    use WithFileUploads;

    public Product $product;

    public $name;
    public $description;
    public $price;
    public $discount_price;
    public $size;
    public $in_stock;
    public $category_id;
    public $brand_id;

    public $featured_image;
    public array $attachments = [];
    public array $imagesToDelete = [];
    public array $visibleImages = [];

    public function mount(Product $product)
    {
        $this->product = $product;

        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->discount_price = $product->discount_price;
        $this->size = $product->size;
        $this->in_stock = (bool) $product->in_stock;
        $this->category_id = $product->category_id;
        $this->brand_id = $product->brand_id;

        $this->visibleImages = $product->images->pluck('id')->toArray();
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
            'discount_price' => $this->discount_price,
            'size' => $this->size,
            'in_stock' => $this->in_stock,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
        ]);

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
                'alt_text' => $this->name . '_' . ($key + 1),
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

        Flux::modals()->close();
        $this->dispatch('productUpdated');

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

    #[Computed]
    public function categories()
    {
        return Category::query()->whereNotNull('categories.parent_id')->join('categories as parents', 'categories.parent_id', '=', 'parents.id')->orderBy('parents.name')->select('categories.*')->get();
    }

    #[Computed]
    public function brands()
    {
        return Brand::all();
    }
}; ?>

<flux:modal name="edit-product-{{ $product->id }}" class="md:w-auto">
    <form wire:submit.prevent="updateProduct" class="space-y-6">
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
            @forelse ($this->categories as $category)
                <flux:select.option value="{{ $category->id }}">{{ $category->parent->name }} - {{ $category->name }}
                </flux:select.option>
            @empty
                <flux:select.option disabled>No hay categorías</flux:select.option>
            @endforelse
        </flux:select>

        <flux:select wire:model="brand_id" label="Marca" badge="Opcional" variant="listbox" searchable
            placeholder="Selecciona una marca">
            @forelse ($this->brands as $brand)
                <flux:select.option value="{{ $brand->id }}">{{ $brand->name }}</flux:select.option>
            @empty
                <flux:select.option disabled>No hay marcas</flux:select.option>
            @endforelse
        </flux:select>

        <flux:input label="Nueva imagen destacada" type="file" wire:model="featured_image" />

        @if ($featured_image)
            <img src="{{ $featured_image->temporaryUrl() }}">
        @else
            @if ($product->featuredImage)
                <img src="{{ Storage::url($product->featuredImage->path) }}"
                    alt="{{ $product->featuredImage->alt_text }}">
            @endif
        @endif

        <flux:input label="Agregar más imágenes" badge="Opcional" type="file" wire:model="attachments" multiple />

        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
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

        <div class="flex justify-end gap-2">
            <flux:button type="button" variant="ghost" x-on:click="$flux.modals().close()">Cancelar</flux:button>
            <flux:button type="submit" variant="primary">Actualizar</flux:button>
        </div>
    </form>
</flux:modal>
