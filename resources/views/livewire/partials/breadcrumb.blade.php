<flux:breadcrumbs class="mb-4">
    <flux:breadcrumbs.item href="{{ route('home') }}" wire:navigate>Home</flux:breadcrumbs.item>
    <flux:breadcrumbs.item href="#">{{ $product->category->name }}</flux:breadcrumbs.item>
    <flux:breadcrumbs.item>{{ Str::ucfirst($product->name) }}</flux:breadcrumbs.item>
</flux:breadcrumbs>