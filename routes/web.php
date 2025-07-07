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
Volt::route('checkout/success', 'checkout.success')->name('checkout.success');
Volt::route('checkout/failure', 'checkout.failure')->name('checkout.failure');
Volt::route('checkout/pending', 'checkout.pending')->name('checkout.pending');

Volt::route('productos/{product:slug}', 'products.show')->name('products.show');
Volt::route('categorias/{category:slug}', 'categories.show')->name('categories.show');

require __DIR__ . '/auth.php';
