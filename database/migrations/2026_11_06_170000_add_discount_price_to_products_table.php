<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'discount_price')) {
                $table->decimal('discount_price', 12, 2)->nullable()->after('price');
            }
            if (! Schema::hasColumn('products', 'is_discount_active')) {
                $table->boolean('is_discount_active')->default(false)->after('discount_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'is_discount_active')) {
                $table->dropColumn('is_discount_active');
            }
            if (Schema::hasColumn('products', 'discount_price')) {
                $table->dropColumn('discount_price');
            }
        });
    }
};
