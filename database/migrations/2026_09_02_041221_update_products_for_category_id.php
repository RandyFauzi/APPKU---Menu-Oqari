<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('shop_id')->constrained('categories')->nullOnDelete();
            // Drop old string column if possible. We leave it nullable just in case SQLite complains during drop, 
            // but we can try dropping it.
            $table->dropColumn('category_name');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            $table->string('category_name')->nullable();
        });
    }
};
