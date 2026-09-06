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
            $table->string('business_type')->nullable()->after('theme_style');
            $table->json('sales_modes')->nullable()->after('business_type');
            $table->string('onboarding_status')->default('pending')->after('sales_modes'); // pending, in_progress, completed
            $table->string('onboarding_step')->default('welcome')->after('onboarding_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['business_type', 'sales_modes', 'onboarding_status', 'onboarding_step']);
        });
    }
};
