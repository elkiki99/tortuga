@if (Auth::check() && Auth::user()->isAdmin())
    <x-layouts.app.sidebar :title="$title ?? null">
        <flux:main>
            {{ $slot }}
        </flux:main>
    </x-layouts.app.sidebar>
@else
    <x-layouts.app.header :title="$title ?? null">
        <flux:main class="container mx-auto">
            {{ $slot }}
        </flux:main>
    </x-layouts.app.header>
@endif
