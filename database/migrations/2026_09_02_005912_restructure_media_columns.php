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
            $table->renameColumn('logo_url', 'logo_path');
            // 'banners' is a JSON column, we can leave it as banners or rename to banner_paths.
            // Let's rename to banner_paths to be consistent with the user's naming convention.
            $table->renameColumn('banners', 'banner_paths');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('image_url', 'image_path');
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->renameColumn('logo_path', 'logo_url');
            $table->renameColumn('banner_paths', 'banners');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('image_path', 'image_url');
        });
    }
};
