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
            'flour_weight' => 200,
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
            'flour_weight' => 500,
            'grinding_fee' => 0,
            'total_price' => 45000,
            'status' => 'completed',
            'pickup_at' => now()->addHours(3),
        ]);

        $this->actingAs($user1);

        Livewire::test(MyOrders::class)
            ->assertSee('#BSK-' . str_pad($orderUser1->id, 5, '0', STR_PAD_LEFT))
            ->assertDontSee('#BSK-' . str_pad($orderUser2->id, 5, '0', STR_PAD_LEFT));
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
            'flour_weight' => 200,
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
            'flour_weight' => 150,
            'grinding_fee' => 7500,
            'total_price' => 25000,
            'status' => 'completed',
            'pickup_at' => now()->addHours(4),
        ]);

        $this->actingAs($user);

        Livewire::test(MyOrders::class)
            ->call('filterByStatus', 'pending')
            ->assertSet('statusFilter', 'pending')
            ->assertSee('#BSK-' . str_pad($orderPending->id, 5, '0', STR_PAD_LEFT))
            ->assertDontSee('#BSK-' . str_pad($orderCompleted->id, 5, '0', STR_PAD_LEFT))
            ->call('filterByStatus', 'completed')
            ->assertSet('statusFilter', 'completed')
            ->assertSee('#BSK-' . str_pad($orderCompleted->id, 5, '0', STR_PAD_LEFT))
            ->assertDontSee('#BSK-' . str_pad($orderPending->id, 5, '0', STR_PAD_LEFT));
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
            'flour_weight' => 200,
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
}
