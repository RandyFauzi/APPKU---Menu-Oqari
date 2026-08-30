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
            $table->string('gobiz_outlet_id')->nullable()->after('whatsapp_number');
            $table->text('gobiz_access_token')->nullable()->after('gobiz_outlet_id');
            $table->text('gobiz_refresh_token')->nullable()->after('gobiz_access_token');
            $table->timestamp('gobiz_token_expires_at')->nullable()->after('gobiz_refresh_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn([
                'gobiz_outlet_id',
                'gobiz_access_token',
                'gobiz_refresh_token',
                'gobiz_token_expires_at',
            ]);
        });
    }
};
