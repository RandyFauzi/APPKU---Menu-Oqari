<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_register_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_register_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Kasir yang buka
            
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            
            $table->integer('opening_cash')->default(0); // Modal awal
            $table->integer('expected_cash')->default(0); // Uang yang seharusnya ada di laci
            $table->integer('actual_cash')->nullable(); // Uang fisik yang dihitung saat tutup
            $table->integer('difference')->nullable(); // actual - expected
            
            // Total dari berbagai metode pembayaran selama sesi ini
            $table->integer('total_cash_sales')->default(0);
            $table->integer('total_qris_sales')->default(0);
            $table->integer('total_other_sales')->default(0); // Debit, dll
            
            $table->string('status')->default('OPEN'); // OPEN, CLOSED
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_register_sessions');
    }
};
