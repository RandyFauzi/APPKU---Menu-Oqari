<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceiptAndPrinterTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_can_save_receipt_and_printer_settings(): void
    {
        $shop = Shop::factory()->create([
            'onboarding_status' => 'completed',
        ]);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);

        $payload = [
            'name' => $shop->name,
            'slug' => $shop->slug,
            'receipt_settings' => [
                'paper_width' => '58mm',
                'show_logo' => true,
                'show_phone' => true,
                'show_social' => true,
                'show_customer' => true,
                'show_wifi' => true,
                'wifi_ssid' => 'Sparta_Coffee_Guest',
                'wifi_password' => 'kopi1234',
                'header_text' => 'Selamat Menikmati Kopi Kami',
                'footer_text' => 'Terima kasih atas kunjungan Anda!',
                'kitchen_large_font' => true,
                'kitchen_show_prices' => false,
                'kitchen_show_notes' => true,
                'kitchen_auto_cut' => true,
            ],
            'printer_settings' => [
                'multi_printer_enabled' => true,
                'cashier_auto_print' => true,
                'kitchen_auto_print' => true,
            ],
            'printers' => [
                [
                    'name' => 'Printer Kasir',
                    'location' => 'Kasir Utama',
                    'connection_type' => 'browser',
                    'paper_width' => '58mm',
                    'is_active' => true,
                    'auto_print' => true,
                    'category_ids' => [],
                ],
                [
                    'name' => 'Printer Dapur',
                    'location' => 'Dapur Makanan',
                    'connection_type' => 'network',
                    'ip_address' => '192.168.1.150',
                    'port' => 9100,
                    'paper_width' => '80mm',
                    'is_active' => true,
                    'auto_print' => true,
                    'category_ids' => [1, 2],
                ],
            ],
        ];

        $response = $this->actingAs($owner)->postJson('/admin/api/settings', $payload);

        $response->assertOk()->assertJson(['success' => true]);

        $shop->refresh();
        $this->assertSame('58mm', $shop->receipt_settings['paper_width']);
        $this->assertSame('Sparta_Coffee_Guest', $shop->receipt_settings['wifi_ssid']);
        $this->assertTrue($shop->printer_settings['multi_printer_enabled']);

        $this->assertDatabaseHas('printers', [
            'shop_id' => $shop->id,
            'name' => 'Printer Kasir',
            'location' => 'Kasir Utama',
        ]);
        $this->assertDatabaseHas('printers', [
            'shop_id' => $shop->id,
            'name' => 'Printer Dapur',
            'ip_address' => '192.168.1.150',
            'port' => 9100,
        ]);
    }

    public function test_customer_thermal_receipt_renders_with_details_and_wifi(): void
    {
        $shop = Shop::factory()->create([
            'name' => 'Kedai Kopi Mantap',
            'receipt_settings' => [
                'paper_width' => '58mm',
                'show_wifi' => true,
                'wifi_ssid' => 'MantapCafe_WiFi',
                'wifi_password' => 'kopimantap123',
                'footer_text' => 'Follow IG @kopi.mantap',
            ],
        ]);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $table = Table::create(['shop_id' => $shop->id, 'name' => 'Meja 05']);

        $order = Order::create([
            'shop_id' => $shop->id,
            'table_id' => $table->id,
            'customer_name' => 'Budi Santoso',
            'subtotal' => 50000,
            'discount_amount' => 5000,
            'tax_amount' => 4500,
            'service_charge_amount' => 0,
            'grand_total' => 49500,
            'amount_paid' => 50000,
            'change_amount' => 500,
            'payment_method' => 'CASH',
            'payment_status' => 'PAID',
            'order_status' => 'COMPLETED',
            'fulfillment_type' => 'DINE_IN',
        ]);

        $product = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Espresso Doppio']);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => 'Espresso Doppio',
            'quantity' => 2,
            'unit_price' => 25000,
            'subtotal' => 50000,
            'notes' => 'Less sugar please',
        ]);

        $response = $this->actingAs($owner)->get("/admin/api/orders/{$order->id}/print?type=customer");

        $response->assertOk();
        $response->assertSee('Kedai Kopi Mantap');
        $response->assertSee('Espresso Doppio');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Meja 05');
        $response->assertSee('MantapCafe_WiFi');
        $response->assertSee('kopimantap123');
        $response->assertSee('Follow IG @kopi.mantap');
    }

    public function test_kitchen_order_ticket_renders_cleanly_for_kitchen_prep(): void
    {
        $shop = Shop::factory()->create([
            'name' => 'Dapur Nusantara',
            'receipt_settings' => [
                'kitchen_show_prices' => false,
                'kitchen_show_notes' => true,
            ],
        ]);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);
        $table = Table::create(['shop_id' => $shop->id, 'name' => 'VIP 02']);

        $order = Order::create([
            'shop_id' => $shop->id,
            'table_id' => $table->id,
            'customer_name' => 'Siti Rahma',
            'subtotal' => 75000,
            'grand_total' => 75000,
            'order_status' => 'PROCESSING',
            'payment_status' => 'PAID',
            'fulfillment_type' => 'DINE_IN',
        ]);

        $product = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Nasi Goreng Spesial']);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => 'Nasi Goreng Spesial',
            'quantity' => 3,
            'unit_price' => 25000,
            'subtotal' => 75000,
            'notes' => 'Pedas banget level 5, tanpa daun bawang',
        ]);

        $response = $this->actingAs($owner)->get("/admin/api/orders/{$order->id}/print?type=kitchen");

        $response->assertOk();
        $response->assertSee('TIKET PESANAN DAPUR');
        $response->assertSee('VIP 02');
        $response->assertSee('Nasi Goreng Spesial');
        $response->assertSee('3x');
        $response->assertSee('Pedas banget level 5, tanpa daun bawang');
    }

    public function test_test_print_sample_route_renders(): void
    {
        $shop = Shop::factory()->create(['name' => 'Cafe Sample']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);

        $customerResponse = $this->actingAs($owner)->get('/admin/api/orders/sample/test-print?type=customer&width=58mm');
        $customerResponse->assertOk();
        $customerResponse->assertSee('Cafe Sample');

        $kitchenResponse = $this->actingAs($owner)->get('/admin/api/orders/sample/test-print?type=kitchen&width=80mm');
        $kitchenResponse->assertOk();
        $kitchenResponse->assertSee('TIKET PESANAN DAPUR');
    }

    public function test_kitchen_ticket_renders_dine_in_take_away_when_order_is_mixed(): void
    {
        $shop = Shop::factory()->create(['name' => 'Sparta Coffee']);
        $owner = User::factory()->create(['shop_id' => $shop->id, 'role' => 'owner']);

        $order = Order::create([
            'shop_id' => $shop->id,
            'customer_name' => 'Guest',
            'subtotal' => 125000,
            'grand_total' => 125000,
            'order_status' => 'CONFIRMED',
            'payment_status' => 'PAID',
            'fulfillment_type' => 'DINE_IN',
        ]);

        $product1 = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Mont Blanc']);
        $product2 = Product::factory()->create(['shop_id' => $shop->id, 'name' => 'Blue Ocean']);

        // Dine In item
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'product_name' => 'Mont Blanc',
            'quantity' => 1,
            'unit_price' => 35000,
            'subtotal' => 35000,
            'serving_type' => 'DINE_IN',
        ]);

        // Takeaway item
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'product_name' => 'Blue Ocean',
            'quantity' => 1,
            'unit_price' => 45000,
            'subtotal' => 45000,
            'serving_type' => 'TAKEAWAY',
        ]);

        $response = $this->actingAs($owner)->get("/admin/api/orders/{$order->id}/print?type=kitchen");

        $response->assertOk();
        $response->assertSee('DINE IN / TAKE AWAY');
        $response->assertSee('DINE IN (MAKAN DI TEMPAT)');
        $response->assertSee('TAKEAWAY (BUNGKUS)');

        $thermalResponse = $this->actingAs($owner)->get("/admin/api/orders/{$order->id}/print?type=customer");
        $thermalResponse->assertOk();
        $thermalResponse->assertSee('Dine In / Takeaway');
    }
}
