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
            if (! Schema::hasColumn('shops', 'business_type')) {
                $table->string('business_type')->nullable();
            }
            if (! Schema::hasColumn('shops', 'sales_modes')) {
                $table->json('sales_modes')->nullable();
            }
            if (! Schema::hasColumn('shops', 'onboarding_status')) {
                $table->string('onboarding_status')->default('pending'); // pending, in_progress, completed
            }
            if (! Schema::hasColumn('shops', 'onboarding_step')) {
                $table->string('onboarding_step')->default('welcome');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['business_type', 'sales_modes', 'onboarding_status', 'onboarding_step'] as $col) {
                if (Schema::hasColumn('shops', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
