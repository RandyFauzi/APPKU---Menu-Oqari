<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop old columns if not needed, but SQLite alter table drop column is supported in recent Laravel with DBAL
            $table->dropColumn(['status', 'total_price']);
            
            // New Finance Columns
            $table->integer('subtotal')->default(0);
            $table->integer('discount_amount')->default(0);
            $table->integer('tax_amount')->default(0);
            $table->integer('service_charge_amount')->default(0);
            $table->integer('rounding')->default(0);
            $table->integer('grand_total')->default(0);
            $table->integer('amount_paid')->default(0);
            $table->integer('change_amount')->default(0);

            // Statuses and Types
            $table->string('order_status')->default('DRAFT'); // DRAFT, CONFIRMED, PREPARING, READY, SERVED, COMPLETED, CANCELLED
            $table->string('payment_status')->default('UNPAID'); // UNPAID, PENDING, PAID, FAILED, REFUNDED, PARTIALLY_REFUNDED
            $table->string('fulfillment_type')->default('DINE_IN'); // DINE_IN, TAKEAWAY, DELIVERY
            
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal', 'discount_amount', 'tax_amount', 'service_charge_amount', 'rounding',
                'grand_total', 'amount_paid', 'change_amount', 'order_status', 'payment_status',
                'fulfillment_type', 'payment_reference', 'paid_at'
            ]);
            $table->string('status')->default('Masuk');
            $table->integer('total_price')->default(0);
        });
    }
};
