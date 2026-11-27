<?php

namespace Tests\Feature;

use App\Livewire\Customer\MyOrders;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Database\Seeders\MaterialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MaterialSeeder::class);
    }

    public function test_guest_is_redirected_to_login_when_accessing_orders(): void
    {
        $response = $this->get('/pesanan-saya');
        $response->assertRedirect('/masuk');
    }

    public function test_customer_can_render_my_orders_page(): void
    {
        $user = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->actingAs($user);

        $response = $this->get('/pesanan-saya');
        $response->assertStatus(200);
        $response->assertSeeLivewire(MyOrders::class);
        $response->assertSee('Pesanan & Antrean Saya', false);
    }

    public function test_customer_only_sees_their_own_orders(): void
    {
        $user1 = User::factory()->create(['name' => 'Budi Warung', 'role' => 'customer']);
        $user2 = User::factory()->create(['name' => 'Siti Bakso', 'role' => 'customer']);

        $orderUser1 = Order::create([
            'user_id' => $user1->id,
            'customer_name' => 'Budi Warung',
            'customer_phone' => '081234567890',
            'start_type' => 'start_from_zero',
            'preference' => 'enak',
            'meat_weight' => 1000,
            'grinding_fee' => 15000,
            'total_price' => 165000,
            'status' => 'pending',
            'pickup_at' => now()->addHours(2),
        ]);

        $orderUser2 = Order::create([
            'user_id' => $user2->id,
            'customer_name' => 'Siti Bakso',
            'customer_phone' => '081298765432',
            'start_type' => 'ingredients_only',
            'preference' => 'katalog',
            'meat_weight' => 0,
            'grinding_fee' => 0,
            'total_price' => 45000,
            'status' => 'completed',
            'pickup_at' => now()->addHours(3),
        ]);

        $this->actingAs($user1);

        Livewire::test(MyOrders::class)
            ->assertSee('#BSK-'.str_pad($orderUser1->id, 5, '0', STR_PAD_LEFT))
            ->assertDontSee('#BSK-'.str_pad($orderUser2->id, 5, '0', STR_PAD_LEFT));
    }

    public function test_customer_can_filter_orders_by_status(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $orderPending = Order::create([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'start_type' => 'start_from_zero',
            'preference' => 'enak',
            'meat_weight' => 1000,
            'grinding_fee' => 15000,
            'total_price' => 165000,
            'status' => 'pending',
            'pickup_at' => now()->addHours(2),
        ]);

        $orderCompleted = Order::create([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'start_type' => 'own_meat',
            'preference' => 'ekonomis',
            'meat_weight' => 500,
            'grinding_fee' => 7500,
            'total_price' => 25000,
            'status' => 'completed',
            'pickup_at' => now()->addHours(4),
        ]);

        $this->actingAs($user);

        Livewire::test(MyOrders::class)
            ->call('filterByStatus', 'pending')
            ->assertSet('statusFilter', 'pending')
            ->assertSee('#BSK-'.str_pad($orderPending->id, 5, '0', STR_PAD_LEFT))
            ->assertDontSee('#BSK-'.str_pad($orderCompleted->id, 5, '0', STR_PAD_LEFT))
            ->call('filterByStatus', 'completed')
            ->assertSet('statusFilter', 'completed')
            ->assertSee('#BSK-'.str_pad($orderCompleted->id, 5, '0', STR_PAD_LEFT))
            ->assertDontSee('#BSK-'.str_pad($orderPending->id, 5, '0', STR_PAD_LEFT));
    }

    public function test_customer_can_toggle_order_items_breakdown(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $order = Order::create([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'start_type' => 'start_from_zero',
            'preference' => 'enak',
            'meat_weight' => 1000,
            'grinding_fee' => 15000,
            'total_price' => 165000,
            'status' => 'pending',
            'pickup_at' => now()->addHours(2),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'material_id' => 1,
            'material_name' => 'Daging Sapi Paha Segar',
            'quantity' => 1000,
            'unit' => 'gram',
            'unit_price' => 140,
            'subtotal' => 140000,
        ]);

        $this->actingAs($user);

        Livewire::test(MyOrders::class)
            ->assertDontSee('Daging Sapi Paha Segar')
            ->call('selectOrder', $order->id)
            ->assertSet('selectedOrderId', $order->id)
            ->assertSee('Daging Sapi Paha Segar')
            ->assertSee('140.000')
            ->call('selectOrder', $order->id)
            ->assertSet('selectedOrderId', null)
            ->assertDontSee('Daging Sapi Paha Segar');
    }

    public function test_customer_order_breakdown_displays_bawa_sendiri_for_own_meat_order(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $order = Order::create([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'start_type' => 'own_meat',
            'preference' => 'seimbang',
            'meat_weight' => 500,
            'grinding_fee' => 7500,
            'total_price' => 19500,
            'status' => 'pending',
            'pickup_at' => now()->addHours(2),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'material_name' => 'Daging Sapi',
            'quantity' => 500,
            'unit' => 'gram',
            'unit_price' => 0,
            'subtotal' => 0,
        ]);

        $this->actingAs($user);

        Livewire::test(MyOrders::class)
            ->call('selectOrder', $order->id)
            ->assertSee('Daging milik pelanggan')
            ->assertSee('Bawa Sendiri');
    }

    public function test_customer_sees_cancelled_tab_count_and_can_filter_cancelled_orders(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $pendingOrder = Order::create([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'start_type' => 'start_from_zero',
            'preference' => 'enak',
            'meat_weight' => 1000,
            'grinding_fee' => 15000,
            'total_price' => 165000,
            'status' => 'pending',
            'pickup_at' => now()->addHours(2),
        ]);

        $cancelledOrder = Order::create([
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'start_type' => 'start_from_zero',
            'preference' => 'seimbang',
            'meat_weight' => 500,
            'grinding_fee' => 7500,
            'total_price' => 88000,
            'status' => 'dibatalkan',
            'pickup_at' => now()->addHours(3),
        ]);

        $this->actingAs($user);

        Livewire::test(MyOrders::class)
            ->assertSee('Semua (2)')
            ->assertSee('Menunggu (1)')
            ->assertSee('Dibatalkan (1)')
            ->call('filterByStatus', 'cancelled')
            ->assertSee('#BSK-'.str_pad((string) $cancelledOrder->id, 5, '0', STR_PAD_LEFT))
            ->assertDontSee('#BSK-'.str_pad((string) $pendingOrder->id, 5, '0', STR_PAD_LEFT));
    }

    public function test_customer_can_self_cancel_own_pending_order_only_and_cannot_cancel_processing_or_others(): void
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $other = User::factory()->create(['role' => 'customer']);

        $ownPending = Order::create([
            'user_id' => $owner->id,
            'customer_name' => $owner->name,
            'start_type' => 'start_from_zero',
            'preference' => 'enak',
            'meat_weight' => 500,
            'grinding_fee' => 7500,
            'total_price' => 88000,
            'status' => 'pending',
            'pickup_at' => now()->addHours(2),
        ]);

        $ownProcessing = Order::create([
            'user_id' => $owner->id,
            'customer_name' => $owner->name,
            'start_type' => 'start_from_zero',
            'preference' => 'enak',
            'meat_weight' => 500,
            'grinding_fee' => 7500,
            'total_price' => 88000,
            'status' => 'processing',
            'pickup_at' => now()->addHours(2),
        ]);

        $otherPending = Order::create([
            'user_id' => $other->id,
            'customer_name' => $other->name,
            'start_type' => 'start_from_zero',
            'preference' => 'enak',
            'meat_weight' => 500,
            'grinding_fee' => 7500,
            'total_price' => 88000,
            'status' => 'pending',
            'pickup_at' => now()->addHours(2),
        ]);

        Livewire::actingAs($owner)
            ->test(MyOrders::class)
            ->call('cancelOrder', $ownPending->id)
            ->call('cancelOrder', $ownProcessing->id)
            ->call('cancelOrder', $otherPending->id);

        $this->assertSame('cancelled', $ownPending->fresh()->status);
        $this->assertSame('customer', $ownPending->fresh()->cancelled_by);
        $this->assertNotNull($ownPending->fresh()->cancelled_at);
        $this->assertSame('processing', $ownProcessing->fresh()->status);
        $this->assertSame('pending', $otherPending->fresh()->status);

        // Membedakan tampilan badge pembatalan oleh pembeli vs oleh kios
        Order::create([
            'user_id' => $owner->id,
            'customer_name' => $owner->name,
            'start_type' => 'start_from_zero',
            'preference' => 'enak',
            'meat_weight' => 500,
            'grinding_fee' => 7500,
            'total_price' => 88000,
            'status' => 'cancelled',
            'cancelled_by' => 'admin',
            'cancelled_at' => now(),
            'pickup_at' => now()->addHours(2),
        ]);

        Livewire::actingAs($owner)
            ->test(MyOrders::class)
            ->call('filterByStatus', 'cancelled')
            ->assertSee('Pesanan Dibatalkan oleh Anda')
            ->assertSee('Pesanan Dibatalkan oleh Kios');
    }
}
