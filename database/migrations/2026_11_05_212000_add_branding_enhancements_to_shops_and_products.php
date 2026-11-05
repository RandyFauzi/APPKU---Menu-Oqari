<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            if (! Schema::hasColumn('shops', 'tiktok_link')) {
                $table->string('tiktok_link')->nullable()->after('instagram_link');
            }
            if (! Schema::hasColumn('shops', 'social_settings')) {
                $table->json('social_settings')->nullable()->after('maps_link');
            }
            if (! Schema::hasColumn('shops', 'background_type')) {
                $table->string('background_type', 30)->default('pattern')->after('theme_style');
            }
            if (! Schema::hasColumn('shops', 'background_pattern')) {
                $table->string('background_pattern', 50)->default('dots')->after('background_type');
            }
            if (! Schema::hasColumn('shops', 'background_image_path')) {
                $table->string('background_image_path')->nullable()->after('background_pattern');
            }
            if (! Schema::hasColumn('shops', 'background_color')) {
                $table->string('background_color', 20)->nullable()->after('background_image_path');
            }
            if (! Schema::hasColumn('shops', 'background_overlay_opacity')) {
                $table->unsignedTinyInteger('background_overlay_opacity')->default(85)->after('background_color');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'is_best_seller')) {
                $table->boolean('is_best_seller')->default(false)->after('is_sold_out');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $cols = ['tiktok_link', 'social_settings', 'background_type', 'background_pattern', 'background_image_path', 'background_color', 'background_overlay_opacity'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('shops', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'is_best_seller')) {
                $table->dropColumn('is_best_seller');
            }
        });
    }
};
