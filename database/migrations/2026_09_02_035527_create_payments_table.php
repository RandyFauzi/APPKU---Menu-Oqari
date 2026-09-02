<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete(); // explicitly linking for tenant isolation
            $table->string('payment_method'); // e.g. QRIS, CASH, DEBIT, CREDIT_CARD
            $table->integer('amount');
            $table->string('status')->default('PENDING'); // PENDING, SUCCESS, FAILED, REFUNDED
            $table->string('reference_id')->nullable(); // External reference like Xendit Invoice ID
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
