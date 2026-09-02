<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('crew_shifts', function (Blueprint $table) {
            $table->string('position')->nullable()->after('end_time');
            $table->foreignId('shift_template_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('crew_shifts', function (Blueprint $table) {
            $table->dropForeign(['shift_template_id']);
            $table->dropColumn(['shift_template_id', 'position']);
        });
    }
};
