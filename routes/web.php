<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('main');
})->name('home');

Route::middleware(['auth', 'verified', 'is_admin'])->group(function () {
    Volt::route('dashboard', 'dashboard')->name('dashboard');
    Volt::route('productos', 'products.index')->name('products.index');
    Volt::route('categorias', 'categories.index')->name('categories.index');
    Volt::route('marcas', 'brands.index')->name('brands.index');
    Volt::route('pedidos', 'orders.index')->name('orders.index');
    Volt::route('ventas', 'sales')->name('sales');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('ajustes', 'ajustes/perfil');

    Volt::route('ajustes/perfil', 'settings.profile')->name('settings.profile');
    Volt::route('ajustes/contraseña', 'settings.password')->name('settings.password');
    Volt::route('ajustes/apariencia', 'settings.appearance')->name('settings.appearance');

    Volt::route('wishlist', 'client.wishlist')->name('client.wishlist');
});

Volt::route('productos/{product:slug}', 'products.show')->name('products.show');
Volt::route('categorias/{category:slug}', 'categories.show')->name('categories.show');

Route::post('/webhook', WebhookController::class)->name('webhook');

Volt::route('checkout', 'client.checkout')->name('client.checkout')->middleware('can:view,App\Models\Checkout');

Volt::route('checkout/exito', 'client.checkout.success')->name('checkout.success');

Volt::route('ajustes/pedidos', 'orders.user')->name('orders.user');
// Volt::route('checkout/error', 'client.checkout.failure')->name('checkout.failure');
// Volt::route('checkout/pendiente', 'client.checkout.pending')->name('checkout.pending');

require __DIR__ . '/auth.php';
