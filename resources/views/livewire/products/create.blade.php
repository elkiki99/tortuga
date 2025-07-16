<?php

use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;

new class extends Component {
    use WithFileUploads;

    public $name;
    public $description;
    public $price;
    public $discount_price;
    public $size;
    public $in_stock = true;
    public $category_id;
    public $brand_id;

    public $featured_image;
    public $attachments = [];

    public function createProduct()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1023',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lte:price',
            'size' => 'nullable|string|max:50',
            'in_stock' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'featured_image' => 'required|image|max:2048|mimes:png,jpg,jpeg,webp',
            'attachments.*' => 'nullable|image|max:2048|mimes:png,jpg,jpeg,webp',
        ]);

        $product = Product::create([
            'name' => $this->name,
            'slug' => \Str::slug($this->name),
            'description' => $this->description,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'size' => $this->size,
            'in_stock' => $this->in_stock,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
        ]);

        if ($this->featured_image) {
            $featuredPath = $this->featured_image->store('products', 'public');

            $product->images()->create([
                'path' => $featuredPath,
                'alt_text' => $this->name . '_featured',
                'is_featured' => true,
            ]);
        }

        foreach ($this->attachments as $key => $attachment) {
            $path = $attachment->store('products', 'public');
            $product->images()->create([
                'path' => $path,
                'alt_text' => $this->name . '_' . ($key + 1),
                'is_featured' => false,
            ]);
        }

        Flux::modals()->close();

        $this->reset(['name', 'description', 'price', 'discount_price', 'size', 'in_stock', 'category_id', 'brand_id']);
        $this->reset(['featured_image', 'attachments']);

        $this->dispatch('productCreated');

        Flux::toast(heading: 'Producto creado', text: 'El producto fue creado exitosamente', variant: 'success');
    }

    public function removeTempImage($index)
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    #[Computed]
    public function categories()
    {
        return Category::query()
                ->whereNotNull('categories.parent_id')
                ->join('categories as parents', 'categories.parent_id', '=', 'parents.id')
                ->orderBy('parents.name')
                ->select('categories.*')
                ->get();
    }

    #[Computed]
    public function brands()
    {
        return Brand::all();
    }
}; ?>

<flux:modal name="create-product" class="md:w-auto">
    <form wire:submit.prevent="createProduct" class="space-y-6">
        <div>
            <flux:heading size="lg">Nuevo producto</flux:heading>
            <flux:text class="mt-2">Agrega un nuevo producto a tu tienda</flux:text>
        </div>

        <flux:input label="Nombre" placeholder="Nombre del producto" wire:model="name" required autofocus />

        <flux:textarea label="Descripción" badge="Opcional" placeholder="Descripción del producto"
            wire:model="description" rows="3" />

        <flux:field>
            <flux:label>Precio</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>UYU</flux:input.group.prefix>
                <flux:input type="number" wire:model="price" required min="0" placeholder="730" />
            </flux:input.group>
            <flux:error name="price" />
        </flux:field>

        <flux:field>
            <flux:label badge="Opcional">Precio final</flux:label>
            <flux:input.group>
                <flux:input.group.prefix>UYU</flux:input.group.prefix>
                <flux:input type="number" wire:model="discount_price" min="0" placeholder="550" />
            </flux:input.group>
            <flux:error name="discount_price" />
        </flux:field>

        <flux:input label="Tamaño" badge="Opcional" placeholder="XS" wire:model="size" />

        <flux:select wire:model="category_id" required label="Categoría" variant="listbox" searchable
            placeholder="Selecciona una categoría">
            @forelse ($this->categories as $category)
                <flux:select.option value="{{ $category->id }}">{{ $category->parent->name }} - {{ $category->name }}</flux:select.option>
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

        <flux:input label="Imagen destacada" type="file" wire:model="featured_image" required />

        @if ($featured_image)
            <img src="{{ $featured_image->temporaryUrl() }}">
        @endif

        <flux:input label="Imágenes" badge="Opcional" type="file" wire:model="attachments" multiple />

        @if (count($attachments) > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @foreach ($attachments as $index => $attachment)
                    <div class="relative">
                        <div class="absolute top-2 right-2">
                            <flux:button variant="ghost" icon="x-mark"
                                wire:click="removeTempImage({{ $index }})" />
                        </div>
                        <img src="{{ $attachment->temporaryUrl() }}" class="w-full">
                    </div>
                @endforeach
            </div>
        @endif

        <flux:switch label="En stock" wire:model.live="in_stock" checked="true" />

        <div class="flex justify-end gap-2">
            <flux:button type="button" variant="ghost" x-on:click="$flux.modals().close()">Cancelar</flux:button>
            <flux:button type="submit" variant="primary">Guardar producto</flux:button>
        </div>
    </form>
</flux:modal>
