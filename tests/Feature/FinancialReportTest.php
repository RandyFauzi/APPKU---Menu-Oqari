<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Database\Factories\OrderFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_aggregates_more_than_live_order_limit_and_keeps_zero_cogs_and_real_losses(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = User::factory()->create(['role' => 'owner', 'shop_id' => $shop->id]);
        OrderFactory::new()->count(151)->create(['shop_id' => $shop->id, 'payment_status' => 'PAID', 'order_status' => 'COMPLETED', 'subtotal' => 10000, 'discount_amount' => 0, 'grand_total' => 10000, 'total_cogs' => 0]);
        OrderFactory::new()->create(['shop_id' => $shop->id, 'payment_status' => 'PAID', 'order_status' => 'COMPLETED', 'subtotal' => 10000, 'discount_amount' => 0, 'grand_total' => 10000, 'total_cogs' => 20000]);
        OrderFactory::new()->create(['shop_id' => $shop->id, 'payment_status' => 'UNPAID', 'subtotal' => 900000]);
        OrderFactory::new()->create(['shop_id' => $shop->id, 'payment_status' => 'PAID', 'order_status' => 'CANCELLED', 'subtotal' => 900000]);
        OrderFactory::new()->create(['shop_id' => Shop::factory()->create()->id, 'payment_status' => 'PAID', 'subtotal' => 900000]);

        $this->actingAs($owner)->get(route('admin.reports.index', ['period' => 'all']))->assertOk()
            ->assertViewHas('transactionsCount', 152)->assertViewHas('netSales', 1520000.0)
            ->assertViewHas('totalCogs', 20000.0)->assertViewHas('grossProfit', 1500000.0)
            ->assertViewHas('transactions', fn ($rows) => $rows->count() === 25 && $rows->total() === 152)
            ->assertSee('Rp -10.000')->assertDontSee('Estimasi HPP');
    }

    public function test_export_includes_full_period_but_no_other_cafe_and_escapes_spreadsheet_formula(): void
    {
        $shop = Shop::factory()->create(['onboarding_status' => 'completed']);
        $owner = User::factory()->create(['role' => 'owner', 'shop_id' => $shop->id]);
        OrderFactory::new()->create(['shop_id' => $shop->id, 'payment_status' => 'PAID', 'order_status' => 'COMPLETED', 'customer_name' => '=1+2', 'total_cogs' => 0]);
        OrderFactory::new()->create(['shop_id' => Shop::factory()->create()->id, 'payment_status' => 'PAID', 'customer_name' => 'Foreign secret']);

        $response = $this->actingAs($owner)->get(route('admin.reports.index', ['period' => 'all', 'format' => 'csv']))->assertOk()->assertDownload('laporan-keuangan.csv');
        $this->assertStringContainsString("'=1+2", $response->streamedContent());
        $this->assertStringNotContainsString('Foreign secret', $response->streamedContent());
    }

    public function test_invalid_period_and_date_return_validation_errors(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'shop_id' => Shop::factory()->create()->id]);

        $this->actingAs($owner)->getJson(route('admin.reports.index', ['date' => 'invalid', 'period' => 'invalid']))
            ->assertUnprocessable()->assertJsonValidationErrors(['date', 'period']);
    }
}
