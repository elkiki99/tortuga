<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item :current="request()->routeIs('settings.profile')" :href="route('settings.profile')"
                wire:navigate>{{ __('Perfil') }}</flux:navlist.item>
            <flux:navlist.item :current="request()->routeIs('settings.password')" :href="route('settings.password')"
                wire:navigate>{{ __('Contraseña') }}</flux:navlist.item>
            <flux:navlist.item :current="request()->routeIs('settings.appearance')" :href="route('settings.appearance')"
                wire:navigate>{{ __('Apariencia') }}</flux:navlist.item>

            @if (Auth::check() && !Auth::user()->isAdmin())
                <flux:navlist.item :current="request()->routeIs('orders.user')" :href="route('orders.user')"
                    wire:navigate>{{ __('Mis pedidos') }}</flux:navlist.item>
            @endif
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full @if (request()->routeIs('orders.user')) max-w-none @else max-w-lg @endif">
            {{ $slot }}
        </div>
    </div>
</div>
