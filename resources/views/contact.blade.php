<x-layouts.app :title="__('Contacto • Tortuga')">
    <div class="container mx-auto mt-6 mb-12 mx-4 md:px-6 lg:px-8">
        <div class="space-y-6">
            <div>
                <flux:heading size="xl" level="1">
                    Contacto
                </flux:heading>

                <flux:subheading level="1" size="lg" class="mb-6">
                    ¿Tienes alguna pregunta o sugerencia?
                </flux:subheading>
            </div>

            <flux:separator variant="subtle" />

            <livewire:partials.contact-form />
        </div>
    </div>
</x-layouts.app>
