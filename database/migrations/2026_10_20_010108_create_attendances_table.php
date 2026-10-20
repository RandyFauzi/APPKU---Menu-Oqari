<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_name');
            $table->foreignId('crew_shift_id')->nullable()->constrained()->nullOnDelete();
            $table->date('work_date');
            $table->string('timezone', 64);
            $table->dateTime('scheduled_start_at')->nullable();
            $table->dateTime('scheduled_end_at')->nullable();
            $table->dateTime('checked_in_at');
            $table->dateTime('checked_out_at')->nullable();
            $table->unsignedInteger('late_minutes')->default(0);
            $table->unsignedInteger('early_leave_minutes')->default(0);
            $table->unsignedInteger('worked_minutes')->nullable();
            $table->string('check_in_note', 500)->nullable();
            $table->string('check_out_note', 500)->nullable();
            $table->timestamps();
            $table->unique(['shop_id', 'user_id', 'work_date']);
            $table->index(['shop_id', 'work_date']);
            $table->index(['shop_id', 'user_id', 'checked_out_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
