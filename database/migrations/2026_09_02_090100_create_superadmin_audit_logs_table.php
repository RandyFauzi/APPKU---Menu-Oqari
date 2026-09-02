<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('superadmin_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->constrained('users')->cascadeOnDelete();
            $table->string('action'); // e.g. shop.suspended, shop.activated, shop.deleted, user.deleted
            $table->string('target_type'); // e.g. Shop, User
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('target_label')->nullable(); // snapshot name, in case target is deleted later
            $table->json('meta')->nullable(); // reason, before/after values, etc.
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('superadmin_audit_logs');
    }
};
