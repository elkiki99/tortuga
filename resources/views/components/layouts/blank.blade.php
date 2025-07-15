<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('livewire.partials.head')
</head>

<body class="min-h-screen flex flex-col bg-white dark:bg-zinc-800">
    <div class="flex-1">
        {{ $slot }}
    </div>

    {{-- @include('livewire.partials.footer') --}}

    @persist('toast')
        <flux:toast />
    @endpersist

    @fluxScripts

    <script src="https://sdk.mercadopago.com/js/v2"></script>
</body>

</html>
