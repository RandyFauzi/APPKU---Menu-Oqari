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
            if (! Schema::hasColumn('shops', 'receipt_settings')) {
                $table->json('receipt_settings')->nullable()->after('receipt_footer');
            }
            if (! Schema::hasColumn('shops', 'printer_settings')) {
                $table->json('printer_settings')->nullable()->after('receipt_settings');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['receipt_settings', 'printer_settings']);
        });
    }
};
