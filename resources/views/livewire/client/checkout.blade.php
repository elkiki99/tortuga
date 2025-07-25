<?php

use Livewire\Attributes\{Layout, Title};
use App\Services\MercadoPagoService;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use App\Models\Product;

new #[Layout('components.layouts.blank')] #[Title('Checkout • Tortuga')] class extends Component {
    public $items = [];
    public $total = 0;
    public $preferenceId;
    public $preferenceHash;

    public $guestName;
    public $guestSurname;
    public $guestEmail;
    public bool $providedInfo = false;

    public function mount()
    {
        if (Auth::check()) {
            $cart = Auth::user()->cart;

            if ($cart) {
                $this->items = $cart->items()->with('product')->get();
                $this->total = $this->items->sum(fn($item) => $item->product->discount_price ?? $item->product->price);
                $this->createPreference();
            } else {
                $this->items = [];
                $this->total = 0;
            }
        } else {
            $this->items = session('cart', []);
            $this->guestName = session('guest.name');
            $this->guestSurname = session('guest.surname');

            $productIds = collect($this->items)->pluck('product_id')->unique()->all();

            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $this->items = collect($this->items)
                ->map(function ($item) use ($products) {
                    $item['product'] = $products[$item['product_id']] ?? null;
                    return $item;
                })
                ->filter(fn($item) => $item['product']);

            $this->total = $this->items->sum(fn($item) => $item['product']->discount_price ?? $item['product']->price);

            $this->providedInfo = session()->has('guest.name') && session()->has('guest.surname') && session()->has('guest.email');

            if ($this->providedInfo && $this->items->isNotEmpty()) {
                $this->createPreference();
            }
        }
    }

    public function saveGuestName()
    {
        $this->validate([
            'guestName' => 'required|string|max:255',
            'guestSurname' => 'required|string|max:255',
            'guestEmail' => 'required|email',
        ]);

        session(['guest.name' => $this->guestName]);
        session(['guest.surname' => $this->guestSurname]);
        session(['guest.email' => $this->guestEmail]);

        $this->providedInfo = true;

        $this->createPreference();

        $this->dispatch('$refresh');
    }

    protected function generatePreferenceHash(array $items, array $payer): string
    {
        return md5(
            json_encode([
                'items' => $items,
                'payer' => $payer,
            ]),
        );
    }

    public function createPreference()
    {
        $items = [];
        $payer = [];

        if (Auth::check()) {
            $cart = Auth::user()->cart;

            if ($cart) {
                foreach ($cart->items()->with('product')->get() as $item) {
                    if (!$item->product) {
                        continue;
                    }

                    $items[] = [
                        'id' => $item->product->id,
                        'title' => $item->product->name,
                        'description' => $item->product->description,
                        'currency_id' => 'UYU',
                        'quantity' => 1,
                        'unit_price' => floatval($item->product->discount_price ?? $item->product->price),
                        'picture_url' => $item->product->featuredImage?->path,
                        'category_id' => 'fashion',
                    ];
                }
            }

            $fullName = trim(Auth::user()->name ?? '');

            $parts = explode(' ', $fullName);
            $lastIndex = count($parts) - 1;

            $name = $lastIndex > 0 ? implode(' ', array_slice($parts, 0, $lastIndex)) : $parts[0] ?? null;
            $surname = $lastIndex > 0 ? $parts[$lastIndex] : null;

            $payer = [
                'name' => $name,
                'surname' => $surname,
                'email' => Auth::user()->email ?? null,
            ];
        } else {
            $cart = session('cart', []);
            foreach ($cart as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    continue;
                }

                $items[] = [
                    'id' => $product->id,
                    'title' => $product->name,
                    'description' => $product->description,
                    'currency_id' => 'UYU',
                    'quantity' => 1,
                    'unit_price' => floatval($product->discount_price ?? $product->price),
                    'picture_url' => $product->featuredImage?->path,
                    'category_id' => 'fashion',
                ];
            }

            $payer = [
                'name' => session('guest.name'),
                'surname' => session('guest.surname'),
                'email' => session('guest.email'),
            ];
        }

        $newHash = $this->generatePreferenceHash($items, $payer);
        if ($this->preferenceHash === $newHash && $this->preferenceId) {
            return;
        }

        $mp = new MercadoPagoService();
        $preference = $mp->createPreference($items, $payer);

        if ($preference) {
            $this->preferenceId = $preference->id;
            $this->preferenceHash = $newHash;
            $this->dispatch('preference-updated', $this->preferenceId);
        }
    }
}; ?>

<section class="container mx-auto px-4 mt-6 md:px-6 lg:px-8 my-12">
    <flux:breadcrumbs class="my-6">
        <flux:breadcrumbs.item href="{{ route('home') }}" wire:navigate>Inicio</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Checkout</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl">Checkout</flux:heading>

    <div class="flex gap-12">
        <div class="flex flex-col h-full w-full lg:w-1/2 mt-6">
            <div class="space-y-4 flex-1 flex flex-col py-4">
                @forelse($items as $item)
                    @auth
                        <div wire:key="item-{{ $item->product->id }}" class="flex items-center justify-between">
                            <div class="flex items-start gap-4">
                                <div class="block w-24 h-24 aspect-square object-cover bg-gray-100">
                                    <img src="{{ Storage::url($item->product->featuredImage->path ?? '') }}" alt="{{ $item->product->name }}"
                                        class="w-16 h-16 object-cover">
                                </div>

                                @php
                                    $price = $item->product->discount_price ?? $item->product->price;
                                @endphp
                                
                                <div>
                                    <flux:heading>{{ Str::ucfirst($item->product->name) }}</flux:heading>
                                    <flux:subheading>${{ number_format($price, 2, ',', '.') }}&nbsp;UYU
                                    </flux:subheading>
                                </div>
                            </div>
                        </div>
                        <flux:separator />
                    @else
                        <div wire:key="item-{{ $item['product_id'] }}" class="flex items-center justify-between">
                            @if ($item['product'])
                                <div class="flex items-start gap-4">
                                    <div class="block w-24 h-24 aspect-square object-cover bg-gray-100">
                                        <img src="{{ Storage::url($item['product']->featuredImage->path ?? '') }}" alt="{{ $item['product']->name }}"
                                            class="w-16 h-16 object-cover">
                                    </div>

                                    @php
                                        $price = $item['product']->discount_price ?? $item['product']->price;
                                    @endphp

                                    <div>
                                        <flux:heading>{{ Str::ucfirst($item['product']->name) }}</flux:heading>
                                        <flux:subheading>${{ number_format($price, 2, ',', '.') }}&nbsp;UYU</flux:subheading>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <flux:separator />
                    @endauth
                @empty
                    <flux:text>No hay productos todavía.
                        <flux:link href="{{ route('home') }}" wire:navigate>Vuelve a la tienda</flux:link>
                    </flux:text>
                @endforelse
            </div>

            @if (count($items) > 0)
                <footer class="space-y-6 mt-auto">
                    <div class="flex items-center justify-between">
                        <flux:subheading size="lg">Total</flux:subheading>
                        <flux:heading size="lg">${{ number_format($total, 2, ',', '.') }}&nbsp;UYU</flux:heading>
                    </div>
                </footer>

                @if (!Auth::check() && $providedInfo == false)
                    <flux:card class="mt-6">
                        <div class="space-y-6">
                            <flux:heading size="lg">Agrega tu nombre y apellido para concluir el pago
                            </flux:heading>

                            <div class="flex items-center gap-4 w-full">
                                <div class="w-1/2">
                                <flux:input required wire:model="guestName" type="text" placeholder="Tu nombre"
                                    label="Nombre" />
                                </div>
                                <div class="w-1/2">
                                <flux:input required wire:model="guestSurname" type="text" placeholder="Tu apellido"
                                    label="Apellido" />
                                </div>
                            </div>

                            <flux:input required wire:model="guestEmail" type="email" placeholder="Tu email"
                                label="Email" />

                            <div class="flex">
                                <flux:spacer />
                                <flux:button variant="primary" wire:click.prevent="saveGuestName"
                                    icon-trailing="chevron-right">
                                    Continuar</flux:button>
                            </div>
                        </div>
                    </flux:card>
                @else
                    <div class="mt-6" id="walletBrick_container"></div>
                @endif
            @endif

        </div>
    </div>
</section>

@script
    <script>
        const publicKey = "{{ config('services.mercadopago.public_key') }}";

        let bricksBuilder = null;

        async function renderWalletBrick(preferenceId) {
            const containerId = "walletBrick_container";

            const mp = new MercadoPago(publicKey);
            bricksBuilder = mp.bricks();

            await bricksBuilder.create("wallet", containerId, {
                initialization: {
                    preferenceId: preferenceId,
                },
            });
        }

        Livewire.on('preference-updated', (preferenceId) => {
            renderWalletBrick(preferenceId);
        });
    </script>
@endscript
