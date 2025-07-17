<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('buyer_name');
            $table->string('buyer_email');
            $table->string('purchase_id')->unique();
            $table->dateTime('purchase_date');
            $table->decimal('total', 10, 2);
            $table->enum('status', ['pending', 'payed', 'cancelled', 'delivered'])->default('payed');
            $table->enum('payment_method', ['credit_card', 'paypal', 'bank_transfer', 'mercadopago'])->default('mercadopago');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
