<?php
namespace Tests\Feature;

use App\Models\{Booking, Service};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeCareDemoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_viewer_can_open_bootstrap_and_get_quote(): void
    {
        $this->getJson('/api/bootstrap')->assertOk()->assertJsonStructure(['services','caregivers','bookings','reviews']);
        $this->postJson('/api/quote', [
            'service_id' => Service::firstOrFail()->id,
            'scheduled_date' => now('Asia/Jakarta')->addWeeks(2)->startOfWeek()->addDays(2)->toDateString(),
            'scheduled_time' => '09:00', 'units' => 2, 'distance_km' => 7,
        ])->assertOk()->assertJsonPath('quote.travel', 9000);
    }

    public function test_viewer_can_create_booking_and_admin_can_manage_it(): void
    {
        $response = $this->postJson('/api/bookings', [
            'service_id' => Service::firstOrFail()->id,
            'scheduled_date' => now('Asia/Jakarta')->addWeeks(2)->startOfWeek()->addDays(2)->toDateString(),
            'scheduled_time' => '09:00', 'units' => 2, 'distance_km' => 7,
            'name' => 'Pelanggan Demo', 'phone' => '081290001111',
            'email' => 'pelanggan@example.test', 'address' => 'Jalan Contoh 16 Bandung',
        ]);
        $response->assertCreated()->assertJsonPath('booking.total', 184000);
        $id = $response->json('booking.id');
        $this->assertDatabaseHas('bookings', ['id' => $id, 'total' => 184000]);
        $this->patchJson("/api/bookings/{$id}",['status'=>'confirmed'])->assertForbidden();
        $this->withSession(['demo_role'=>'admin'])->patchJson("/api/bookings/{$id}", ['status'=>'confirmed'])->assertOk()->assertJsonPath('booking.status','confirmed');
        $this->deleteJson("/api/bookings/{$id}")->assertOk();
        $this->assertDatabaseMissing('bookings', ['id'=>$id]);
    }

    public function test_admin_service_crud_and_viewer_access_restriction(): void
    {
        $payload = [
            'name'=>'Konsultasi Home Care','category'=>'Konsultasi','description'=>'Pemeriksaan dan konsultasi rumah untuk demo.',
            'price'=>125000,'unit'=>'visit','min_units'=>1,'duration_minutes'=>60,'active'=>true,'icon'=>'heart',
        ];
        $this->postJson('/api/manage/services', $payload)->assertForbidden();
        $this->withSession(['demo_role'=>'admin']);
        $response=$this->postJson('/api/manage/services', $payload)->assertCreated();
        $id=$response->json('item.id');
        $this->patchJson("/api/manage/services/{$id}", [...$payload,'price'=>150000])->assertOk()->assertJsonPath('item.price',150000);
        $this->deleteJson("/api/manage/services/{$id}")->assertOk();
    }
}
