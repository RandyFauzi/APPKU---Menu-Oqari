<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_register_sessions', function (Blueprint $table): void {
            foreach (['opening_cash', 'expected_cash', 'total_cash_sales', 'total_qris_sales', 'total_other_sales'] as $column) {
                $table->decimal($column, 14, 2)->default(0)->change();
            }
            foreach (['actual_cash', 'difference'] as $column) {
                $table->decimal($column, 14, 2)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        throw new RuntimeException('Nominal kas desimal tidak boleh dikembalikan ke integer karena dapat memotong snapshot keuangan. Gunakan migrasi perbaikan lanjutan.');
    }
};
