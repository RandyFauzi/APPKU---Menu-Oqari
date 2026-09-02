<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crew_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_template_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('position')->nullable(); // e.g. Cashier, Barista, Waiter
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('notes')->nullable();
            
            $table->timestamps();
            
            // Optional: You could add a unique constraint if a user can only have one shift per day per shop
            // $table->unique(['shop_id', 'user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crew_shifts');
    }
};