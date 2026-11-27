<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManageOrders;
use App\Models\Material;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\MaterialSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
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

    public function test_admin_can_export_orders_to_csv(): void
    {
        $order = Order::create([
            'customer_name' => 'Pak Budi Ekspor',
            'customer_phone' => '081234567890',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 1000,
            'preference' => 'enak',
            'grinding_fee' => 15000,
            'total_price' => 165000,
            'status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'material_name' => 'Tepung Tapioka',
            'quantity' => 250,
            'unit' => 'gram',
            'unit_price' => 18,
            'subtotal' => 9000,
        ]);

        $component = Livewire::actingAs($this->admin)
            ->test(ManageOrders::class)
            ->call('exportCsv');

        $response = $component->instance()->exportCsv();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('rekap-pesanan-basoku-', $response->headers->get('Content-Disposition'));

        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        $this->assertStringContainsString('Pak Budi Ekspor', $csvContent);
        $this->assertStringContainsString(',1000,250,15000,165000,', $csvContent);
    }

    public function test_non_admin_cannot_invoke_manage_orders_livewire_actions_directly(): void
    {
        $customer = User::create([
            'name' => 'Attacker Customer',
            'email' => 'attacker@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        Livewire::actingAs($customer)
            ->test(ManageOrders::class)
            ->assertForbidden();
    }

    public function test_admin_wa_url_normalizes_indonesian_phone_and_contains_status_message(): void
    {
        $order = Order::create([
            'customer_name' => 'Bu Rina',
            'customer_phone' => '0812-3456-7890',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 1000,
            'preference' => 'seimbang',
            'grinding_fee' => 15000,
            'total_price' => 170000,
            'status' => 'completed',
        ]);

        $component = Livewire::actingAs($this->admin)->test(ManageOrders::class);
        $waUrl = $component->instance()->getCustomerWaUrl($order);

        $this->assertNotNull($waUrl);
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $waUrl);
        $this->assertStringContainsString('SELESAI', urldecode($waUrl));
        $this->assertStringContainsString('#BSK-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT), urldecode($waUrl));
    }

    public function test_admin_orders_table_paginates_large_order_lists(): void
    {
        for ($i = 1; $i <= 18; $i++) {
            Order::create([
                'customer_name' => sprintf('Pelanggan Urut %02d', $i),
                'pickup_at' => now()->addDay(),
                'start_type' => 'start_from_zero',
                'meat_weight' => 500,
                'preference' => 'seimbang',
                'grinding_fee' => 7500,
                'total_price' => 93500,
                'status' => 'pending',
            ]);
        }

        Livewire::actingAs($this->admin)
            ->test(ManageOrders::class)
            ->assertViewHas('orders', fn ($orders) => $orders->count() === 15 && $orders->total() === 18);
    }

    public function test_status_filter_and_counts_support_indonesian_and_english_aliases(): void
    {
        Order::create([
            'customer_name' => 'Order Inggris Pending',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 500,
            'preference' => 'seimbang',
            'grinding_fee' => 7500,
            'total_price' => 88000,
            'status' => 'pending',
        ]);

        Order::create([
            'customer_name' => 'Order Lama Menunggu',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 500,
            'preference' => 'seimbang',
            'grinding_fee' => 7500,
            'total_price' => 88000,
            'status' => 'menunggu',
        ]);

        Order::create([
            'customer_name' => 'Order Lama Selesai',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 500,
            'preference' => 'seimbang',
            'grinding_fee' => 7500,
            'total_price' => 88000,
            'status' => 'selesai',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ManageOrders::class)
            ->assertViewHas('counts', fn (array $counts) => $counts['pending'] === 2 && $counts['completed'] === 1)
            ->set('statusFilter', 'pending')
            ->assertSee('Order Inggris Pending')
            ->assertSee('Order Lama Menunggu')
            ->assertDontSee('Order Lama Selesai')
            ->set('statusFilter', 'completed')
            ->assertSee('Order Lama Selesai')
            ->assertDontSee('Order Lama Menunggu');
    }

    public function test_csv_export_neutralizes_formula_injection_and_converts_pack_flour_to_grams(): void
    {
        $this->seed(MaterialSeeder::class);
        $tapioka = Material::where('name', 'Tepung Tapioka')->firstOrFail();

        $order = Order::create([
            'customer_name' => '=CMD|calc',
            'customer_phone' => '+081234567890',
            'pickup_at' => now()->addDay(),
            'start_type' => 'ingredients_only',
            'meat_weight' => 0,
            'preference' => 'bahan_saja',
            'grinding_fee' => 0,
            'total_price' => 18000,
            'status' => 'dibatalkan',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'material_id' => $tapioka->id,
            'material_name' => 'Tepung Tapioka',
            'quantity' => 2,
            'unit' => 'Pack',
            'unit_price' => 9000,
            'subtotal' => 18000,
        ]);

        $component = Livewire::actingAs($this->admin)->test(ManageOrders::class);
        $response = $component->instance()->exportCsv();

        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        $this->assertStringContainsString("'=CMD|calc", $csvContent);
        $this->assertStringContainsString("'+081234567890", $csvContent);
        // 2 Pack Tapioka @500g = 1000 gram tepung pada kolom Berat Tepung (g)
        $this->assertStringContainsString(',0,1000,0,18000,', $csvContent);
    }

    public function test_admin_order_detail_and_receipt_display_bawa_sendiri_for_own_meat_order(): void
    {
        $order = Order::create([
            'customer_name' => 'Pak Joko Own Meat',
            'pickup_at' => now()->addDay(),
            'start_type' => 'own_meat',
            'meat_weight' => 1000,
            'preference' => 'enak',
            'grinding_fee' => 15000,
            'total_price' => 27000,
            'status' => 'pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'material_name' => 'Daging Sapi',
            'quantity' => 1000,
            'unit' => 'gram',
            'unit_price' => 0,
            'subtotal' => 0,
        ]);

        Livewire::actingAs($this->admin)
            ->test(ManageOrders::class)
            ->call('viewOrder', $order->id)
            ->assertSee('Bawa Sendiri')
            ->assertSee('daging milik pelanggan');
    }

    public function test_status_counts_and_filter_handle_uppercase_and_whitespace_statuses(): void
    {
        Order::create([
            'customer_name' => 'Order Kapital Spasi',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 500,
            'preference' => 'seimbang',
            'grinding_fee' => 7500,
            'total_price' => 88000,
            'status' => ' Pending ',
        ]);

        Order::create([
            'customer_name' => 'Order Kapital Selesai',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 500,
            'preference' => 'seimbang',
            'grinding_fee' => 7500,
            'total_price' => 88000,
            'status' => 'SELESAI',
        ]);

        Livewire::actingAs($this->admin)
            ->test(ManageOrders::class)
            ->assertViewHas('counts', fn (array $counts) => $counts['pending'] === 1 && $counts['completed'] === 1)
            ->set('statusFilter', 'pending')
            ->assertSee('Order Kapital Spasi')
            ->assertDontSee('Order Kapital Selesai');
    }

    public function test_admin_redirect_routes_have_no_duplicate_admin_dot_name(): void
    {
        $routes = collect(Route::getRoutes()->getRoutes());
        $duplicateAdminDot = $routes->filter(fn ($route) => $route->getName() === 'admin.');

        $this->assertCount(0, $duplicateAdminDot);

        $this->actingAs($this->admin)->get('/admin/materials')->assertRedirect('/admin/bahan');
        $this->actingAs($this->admin)->get('/admin/recipes')->assertRedirect('/admin/resep');
        $this->actingAs($this->admin)->get('/admin/orders')->assertRedirect('/admin/pesanan');
    }

    public function test_buyer_cancelled_order_is_locked_from_admin_status_changes_while_admin_cancelled_remains_editable(): void
    {
        $buyerCancelled = Order::create([
            'customer_name' => 'Pembeli Batal Sendiri',
            'customer_phone' => '081234567891',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 500,
            'preference' => 'seimbang',
            'grinding_fee' => 7500,
            'total_price' => 88000,
            'status' => 'cancelled',
            'cancelled_by' => 'customer',
            'cancelled_at' => now(),
        ]);

        $adminPending = Order::create([
            'customer_name' => 'Pesanan Dibatalkan Kios',
            'customer_phone' => '081234567892',
            'pickup_at' => now()->addDay(),
            'start_type' => 'start_from_zero',
            'meat_weight' => 500,
            'preference' => 'seimbang',
            'grinding_fee' => 7500,
            'total_price' => 88000,
            'status' => 'pending',
        ]);

        // 1. Admin tidak dapat mengubah status pesanan yang dibatalkan pembeli (terkunci di UI & backend)
        $component = Livewire::actingAs($this->admin)
            ->test(ManageOrders::class)
            ->assertSee('Dibatalkan Pembeli')
            ->assertSee('Terkunci')
            ->assertDontSeeHtml('id="order-status-'.$buyerCancelled->id.'"')
            ->call('updateStatus', $buyerCancelled->id, 'processing')
            ->assertSee('terkunci permanen');

        $this->assertSame('cancelled', $buyerCancelled->fresh()->status);
        $this->assertSame('customer', $buyerCancelled->fresh()->cancelled_by);

        // Modal detail juga menampilkan pesan terkunci
        $component->call('viewOrder', $buyerCancelled->id)
            ->assertSee('Status Terkunci — Dibatalkan oleh Pembeli');

        // 2. Pembatalan oleh admin mencatat cancelled_by = 'admin' dan tetap bisa dipulihkan admin
        $component->call('updateStatus', $adminPending->id, 'cancelled');
        $adminPending->refresh();
        $this->assertSame('cancelled', $adminPending->status);
        $this->assertSame('admin', $adminPending->cancelled_by);
        $this->assertNotNull($adminPending->cancelled_at);

        // CSV membedakan Dibatalkan (Pembeli) vs Dibatalkan (Kios)
        $response = $component->instance()->exportCsv();
        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();
        $this->assertStringContainsString('Dibatalkan (Pembeli)', $csvContent);
        $this->assertStringContainsString('Dibatalkan (Kios)', $csvContent);

        // Admin memulihkan pesanan yang dibatalkan kios -> membersihkan cancelled_by & cancelled_at
        $component->call('updateStatus', $adminPending->id, 'processing');
        $adminPending->refresh();
        $this->assertSame('processing', $adminPending->status);
        $this->assertNull($adminPending->cancelled_by);
        $this->assertNull($adminPending->cancelled_at);
    }
}
