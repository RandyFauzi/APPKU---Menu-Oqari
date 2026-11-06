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
        if (! Schema::hasTable('printers')) {
            Schema::create('printers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('location')->default('cashier'); // 'cashier', 'kitchen', 'bar', 'checker'
                $table->string('connection_type')->default('browser'); // 'browser', 'network', 'bluetooth'
                $table->string('ip_address')->nullable();
                $table->integer('port')->default(9100);
                $table->string('paper_width')->default('58mm'); // '58mm', '80mm'
                $table->json('category_ids')->nullable(); // null for all categories
                $table->boolean('is_active')->default(true);
                $table->boolean('auto_print')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printers');
    }
};
