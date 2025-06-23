<flux:breadcrumbs class="my-6">
    <flux:breadcrumbs.item href="{{ route('home') }}" wire:navigate>Home</flux:breadcrumbs.item>

    @isset($category)
        @if ($category->parent)
            <flux:breadcrumbs.item href="{{ route('categories.show', $category->parent) }}" wire:navigate>
                {{ Str::ucfirst($category->parent->name) }}
            </flux:breadcrumbs.item>
        @endif

        <flux:breadcrumbs.item href="{{ route('categories.show', $category) }}" wire:navigate>
            {{ Str::ucfirst($category->name) }}
        </flux:breadcrumbs.item>
    @endisset

    @isset($product)
        <flux:breadcrumbs.item href="{{ route('categories.show', $product->category) }}" wire:navigate>
            {{ Str::ucfirst($product->category->name) }}
        </flux:breadcrumbs.item>
        <flux:breadcrumbs.item>
            {{ Str::ucfirst($product->name) }}
        </flux:breadcrumbs.item>
    @endisset
</flux:breadcrumbs>