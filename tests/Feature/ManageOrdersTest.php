<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManageOrders;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageOrdersTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminUserSeeder::class);
        $this->admin = User::where('email', 'admin@basoku.com')->first();
    }

    public function test_guest_cannot_access_manage_orders(): void
    {
        $response = $this->get('/admin/pesanan');
        $response->assertRedirect('/masuk');
    }

    public function test_customer_cannot_access_manage_orders(): void
    {
        $customer = User::create([
            'name' => 'Pelanggan Biasa',
            'email' => 'customer@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)->get('/admin/pesanan');
        $response->assertRedirect('/masuk');
    }

    public function test_admin_can_view_orders_table(): void
    {
        $order = Order::create([
            'customer_name' => 'Pak Budi',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 500,
            'preference' => 'seimbang',
            'grinding_fee' => 15000,
            'total_price' => 125000,
            'status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'material_name' => 'Daging Sapi',
            'quantity' => 500,
            'unit' => 'gram',
            'unit_price' => 140,
            'subtotal' => 70000,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/pesanan');

        $response->assertStatus(200);
        $response->assertSeeLivewire(ManageOrders::class);
        $response->assertSee('Manajemen Pesanan');
        $response->assertSee('Pak Budi');
        $response->assertSee('125.000');
    }

    public function test_admin_can_view_order_details_modal(): void
    {
        $order = Order::create([
            'customer_name' => 'Bu Siti',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 1000,
            'preference' => 'enak',
            'grinding_fee' => 30000,
            'total_price' => 250000,
            'status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'material_name' => 'Daging Sapi Paha',
            'quantity' => 1000,
            'unit' => 'gram',
            'unit_price' => 140,
            'subtotal' => 140000,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ManageOrders::class)
            ->assertSet('showDetailModal', false)
            ->call('viewOrder', $order->id)
            ->assertSet('showDetailModal', true)
            ->assertSee('Bu Siti')
            ->assertSee('Daging Sapi Paha')
            ->assertSee('1000 gram')
            ->call('closeModal')
            ->assertSet('showDetailModal', false)
            ->assertSet('selectedOrder', null);
    }

    public function test_admin_can_update_order_status(): void
    {
        $order = Order::create([
            'customer_name' => 'Mas Danu',
            'pickup_at' => now()->addDay(),
            'start_type' => 'own_meat',
            'meat_weight' => 500,
            'preference' => 'ekonomis',
            'grinding_fee' => 15000,
            'total_price' => 35000,
            'status' => 'pending',
        ]);

        // 1. Update from pending to processing
        Livewire::actingAs($this->admin)
            ->test(ManageOrders::class)
            ->call('updateStatus', $order->id, 'processing')
            ->assertSee('Sedang Diproses');

        $this->assertEquals('processing', $order->fresh()->status);

        // 2. Update to completed
        Livewire::actingAs($this->admin)
            ->test(ManageOrders::class)
            ->call('updateStatus', $order->id, 'completed')
            ->assertSee('Selesai');

        $this->assertEquals('completed', $order->fresh()->status);

        // 3. Update to cancelled
        Livewire::actingAs($this->admin)
            ->test(ManageOrders::class)
            ->call('updateStatus', $order->id, 'cancelled')
            ->assertSee('Dibatalkan');

        $this->assertEquals('cancelled', $order->fresh()->status);
    }

    public function test_filter_by_status_filters_orders_correctly(): void
    {
        Order::create([
            'customer_name' => 'Order Pending',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 500,
            'preference' => 'seimbang',
            'grinding_fee' => 15000,
            'total_price' => 100000,
            'status' => 'pending',
        ]);

        Order::create([
            'customer_name' => 'Order Completed',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 500,
            'preference' => 'seimbang',
            'grinding_fee' => 15000,
            'total_price' => 100000,
            'status' => 'completed',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ManageOrders::class)
            ->set('statusFilter', 'completed')
            ->assertSee('Order Completed')
            ->assertDontSee('Order Pending');
    }
}
