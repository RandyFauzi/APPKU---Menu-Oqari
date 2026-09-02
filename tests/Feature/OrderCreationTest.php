<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Table;
use App\Models\PaymentMethod;
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
            'is_active' => true
        ]);
    }

    public function test_can_create_order_successfully()
    {
        $product = Product::factory()->create([
            'shop_id' => $this->shop->id,
            'price' => 30000,
            'is_sold_out' => false,
            'is_active' => true // wait, does product have is_active?
        ]);

        $payload = [
            'table_id' => 'Meja 1',
            'customer_name' => 'Randy',
            'payment_method' => 'QRIS',
            'items' => [
                [
                    'id' => $product->id,
                    'qty' => 2
                ]
            ]
        ];

        $response = $this->postJson(route('shop.submit', ['slug' => $this->shop->slug]), $payload);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'shop_id' => $this->shop->id,
            'customer_name' => 'Randy',
            'grand_total' => 60000
        ]);
        
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 30000,
            'subtotal' => 60000
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
                    'qty' => 1
                ]
            ]
        ];

        $response = $this->postJson(route('shop.submit', ['slug' => $this->shop->slug]), $payload);

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
            'items' => [['id' => $product->id, 'qty' => 1]]
        ];

        $response = $this->postJson(route('shop.submit', ['slug' => $this->shop->slug]), $payload);

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
            'items' => [['id' => $product->id, 'qty' => -5]]
        ];

        $response = $this->postJson(route('shop.submit', ['slug' => $this->shop->slug]), $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['items.0.qty']);
    }
}
