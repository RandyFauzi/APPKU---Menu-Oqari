<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('modifier_groups')) {
            Schema::table('modifier_groups', function (Blueprint $table) {
                if (! Schema::hasColumn('modifier_groups', 'shop_id')) {
                    $table->foreignId('shop_id')->nullable()->after('id')->constrained('shops')->nullOnDelete();
                }
                if (! Schema::hasColumn('modifier_groups', 'selection_type')) {
                    $table->string('selection_type')->default('MULTIPLE')->after('name'); // SINGLE (Radio) or MULTIPLE (Checkbox)
                }
                if (! Schema::hasColumn('modifier_groups', 'category_target')) {
                    $table->string('category_target')->default('all')->nullable()->after('selection_type'); // all, beverage, food
                }
            });

            // Make product_id nullable if it exists and is not nullable
            if (Schema::hasColumn('modifier_groups', 'product_id')) {
                try {
                    Schema::table('modifier_groups', function (Blueprint $table) {
                        $table->unsignedBigInteger('product_id')->nullable()->change();
                    });
                } catch (Throwable $e) {
                    // Ignore on sqlite in tests if change() is not supported
                }
            }
        }

        if (! Schema::hasTable('product_modifier_group')) {
            Schema::create('product_modifier_group', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('modifier_group_id')->constrained('modifier_groups')->cascadeOnDelete();
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['product_id', 'modifier_group_id']);
            });
        }

        // Migrate any existing modifier_groups into pivot table and sync shop_id
        if (Schema::hasTable('modifier_groups') && Schema::hasTable('products')) {
            try {
                $legacyGroups = DB::table('modifier_groups')
                    ->whereNotNull('product_id')
                    ->get();

                foreach ($legacyGroups as $group) {
                    $product = DB::table('products')->where('id', $group->product_id)->first();
                    if ($product) {
                        DB::table('modifier_groups')
                            ->where('id', $group->id)
                            ->update(['shop_id' => $product->shop_id]);

                        DB::table('product_modifier_group')->insertOrIgnore([
                            'product_id' => $group->product_id,
                            'modifier_group_id' => $group->id,
                            'sort_order' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } catch (Throwable $e) {
                // Ignore if tables are empty
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_modifier_group');

        if (Schema::hasTable('modifier_groups')) {
            Schema::table('modifier_groups', function (Blueprint $table) {
                if (Schema::hasColumn('modifier_groups', 'category_target')) {
                    $table->dropColumn('category_target');
                }
                if (Schema::hasColumn('modifier_groups', 'selection_type')) {
                    $table->dropColumn('selection_type');
                }
                if (Schema::hasColumn('modifier_groups', 'shop_id')) {
                    $table->dropConstrainedForeignId('shop_id');
                }
            });
        }
    }
};
