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
        if (Schema::hasTable('cash_drawer_transactions')) {
            if (! Schema::hasColumn('cash_drawer_transactions', 'shop_id')) {
                try {
                    Schema::table('cash_drawer_transactions', function (Blueprint $table) {
                        $table->foreignId('shop_id')->nullable()->after('id')->constrained('shops')->cascadeOnDelete();
                    });
                } catch (Throwable) {
                    if (! Schema::hasColumn('cash_drawer_transactions', 'shop_id')) {
                        Schema::table('cash_drawer_transactions', function (Blueprint $table) {
                            $table->unsignedBigInteger('shop_id')->nullable()->index()->after('id');
                        });
                    }
                }
            }

            if (! Schema::hasColumn('cash_drawer_transactions', 'user_id')) {
                try {
                    Schema::table('cash_drawer_transactions', function (Blueprint $table) {
                        $table->foreignId('user_id')->nullable()->after('cash_register_session_id')->constrained('users')->nullOnDelete();
                    });
                } catch (Throwable) {
                    if (! Schema::hasColumn('cash_drawer_transactions', 'user_id')) {
                        Schema::table('cash_drawer_transactions', function (Blueprint $table) {
                            $table->unsignedBigInteger('user_id')->nullable()->index()->after('cash_register_session_id');
                        });
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('cash_drawer_transactions') && Schema::hasColumn('cash_drawer_transactions', 'shop_id')) {
            Schema::table('cash_drawer_transactions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('shop_id');
            });
        }
    }
};
