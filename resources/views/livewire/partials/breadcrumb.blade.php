<flux:breadcrumbs class="my-6">
    <flux:breadcrumbs.item href="{{ route('home') }}" wire:navigate>Inicio</flux:breadcrumbs.item>

    @isset($category)
        @if ($category->parent)
            <flux:breadcrumbs.item href="{{ route('categories.show', $category->parent) }}" wire:navigate>
                {{ Str::ucfirst($category->parent->name) }}
            </flux:breadcrumbs.item>

            <flux:breadcrumbs.item>
                {{ Str::ucfirst($category->name) }}
            </flux:breadcrumbs.item>
        @else
            <flux:breadcrumbs.item>
                {{ Str::ucfirst($category->name) }}
            </flux:breadcrumbs.item>
        @endif
    @endisset

    @isset($product)
        @if ($product->category->parent)
            <flux:breadcrumbs.item href="{{ route('categories.show', $product->category->parent) }}" wire:navigate>
                {{ Str::ucfirst($product->category->parent->name) }}
            </flux:breadcrumbs.item>
        @endif

        <flux:breadcrumbs.item href="{{ route('categories.show', $product->category) }}" wire:navigate>
            {{ Str::ucfirst($product->category->name) }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>
            {{ Str::ucfirst($product->name) }}
        </flux:breadcrumbs.item>
    @endisset
</flux:breadcrumbs>
