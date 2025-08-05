<footer class="bg-zinc-900 dark:bg-zinc-900 text-white py-6 border-t dark:border-zinc-700">
    <div class="container mx-auto px-4 md:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
            <flux:text variant="subtle">&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos
                reservados.</flux:text>

            <div class="flex space-x-4 rtl:space-x-reverse">
                <flux:link wire:navigate href="{{ route('privacy') }}" class="!text-sm !text-zinc-400 !dark:text-white/50">Política de privacidad
                </flux:link>
                <flux:link wire:navigate href="{{ route('terms') }}" class="!text-sm !text-zinc-400 !dark:text-white/50">Términos de servicio
                </flux:link>
                <flux:link wire:navigate href="{{ route('contact') }}" class="!text-sm !text-zinc-400 !dark:text-white/50">Contacto</flux:link>
            </div>
        </div>
    </div>
</footer>
