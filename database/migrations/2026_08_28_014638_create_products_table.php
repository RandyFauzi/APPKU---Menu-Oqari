<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            // Replacing category_name with category_id
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            
            $table->string('name');
            $table->text('description')->nullable();
            
            $table->decimal('price', 10, 2);
            $table->decimal('cogs', 10, 2)->default(0)->comment('Cost of Goods Sold');
            
            $table->string('image_url')->nullable();
            $table->boolean('is_sold_out')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};