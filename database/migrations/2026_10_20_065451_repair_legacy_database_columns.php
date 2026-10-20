<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'users' => [
                'permissions' => fn (Blueprint $table) => $table->json('permissions')->nullable(),
                'is_active' => fn (Blueprint $table) => $table->boolean('is_active')->default(true),
                'account_version' => fn (Blueprint $table) => $table->unsignedInteger('account_version')->default(0),
            ],
            'products' => [
                'image_path' => fn (Blueprint $table) => $table->string('image_path')->nullable(),
                'deleted_at' => fn (Blueprint $table) => $table->timestamp('deleted_at')->nullable(),
                'hpp_overhead_cost' => fn (Blueprint $table) => $table->decimal('hpp_overhead_cost', 10, 2)->default(0),
                'hpp_target_margin' => fn (Blueprint $table) => $table->decimal('hpp_target_margin', 5, 2)->nullable(),
                'hpp_settings' => fn (Blueprint $table) => $table->json('hpp_settings')->nullable(),
            ],
            'product_variants' => ['cogs_adjustment' => fn (Blueprint $table) => $table->decimal('cogs_adjustment', 12, 2)->default(0)],
            'modifiers' => ['cogs_adjustment' => fn (Blueprint $table) => $table->decimal('cogs_adjustment', 12, 2)->default(0)],
            'shops' => [
                'logo_path' => fn (Blueprint $table) => $table->string('logo_path')->nullable(),
                'banner_paths' => fn (Blueprint $table) => $table->json('banner_paths')->nullable(),
            ],
            'payments' => [
                'shop_id' => fn (Blueprint $table) => $table->foreignId('shop_id')->nullable()->index(),
                'reference_number' => fn (Blueprint $table) => $table->string('reference_number')->nullable(),
                'cancelled_at' => fn (Blueprint $table) => $table->timestamp('cancelled_at')->nullable(),
                'cancelled_by' => fn (Blueprint $table) => $table->unsignedBigInteger('cancelled_by')->nullable(),
                'cancellation_reason' => fn (Blueprint $table) => $table->string('cancellation_reason')->nullable(),
            ],
            'cash_drawer_transactions' => [
                'payment_id' => fn (Blueprint $table) => $table->unsignedBigInteger('payment_id')->nullable()->index(),
                'cancelled_at' => fn (Blueprint $table) => $table->timestamp('cancelled_at')->nullable(),
                'cancelled_by' => fn (Blueprint $table) => $table->unsignedBigInteger('cancelled_by')->nullable(),
                'cancellation_reason' => fn (Blueprint $table) => $table->string('cancellation_reason')->nullable(),
            ],
            'product_recipes' => [
                'batch_amount' => fn (Blueprint $table) => $table->decimal('batch_amount', 18, 6)->nullable(),
                'notes' => fn (Blueprint $table) => $table->string('notes', 500)->nullable(),
            ],
            'stock_mutations' => ['unit' => fn (Blueprint $table) => $table->string('unit', 50)->nullable()],
            'activity_logs' => [
                'entity_type' => fn (Blueprint $table) => $table->string('entity_type')->nullable(),
                'entity_id' => fn (Blueprint $table) => $table->unsignedBigInteger('entity_id')->nullable(),
                'old_values' => fn (Blueprint $table) => $table->json('old_values')->nullable(),
                'new_values' => fn (Blueprint $table) => $table->json('new_values')->nullable(),
                'user_agent' => fn (Blueprint $table) => $table->string('user_agent')->nullable(),
            ],
        ];
        foreach ($columns as $name => $definitions) {
            if (! Schema::hasTable($name)) {
                continue;
            }
            foreach ($definitions as $column => $definition) {
                if (! Schema::hasColumn($name, $column)) {
                    Schema::table($name, $definition);
                }
            }
        }
        foreach (['products' => ['image_url', 'image_path'], 'shops' => ['logo_url', 'logo_path']] as $name => [$old, $new]) {
            if (Schema::hasColumns($name, [$old, $new])) {
                DB::table($name)->whereNull($new)->select(['id', $old])->chunkById(500, function ($rows) use ($name, $old, $new): void {
                    foreach ($rows as $row) {
                        DB::table($name)->where('id', $row->id)->whereNull($new)->update([$new => $row->{$old}]);
                    }
                });
            }
        }
        if (Schema::hasColumns('shops', ['banners', 'banner_paths'])) {
            DB::table('shops')->whereNull('banner_paths')->select(['id', 'banners'])->chunkById(500, function ($rows): void {
                foreach ($rows as $row) {
                    DB::table('shops')->where('id', $row->id)->whereNull('banner_paths')->update(['banner_paths' => $row->banners]);
                }
            });
        }
        if (Schema::hasColumns('payments', ['shop_id', 'order_id'])) {
            DB::table('payments')->whereNull('shop_id')->select(['id', 'order_id'])->chunkById(500, function ($rows): void {
                $shops = DB::table('orders')->whereIn('id', $rows->pluck('order_id'))->pluck('shop_id', 'id');
                foreach ($rows as $row) {
                    if (isset($shops[$row->order_id])) {
                        DB::table('payments')->where('id', $row->id)->whereNull('shop_id')->update(['shop_id' => $shops[$row->order_id]]);
                    }
                }
            });
        }
    }

    /** Forward-only: rollback must not drop columns or values that existed before this repair. */
    public function down(): void
    {
        throw new RuntimeException('Legacy schema repair is forward-only; restore a verified backup to reverse it.');
    }
};
