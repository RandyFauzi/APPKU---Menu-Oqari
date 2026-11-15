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
        if (Schema::hasTable('cash_drawer_transactions') && ! Schema::hasColumn('cash_drawer_transactions', 'user_id')) {
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('cash_drawer_transactions') && Schema::hasColumn('cash_drawer_transactions', 'user_id')) {
            Schema::table('cash_drawer_transactions', function (Blueprint $table) {
                try {
                    $table->dropConstrainedForeignId('user_id');
                } catch (Throwable) {
                    $table->dropColumn('user_id');
                }
            });
        }
    }
};
