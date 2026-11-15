<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Table;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    protected $shop;

    protected $tableModel;

    protected $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shop = Shop::factory()->create();
        $this->tableModel = Table::create(['shop_id' => $this->shop->id, 'name' => 'Meja 1']);

        $this->paymentMethod = PaymentMethod::create([
            'shop_id' => $this->shop->id,
            'name' => 'QRIS',
            'code' => 'QRIS',
            'type' => 'QRIS',
            'is_active' => true,
        ]);
    }

    public function test_can_create_order_successfully()
    {
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'price' => 30000,
            'is_sold_out' => false,
        ]);

        $payload = [
            'table_id' => 'Meja 1',
            'customer_name' => 'Randy',
            'payment_method' => 'QRIS',
            'items' => [
                [
                    'id' => $product->id,
                    'qty' => 2,
                ],
            ],
        ];

        $response = $this->postJson(route('shop.order.submit', ['slug' => $this->shop->slug]), $payload);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'shop_id' => $this->shop->id,
            'customer_name' => 'Randy',
            'grand_total' => 60000,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 30000,
            'subtotal' => 60000,
        ]);
    }

    public function test_cannot_order_invalid_product()
    {
        $payload = [
            'table_id' => 'Meja 1',
            'customer_name' => 'Randy',
            'payment_method' => 'QRIS',
            'items' => [
                [
                    'id' => 9999, // Invalid
                    'qty' => 1,
                ],
            ],
        ];

        $response = $this->postJson(route('shop.order.submit', ['slug' => $this->shop->slug]), $payload);

        // Validation error
        $response->assertStatus(422);
    }

    public function test_cannot_order_sold_out_product()
    {
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'is_sold_out' => true,
        ]);

        $payload = [
            'table_id' => 'Meja 1',
            'customer_name' => 'Randy',
            'payment_method' => 'QRIS',
            'items' => [['id' => $product->id, 'qty' => 1]],
        ];

        $response = $this->postJson(route('shop.order.submit', ['slug' => $this->shop->slug]), $payload);

        // Custom Domain Exception / bad request
        $response->assertStatus(400);
    }

    public function test_cannot_order_negative_quantity()
    {
        $product = Product::factory()->create(['shop_id' => $this->shop->id]);

        $payload = [
            'table_id' => 'Meja 1',
            'customer_name' => 'Randy',
            'payment_method' => 'QRIS',
            'items' => [['id' => $product->id, 'qty' => -5]],
        ];

        $response = $this->postJson(route('shop.order.submit', ['slug' => $this->shop->slug]), $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.qty']);
    }

    public function test_can_create_takeaway_order_without_table()
    {
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'price' => 25000,
            'is_sold_out' => false,
        ]);

        $payload = [
            'fulfillment_type' => 'TAKEAWAY',
            'table_id' => null,
            'customer_name' => 'Budi Takeaway',
            'customer_email' => 'budi@example.com',
            'payment_method' => 'QRIS',
            'items' => [
                ['id' => $product->id, 'qty' => 1],
            ],
        ];

        $response = $this->postJson(route('shop.order.submit', ['slug' => $this->shop->slug]), $payload);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'shop_id' => $this->shop->id,
            'customer_name' => 'Budi Takeaway',
            'fulfillment_type' => 'TAKEAWAY',
            'table_id' => null,
            'grand_total' => 25000,
        ]);
    }

    public function test_cannot_create_dine_in_order_without_table()
    {
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'price' => 25000,
            'is_sold_out' => false,
        ]);

        $payload = [
            'fulfillment_type' => 'DINE_IN',
            'table_id' => '',
            'customer_name' => 'Dina DineIn',
            'customer_email' => 'dina@example.com',
            'payment_method' => 'QRIS',
            'items' => [
                ['id' => $product->id, 'qty' => 1],
            ],
        ];

        $response = $this->postJson(route('shop.order.submit', ['slug' => $this->shop->slug]), $payload);

        $response->assertStatus(422)
            ->assertJson(['success' => false, 'message' => 'Nomor meja wajib diisi untuk pesanan Dine In.']);
    }

    public function test_can_create_order_without_email_and_view_marks_it_as_optional(): void
    {
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'price' => 20000,
            'is_sold_out' => false,
        ]);

        $payload = [
            'fulfillment_type' => 'TAKEAWAY',
            'customer_name' => 'Customer Without Email',
            'customer_email' => '',
            'customer_phone' => '08123456789',
            'payment_method' => 'QRIS',
            'items' => [
                ['id' => $product->id, 'qty' => 1],
            ],
        ];

        $response = $this->postJson(route('shop.order.submit', ['slug' => $this->shop->slug]), $payload);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'shop_id' => $this->shop->id,
            'customer_name' => 'Customer Without Email',
            'customer_email' => null,
            'grand_total' => 20000,
        ]);

        $cartResponse = $this->get(route('shop.cart', ['slug' => $this->shop->slug]));
        $cartResponse->assertOk()
            ->assertSee('Alamat Email')
            ->assertSee('(Opsional)')
            ->assertDontSee('id="customer-email" name="customer-email" autocomplete="email" placeholder="contoh@email.com" class="w-full bg-transparent border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" required', false);
    }

    public function test_can_validate_voucher_and_apply_promo_code_at_checkout(): void
    {
        $voucher = Voucher::create([
            'shop_id' => $this->shop->id,
            'code' => 'HEMAT10K',
            'name' => 'Diskon Rp 10.000',
            'discount_type' => 'fixed',
            'discount_value' => 10000,
            'min_spend' => 30000,
            'is_active' => true,
        ]);

        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'price' => 50000,
            'is_sold_out' => false,
        ]);

        // 1. Validation fails if subtotal is below minimum spend
        $this->postJson(route('shop.voucher.validate', ['slug' => $this->shop->slug]), [
            'code' => 'HEMAT10K',
            'subtotal' => 20000,
        ])->assertStatus(422);

        // 2. Validation succeeds if subtotal meets criteria
        $valRes = $this->postJson(route('shop.voucher.validate', ['slug' => $this->shop->slug]), [
            'code' => 'HEMAT10K',
            'subtotal' => 50000,
        ]);

        $valRes->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('discount_amount', 10000)
            ->assertJsonPath('net_total', 40000);

        // 3. Submitting order with voucher_code deducts discount and records voucher details
        $payload = [
            'fulfillment_type' => 'TAKEAWAY',
            'customer_name' => 'Promo User',
            'customer_email' => null,
            'customer_phone' => '08123456789',
            'voucher_code' => 'HEMAT10K',
            'payment_method' => 'QRIS',
            'items' => [
                ['id' => $product->id, 'qty' => 1],
            ],
        ];

        $response = $this->postJson(route('shop.order.submit', ['slug' => $this->shop->slug]), $payload);

        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'shop_id' => $this->shop->id,
            'customer_name' => 'Promo User',
            'subtotal' => 50000,
            'discount_amount' => 10000,
            'voucher_code' => 'HEMAT10K',
            'discount_reason' => 'Diskon Rp 10.000',
            'grand_total' => 40000,
        ]);

        // 4. Cart page renders Kode Promo optional field
        $cartResponse = $this->get(route('shop.cart', ['slug' => $this->shop->slug]));
        $cartResponse->assertOk()
            ->assertSee('Kode Promo')
            ->assertSee('(Opsional)')
            ->assertSee('customer-promo');
    }
}
