<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->string('public_token')->unique()->after('name')->nullable();
        });

        // Fill existing tables with tokens
        $tables = \App\Models\Table::all();
        foreach ($tables as $t) {
            $t->public_token = strtolower(Str::random(8));
            $t->save();
        }
        
        Schema::table('tables', function (Blueprint $table) {
            $table->string('public_token')->nullable(false)->change();
            $table->dropColumn('qr_code_url');
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->string('qr_code_url')->nullable()->after('public_token');
        });

        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn('public_token');
        });
    }
};
