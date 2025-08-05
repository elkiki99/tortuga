<?php

use Livewire\Attributes\{Layout, Title, On};
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\OrderPurchased;
use Livewire\Volt\Component;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stats;
use App\Models\Order;
use Carbon\Carbon;

new #[Layout('components.layouts.blank')] #[Title('Éxito • Tortuga')] class extends Component {
    public $order;
    public $items = [];
    public $purchaseId;

    public function updateStats(): void
    {
        if (!$this->order) {
            return;
        }

        $date = now()->startOfDay()->toDateTimeString();

        $stats = Stats::where('date', $date)->first();

        if (!$stats) {
            $stats = Stats::create([
                'date' => $date,
                'orders_count' => 0,
                'total_revenue' => 0,
            ]);
        }

        $stats->increment('orders_count');
        $stats->increment('total_revenue', $this->order->total);
    }

    #[On('orderUpdated')]
    public function refreshPage()
    {   
        $this->dispatch('$refresh');
    }

    public function mount()
    {
        $this->purchaseId = request()->query('payment_id');

        if (!$this->purchaseId) {
            abort(404);
        }

        $existingOrder = Order::where('purchase_id', $this->purchaseId)->first();

        if ($existingOrder) {
            $this->order = $existingOrder;
            $this->authorize('view', $this->order);
            $this->items = $existingOrder->items()->with('product')->get();
            return;
        }

        if (Auth::check()) {
            $user = Auth::user();
            $cart = $user->cart;

            if ($cart && $cart->items->isNotEmpty()) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'buyer_name' => $user->name,
                    'buyer_email' => $user->email,
                    'purchase_id' => $this->purchaseId,
                    'purchase_date' => Carbon::now(),
                    'total' => $cart->items->sum(fn($item) => $item->product->discount_price != null ? $item->product->discount_price : $item->product->price),
                    'status' => 'payed',
                    'payment_method' => 'mercadopago',
                ]);

                $itemsForEmail = [];

                foreach ($cart->items as $item) {
                    if ($item->product) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $item->product->id,
                            'price' => $item->product->discount_price != null ? $item->product->discount_price : $item->product->price,
                        ]);

                        $item->product->update(['in_stock' => false]);

                        $itemsForEmail[] = [
                            'name' => $item->product->name,
                            'price' => $item->product->discount_price != null ? $item->product->discount_price : $item->product->price,
                        ];
                    }
                }

                Mail::to($user->email)->send(new OrderPurchased(name: $user->name, purchaseId: $this->purchaseId, items: $itemsForEmail, total: $order->total));

                $cart->items()->delete();

                $this->order = $order;
                $this->items = $order->items()->with('product')->get();
            }
        } else {
            $cartItems = session('cart', []);
            $productIds = collect($cartItems)->pluck('product_id');
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            if ($products->isEmpty()) {
                abort(404);
            }

            $total = $products->sum(fn($p) => $p->discount_price != null ? p->discount_price : $p->price);

            $order = Order::create([
                'user_id' => null,
                'buyer_name' => session('guest.name'),
                'buyer_email' => session('guest.email'),
                'purchase_id' => $this->purchaseId,
                'purchase_date' => Carbon::now(),
                'total' => $total,
                'status' => 'payed',
                'payment_method' => 'mercadopago',
            ]);

            $itemsForEmail = [];

            foreach ($cartItems as $item) {
                $product = $products[$item['product_id']] ?? null;

                if ($product) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'price' => $product->discount_price != null ? $product->discount_price : $product->price,
                    ]);

                    $product->update(['in_stock' => false]);

                    $itemsForEmail[] = [
                        'name' => $product->name,
                        'price' => $product->discount_price != null ? $product->discount_price : $product->price,
                    ];
                }
            }

            Mail::to(session('guest.email'))->send(new OrderPurchased(name: session('guest.name') . ' ' . session('guest.surname'), purchaseId: $this->purchaseId, items: $itemsForEmail, total: $order->total));

            session()->forget(['cart', 'guest.name', 'guest.surname', 'guest.email']);

            $this->order = $order;
            $this->items = $order->items()->with('product')->get();
        }

        $this->updateStats();
    }
}; ?>

<section class="container mx-auto px-4 mt-6 md:px-6 lg:px-8 my-12 space-y-6">
    <flux:text size="xs">
        @can('delete', $order)
            <flux:link variant="subtle" href="{{ route('orders.index') }}" wire:navigate>
                <flux:icon.arrow-left variant="micro" class="mr-1 mb-0.5 inline-block" />
                Volver a pedidos
            </flux:link>
        @else
            <flux:link variant="subtle" href="{{ route('home') }}" wire:navigate>
                <flux:icon.arrow-left variant="micro" class="mr-1 mb-0.5 inline-block" />
                Volver al inicio
            </flux:link>
        @endcan
    </flux:text>

    <div>
        @if (Auth()->check() && Auth::user()->isAdmin())
            <div class="flex items-center gap-4">
                <flux:heading size="xl" level="1">Pago exitoso confirmado</flux:heading>

                @can('edit', $order)
                    <flux:button wire:click="$dispatch('editOrder', { id: {{ $order->id }} })"
                        icon="pencil" size="sm" variant="ghost" />

                    <!-- Update category modal -->
                    <livewire:orders.edit />
                @endcan
            </div>
            <flux:subheading class="mt-2">
                Este pago fue procesado correctamente y está asociado a la orden de compra
                <strong>{{ $this->purchaseId }}</strong>.
            </flux:subheading>
        @else
            <flux:heading size="xl">¡Gracias por tu compra!</flux:heading>
            <flux:subheading>
                Tu pago fue procesado correctamente.
            </flux:subheading>
        @endif

        @php
            $statusMap = [
                'pending' => [
                    'color' => 'yellow',
                    'icon' => 'clock',
                    'label' => 'Pendiente',
                ],
                'payed' => [
                    'color' => 'blue',
                    'icon' => 'currency-dollar',
                    'label' => 'Pago',
                ],
                'cancelled' => [
                    'color' => 'red',
                    'icon' => 'x-circle',
                    'label' => 'Cancelado',
                ],
                'delivered' => [
                    'color' => 'green',
                    'icon' => 'check-circle',
                    'label' => 'Entregado',
                ],
            ];

            $status = $order->status;
            $config = $statusMap[$status] ?? $statusMap['pending'];
        @endphp

        <flux:badge class="mt-4" variant="pill" color="{{ $config['color'] }}" icon="{{ $config['icon'] }}"
            size="sm" inset="top bottom">
            <span class="hidden sm:inline">{{ $config['label'] }}</span>
        </flux:badge>
    </div>

    <div class="lg:flex lg:items-start lg:gap-6">
        <div class="space-y-6 w-full lg:w-1/2">
            <div class="space-y-4 flex-1 flex flex-col py-4">
                @forelse ($items as $item)
                    <div class="flex items-center justify-between">
                        <div class="flex items-start gap-4">
                            <div class="block w-24 h-24 aspect-square object-cover bg-gray-100">
                                <img src="{{ Storage::url($item->product->featuredImage->path ?? '') }}"
                                    alt="{{ $item->product->name }}" class="w-16 h-16 object-cover">
                            </div>

                            @php
                                $price =
                                    $item->product->discount_price != null
                                        ? $item->product->discount_price
                                        : $item->product->price;
                            @endphp

                            <div>
                                <flux:heading>{{ Str::ucfirst($item->product->name) }}</flux:heading>
                                <flux:subheading>${{ number_format($price, 2, ',', '.') }}&nbsp;UYU</flux:subheading>
                            </div>
                        </div>
                    </div>
                    <flux:separator />
                @empty
                @endforelse

                <div class="flex items-center justify-between">
                    <flux:subheading size="lg">Total</flux:subheading>
                    <flux:heading size="lg">${{ number_format($order->total, 2, ',', '.') }}&nbsp;UYU
                    </flux:heading>
                </div>
            </div>

            @if (!Auth::check() || !Auth::user()->isAdmin())
                <div class="flex">
                    <flux:spacer />

                    <flux:text size="xs">
                        <flux:link target="_blank" rel="noopener noreferrer"
                            href="https://www.mercadopago.com.uy/tools/receipt-view/{{ $purchaseId }}">
                            Ver comprobante
                            <flux:icon.arrow-right variant="micro" class="ml-1 mb-0.5 inline-block" />
                        </flux:link>
                    </flux:text>
                </div>
            @endif

            @auth
                @if (Auth::user()->isAdmin())
                    <div class="mt-6">
                        <flux:modal.trigger name="delete-order">
                            <flux:badge as="button" color="red" icon="trash">Eliminar pedido</flux:button>
                        </flux:modal.trigger>

                        <livewire:orders.delete :$order wire:key="delete-order" />
                    </div>
                @endif
            @endauth
        </div>

        <div class="lg:flex justify-center items-center min-h-[60vh] lg:w-1/2 hidden">
            <flux:icon.shopping-bag variant="solid" class="size-48 dark:text-zinc-700 text-zinc-100" />
        </div>
    </div>
</section>
