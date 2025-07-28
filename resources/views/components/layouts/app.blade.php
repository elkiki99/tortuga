<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('livewire.partials.head')
</head>

<body class="bg-white dark:bg-zinc-800">
    <flux:header container sticky
        class="z-20 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between w-full">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <a href="{{ route('home') }}"
            class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0 max-lg:hidden" wire:navigate>
            <x-app-logo />
        </a>

        <flux:spacer />

        <flux:navbar class="-mb-px max-lg:hidden">
            @foreach (\App\Models\Category::whereNull('parent_id')->get()->all() as $parent)
                @php
                    $children = $parent->children;
                @endphp

                @if ($children->isNotEmpty())
                    <flux:dropdown class="max-lg:hidden" gap="12">
                        <flux:navbar.item icon:trailing="chevron-down">
                            {{ Str::ucfirst($parent->name) }}
                        </flux:navbar.item>
                        <flux:navmenu>
                            @foreach ($children as $child)
                                <flux:navmenu.item href="{{ route('categories.show', $child->slug) }}" wire:navigate>
                                    {{ Str::ucfirst($child->name) }}
                                </flux:navmenu.item>
                            @endforeach
                        </flux:navmenu>
                    </flux:dropdown>
                @else
                    <flux:navbar.item href="{{ route('categories.show', $parent->slug) }}" wire:navigate>
                        {{ Str::ucfirst($parent->name) }}
                    </flux:navbar.item>
                @endif
            @endforeach
        </flux:navbar>

        <flux:spacer />

        <!-- Cart open modal -->
        @can('add', \App\Models\Cart::class)
            <flux:navbar class="!mr-2">
                <livewire:cart.show />
            </flux:navbar>
        @endcan

        @if (Route::has('login'))
            @auth
                <flux:dropdown gap="9" position="top" align="end">
                    <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <span
                                            class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                            {{ auth()->user()->initials() }}
                                        </span>
                                    </span>

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                        <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <flux:menu.radio.group>
                            @if (Auth::user()->isAdmin())
                                <flux:menu.item :href="route('dashboard')" icon="home" wire:navigate>
                                    {{ __('Panel') }}
                                </flux:menu.item>
                            @else
                                <flux:menu.item :href="route('client.wishlist')" icon="heart" wire:navigate>
                                    {{ __('Wishlist') }}
                                </flux:menu.item>
                            @endif
                            <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                                {{ __('Ajustes') }}
                            </flux:menu.item>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                                class="w-full">
                                {{ __('Cerrar sesión') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            @else
                <flux:button size="sm" href="{{ route('login') }}">Login</flux:button>
            @endauth
        @endif
    </flux:header>

    <flux:sidebar stashable sticky
        class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border rtl:border-r-0 rtl:border-l border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('home') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0"
            wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            @forelse (\App\Models\Category::take(6)->get() as $category)
                <flux:navlist.item href="{{ route('categories.show', $category->slug) }}" wire:navigate>
                    {{ Str::ucfirst($category->name) }}
                </flux:navlist.item>
            @empty
                <flux:navlist.item href="#">No hay categorías disponibles</flux:navlist.item>
            @endforelse
        </flux:navlist>
    </flux:sidebar>

    <div class="min-h-[91vh] flex flex-col flex-grow">
        <div class="flex-1">
            {{ $slot }}
        </div>

        @include('livewire.partials.footer')
    </div>

    @persist('toast')
        <flux:toast />
    @endpersist

    @fluxScripts

    <script src="https://sdk.mercadopago.com/js/v2"></script>
</body>

</html>
