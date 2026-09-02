<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            // active   = tenant beroperasi normal
            // trial    = belum berbayar, masih masa coba
            // suspended = dinonaktifkan sementara oleh superadmin (login staff toko diblokir, data tetap utuh)
            $table->string('status')->default('active')->after('slug');

            $table->timestamp('trial_ends_at')->nullable()->after('status');
            $table->timestamp('last_active_at')->nullable()->after('trial_ends_at');
            $table->decimal('mrr', 12, 2)->default(0)->after('last_active_at');

            $table->timestamp('suspended_at')->nullable()->after('mrr');
            $table->string('suspended_reason')->nullable()->after('suspended_at');
            $table->foreignId('suspended_by')->nullable()->after('suspended_reason')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropConstrainedForeignId('suspended_by');
            $table->dropColumn([
                'status', 'trial_ends_at', 'last_active_at', 'mrr',
                'suspended_at', 'suspended_reason',
            ]);
        });
    }
};
