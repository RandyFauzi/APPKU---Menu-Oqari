<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table): void {
                if (! Schema::hasColumn('order_items', 'batch_no')) {
                    $table->unsignedInteger('batch_no')->default(1)->after('order_id')->index();
                }
                if (! Schema::hasColumn('order_items', 'status')) {
                    $table->string('status', 30)->default('new')->after('batch_no')->index();
                }
            });

            // Backfill batch_no and status from parent order for historical consistency
            if (Schema::hasColumn('order_items', 'status')) {
                DB::table('order_items')->whereNull('status')->orWhere('status', '')->update(['status' => 'new']);
            }
            if (Schema::hasColumn('order_items', 'batch_no')) {
                DB::table('order_items')->whereNull('batch_no')->orWhere('batch_no', 0)->update(['batch_no' => 1]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table): void {
                if (Schema::hasColumn('order_items', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('order_items', 'batch_no')) {
                    $table->dropColumn('batch_no');
                }
            });
        }
    }
};
