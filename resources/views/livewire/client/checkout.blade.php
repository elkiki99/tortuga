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

    public function mount()
    {
        if (Auth::check()) {
            $cart = Auth::user()->cart;
            if ($cart) {
                $this->items = $cart->items()->with('product')->get();
                $this->total = $this->items->sum(fn($item) => $item->product->price);
            } else {
                $this->items = [];
                $this->total = 0;
            }
        } else {
            $this->items = session('cart', []);
            $productIds = collect($this->items)->pluck('product_id')->unique()->all();

            $products = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');

            $this->items = collect($this->items)
                ->map(function ($item) use ($products) {
                    $item['product'] = $products[$item['product_id']] ?? null;
                    return $item;
                })
                ->filter(fn($item) => $item['product']);

            $this->total = $this->items->sum(fn($item) => $item['product']->price);
        }

        $this->createPreference();
    }

    public function createPreference()
    {
        $items = [];

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
                        'unit_price' => floatval($item->product->price),
                    ];
                }
            }
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
                    'unit_price' => floatval($product->price),
                ];
            }
        }

        $payer = [
            'name' => Auth::check() ? Auth::user()->name : null,
            'email' => Auth::check() ? Auth::user()->email : null,
        ];

        $mp = new MercadoPagoService();
        $preference = $mp->createPreference($items, $payer);

        if ($preference) {
            $this->preferenceId = $preference->id;
        }
    }
}; ?>

<section class="container mx-auto px-4 mt-6 md:px-6 lg:px-8 my-12">
    <flux:breadcrumbs class="my-6">
        <flux:breadcrumbs.item href="{{ route('home') }}" wire:navigate>Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>Checkout</flux:breadcrumbs.item>
    </flux:breadcrumbs>

    <flux:heading size="xl">Checkout</flux:heading>

    <div class="flex gap-12">
        <div class="flex flex-col h-full w-1/2 mt-6">
            <div class="space-y-6 flex-grow flex flex-col overflow-y-auto py-4 py-2">
                @forelse($items as $item)
                    @auth
                        <div wire:key="item-{{ $item->product->id }}" class="flex items-center justify-between">
                            <div class="flex items-start gap-4">
                                <a href="{{ route('products.show', $item->product->slug) }}" wire:navigate
                                    class="block w-full aspect-square object-cover bg-gray-100">
                                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                                        class="w-16 h-16 object-cover">
                                </a>

                                <div>
                                    <flux:heading>{{ Str::ucfirst($item->product->name) }}</flux:heading>
                                    <flux:subheading>${{ $item->product->price }}UYU</flux:subheading>
                                </div>
                            </div>
                        </div>
                        <flux:separator />
                    @else
                        <div wire:key="item-{{ $item['product_id'] }}" class="flex items-center justify-between">
                            @if ($item['product'])
                                <div class="flex items-start gap-4">
                                    <a href="{{ route('products.show', $item['product']->slug) }}" wire:navigate
                                        class="block w-full aspect-square object-cover bg-gray-100">
                                        <img src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}"
                                            class="w-16 h-16 object-cover">
                                    </a>
                                    <div>
                                        <flux:heading>{{ Str::ucfirst($item['product']->name) }}</flux:heading>
                                        <flux:subheading>${{ $item['product']->price }}UYU</flux:subheading>
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
                        <flux:heading size="lg">Total</flux:heading>
                        <flux:heading size="lg">${{ number_format($total, 2) }}UYU</flux:heading>
                    </div>
                </footer>
            @endif
{{-- 
            @if (!session()->has('guest_checkout_data'))
                <div class="mt-6 pointer-events-none opacity-50" id="walletBrick_container"></div>
            @else --}}
                <div class="mt-6" id="walletBrick_container"></div>
            {{-- @endif --}}
        </div>

        {{-- @guest
            @if (!session()->has('guest_checkout_data'))
                <livewire:partials.guest-checkout-form />
            @endif
        @endguest --}}
    </div>
</section>

@script
    <script>
        const publicKey = "{{ config('services.mercadopago.public_key') }}";
        const preferenceId = "{{ $preferenceId }}";

        let bricksBuilder = null;

        async function renderWalletBrick() {
            const containerId = "walletBrick_container";
            const container = document.getElementById(containerId);
            if (container.hasChildNodes()) {
                return;
            }

            if (!preferenceId) {
                console.warn('No preferenceId available, cannot render MercadoPago wallet brick.');
                return;
            }

            const mp = new MercadoPago(publicKey);
            bricksBuilder = mp.bricks();

            await bricksBuilder.create("wallet", containerId, {
                initialization: {
                    preferenceId: preferenceId,
                },
            });
        }

        document.addEventListener("livewire:navigated", () => {
            renderWalletBrick();
        }, {
            once: true
        });
    </script>
@endscript
