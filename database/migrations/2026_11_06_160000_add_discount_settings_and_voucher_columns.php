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
        Schema::table('shops', function (Blueprint $table) {
            if (! Schema::hasColumn('shops', 'discount_settings')) {
                $table->json('discount_settings')->nullable()->after('printer_settings');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'voucher_code')) {
                $table->string('voucher_code', 50)->nullable()->after('discount_amount');
            }
            if (! Schema::hasColumn('orders', 'discount_reason')) {
                $table->string('discount_reason', 255)->nullable()->after('voucher_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            if (Schema::hasColumn('shops', 'discount_settings')) {
                $table->dropColumn('discount_settings');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('orders', 'voucher_code')) {
                $cols[] = 'voucher_code';
            }
            if (Schema::hasColumn('orders', 'discount_reason')) {
                $cols[] = 'discount_reason';
            }
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
