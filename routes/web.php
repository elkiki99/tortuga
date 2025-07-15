<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('main');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'is_admin'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Volt::route('wishlist', 'client.wishlist')->name('client.wishlist');
});

Route::post('/webhook', WebhookController::class)->name('webhook');

Volt::route('checkout', 'client.checkout')->name('client.checkout');

Volt::route('checkout/exito', 'checkout.success')->name('checkout.success');
Volt::route('checkout/error', 'checkout.failure')->name('checkout.failure');
Volt::route('checkout/pendiente', 'checkout.pending')->name('checkout.pending');

// Route::get('checkout/success', function() {
//     return view('checkout.success');
// })->name('checkout.success');

// Route::get('checkout/pending', function() {
//     return view('checkout.pending');
// })->name('checkout.pending');

// Route::get('checkout/failure', function() {
//     return view('checkout.failure');
// })->name('checkout.failure');

Volt::route('productos/{product:slug}', 'products.show')->name('products.show');
Volt::route('productos', 'products.index')->name('products.index');

Volt::route('categorias/{category:slug}', 'categories.show')->name('categories.show');
Volt::route('categorias', 'categories.index')->name('categories.index');

require __DIR__ . '/auth.php';
