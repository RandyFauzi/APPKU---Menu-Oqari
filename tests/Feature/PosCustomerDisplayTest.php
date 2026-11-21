<?php

namespace Tests\Feature;

use App\Events\PosDisplayUpdated;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PosCustomerDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_user_can_view_customer_display(): void
    {
        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->get(route('admin.pos.customer-display'));

        $response->assertOk()
            ->assertSee('Layar Informasi Pelanggan');
    }

    public function test_pos_user_can_sync_customer_display_and_dispatch_event(): void
    {
        Event::fake([PosDisplayUpdated::class]);

        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('admin.pos.customer-display.sync'), [
                'status' => 'ordering',
                'items' => [
                    [
                        'name' => 'Kopi Susu Gula Aren',
                        'variant' => 'Ice',
                        'modifiers' => ['Less Sugar'],
                        'qty' => 2,
                        'price' => 25000,
                        'subtotal' => 50000,
                    ],
                ],
                'subtotal' => 50000,
                'discount_amount' => 5000,
                'discount_label' => 'Promo 10%',
                'grand_total' => 45000,
                'customer_name' => 'Budi',
                'table_name' => 'Meja 03',
                'fulfillment_type' => 'DINE_IN',
                'payment_method' => 'CASH',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        Event::assertDispatched(PosDisplayUpdated::class, function (PosDisplayUpdated $event) use ($shop): bool {
            return $event->shopId === $shop->id
                && $event->payload['status'] === 'ordering'
                && (int) $event->payload['grand_total'] === 45000
                && count($event->payload['items']) === 1;
        });
    }

    public function test_sync_customer_display_validates_status(): void
    {
        $shop = Shop::factory()->create();
        $user = User::factory()->create([
            'shop_id' => $shop->id,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('admin.pos.customer-display.sync'), [
                'status' => 'invalid_state',
                'subtotal' => 0,
                'grand_total' => 0,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }
}
