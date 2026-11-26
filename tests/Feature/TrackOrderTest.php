<?php

namespace Tests\Feature;

use App\Livewire\TrackOrder;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\TestCase;

class TrackOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_render_public_order_tracking_page(): void
    {
        $response = $this->get('/lacak-pesanan');

        $response->assertStatus(200);
        $response->assertSeeLivewire(TrackOrder::class);
        $response->assertSee('Lacak Status Pesanan');
    }

    public function test_guest_can_track_order_with_matching_code_and_phone(): void
    {
        $order = Order::create([
            'customer_name' => 'Pak Slamet',
            'customer_phone' => '081234567890',
            'pickup_at' => now()->addDay()->setTime(8, 0),
            'start_type' => 'start_from_zero',
            'meat_weight' => 1000,
            'preference' => 'seimbang',
            'grinding_fee' => 15000,
            'total_price' => 171000,
            'status' => 'processing',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'material_name' => 'Daging Sapi',
            'quantity' => 1000,
            'unit' => 'gram',
            'unit_price' => 140,
            'subtotal' => 140000,
        ]);

        $orderCode = '#BSK-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT);

        // Mendukung format internasional 628... meski disimpan sebagai 08...
        Livewire::test(TrackOrder::class)
            ->set('orderCode', $orderCode)
            ->set('phoneNumber', '6281234567890')
            ->call('searchOrder')
            ->assertHasNoErrors()
            ->assertSet('trackedOrderId', $order->id)
            ->assertSee('Pak Slamet')
            ->assertSee('Sedang Digiling / Diproses')
            ->assertSee('Daging Sapi');
    }

    public function test_guest_cannot_track_order_with_mismatched_phone_number(): void
    {
        $order = Order::create([
            'customer_name' => 'Bu Ani Rahasia',
            'customer_phone' => '081234567890',
            'pickup_at' => now()->addDay()->setTime(8, 0),
            'start_type' => 'start_from_zero',
            'meat_weight' => 500,
            'preference' => 'enak',
            'grinding_fee' => 7500,
            'total_price' => 93500,
            'status' => 'pending',
        ]);

        Livewire::test(TrackOrder::class)
            ->set('orderCode', '#BSK-'.str_pad((string) $order->id, 5, '0', STR_PAD_LEFT))
            ->set('phoneNumber', '089999999999')
            ->call('searchOrder')
            ->assertSet('trackedOrderId', null)
            ->assertSee('Pesanan tidak ditemukan')
            ->assertDontSee('Bu Ani Rahasia');
    }

    public function test_tracked_order_id_is_locked_against_idor_tampering(): void
    {
        $order = Order::create([
            'customer_name' => 'Korban IDOR',
            'customer_phone' => '081234567890',
            'pickup_at' => now()->addDay()->setTime(8, 0),
            'start_type' => 'start_from_zero',
            'meat_weight' => 500,
            'preference' => 'enak',
            'grinding_fee' => 7500,
            'total_price' => 88000,
            'status' => 'pending',
        ]);

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::test(TrackOrder::class)
            ->set('trackedOrderId', $order->id);
    }

    public function test_array_code_query_parameter_does_not_crash_track_order_page(): void
    {
        $response = $this->get('/lacak-pesanan?code[]=123');

        $response->assertStatus(200);
        $response->assertSee('Lacak Status Pesanan');
    }
}
