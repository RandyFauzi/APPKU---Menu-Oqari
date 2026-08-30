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
            $table->boolean('is_open')->default(true);
            $table->string('slogan')->nullable();
            $table->string('font_family')->default('poppins');
            $table->string('instagram_link')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('maps_link')->nullable();
            $table->json('banners')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn([
                'is_open', 'slogan', 'font_family', 'instagram_link',
                'whatsapp_number', 'maps_link', 'banners',
            ]);
        });
    }
};
