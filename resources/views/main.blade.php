<x-layouts.app :title="__('Tortuga • Second Hand')">
    <!-- Header section -->
    <section class="relative w-full h-[91vh] overflow-hidden">
        <!-- Imagen de fondo -->
        <div class="absolute inset-0 w-full h-full z-0">
            <img src="{{ asset('header-tortuga.jpg') }}" alt="Header Tortuga"
                class="w-full h-full object-cover" />
        </div>

        <!-- Contenido centrado -->
        <div class="absolute inset-0 z-10 flex items-center justify-center">
            <div class="text-center space-y-4 max-w-2xl">
                <flux:heading size="xl" class="!text-white">
                    {{ __('Tortuga Second Hand') }}
                </flux:heading>
                <flux:subheading size="lg" class="!text-white/90">
                    {{ __('Tu second hand online favorita.') }}
                </flux:subheading>
                <div class="flex justify-center space-x-4">
                    <flux:button href="#" class="w-auto max-w-max inline-flex" icon-trailing="arrow-right">
                        NOVEDADES
                    </flux:button>
                    <flux:button variant="primary" href="#" class="w-auto max-w-max inline-flex" icon-trailing="arrow-right">
                        SALE
                    </flux:button>
                </div>
            </div>
        </div>
    </section>

    <!-- Novedades section -->
    <livewire:sections.novedades />
</x-layouts.app>
