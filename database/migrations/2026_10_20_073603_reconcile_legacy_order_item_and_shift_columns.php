<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('order_items', 'unit_price')) {
            if (! Schema::hasColumn('order_items', 'price') && DB::table('order_items')->exists()) {
                throw new RuntimeException('Legacy order_items has no price column. Historical unit prices must be verified before upgrading; no price was fabricated.');
            }
            Schema::table('order_items', fn (Blueprint $table) => $table->decimal('unit_price', 12, 2)->nullable());
        }
        if (Schema::hasColumn('order_items', 'price')) {
            DB::table('order_items')->whereNull('unit_price')->select(['id', 'price'])->chunkById(500, function ($rows): void {
                foreach ($rows as $row) {
                    DB::table('order_items')->where('id', $row->id)->whereNull('unit_price')->update(['unit_price' => $row->price]);
                }
            });
        }
        if (! Schema::hasColumn('order_items', 'cogs_total')) {
            Schema::table('order_items', fn (Blueprint $table) => $table->decimal('cogs_total', 12, 2)->nullable());
        }
        if (! Schema::hasColumn('crew_shifts', 'position')) {
            Schema::table('crew_shifts', fn (Blueprint $table) => $table->string('position')->nullable());
        }
        if (! Schema::hasColumn('crew_shifts', 'shift_template_id')) {
            Schema::table('crew_shifts', fn (Blueprint $table) => $table->foreignId('shift_template_id')->nullable()->constrained()->nullOnDelete());
        }
    }

    /** Forward-only: original price and shift history must remain available. */
    public function down(): void
    {
        throw new RuntimeException('Legacy history reconciliation is forward-only.');
    }
};
