<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\Equipment;
use App\Models\Booking;
use Carbon\Carbon;

class BookingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_booking_successful_returns_201()
    {
        $customer = Customer::create([
            'customer_code' => 'CUST-001',
            'name' => 'John Doe',
            'phone' => '0123456789',
            'password' => '1234'
        ]);

        $equipment = Equipment::create([
            'equipment_code' => 'EQ-001',
            'name' => 'Tractor A',
            'type' => 'tractor',
            'maintenance_hour_threshold' => 100,
            'current_status' => 'available'
        ]);

        $start = Carbon::now()->addDay()->toDateTimeString();
        $end = Carbon::now()->addDays(2)->toDateTimeString();

        $payload = [
            'customer_id' => $customer->id,
            'equipment_id' => $equipment->id,
            'scheduled_start' => $start,
            'scheduled_end' => $end,
            'actual_area' => 10,
        ];

        $response = $this->postJson('/api/bookings', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'data' => ['id','job_number','customer_id','equipment_id']]);

        $this->assertDatabaseHas('bookings', [
            'customer_id' => $customer->id,
            'equipment_id' => $equipment->id
        ]);
    }

    public function test_create_booking_conflict_returns_422()
    {
        $customer = Customer::create([
            'customer_code' => 'CUST-002',
            'name' => 'Jane',
            'phone' => '0987654321',
            'password' => '1234'
        ]);

        $equipment = Equipment::create([
            'equipment_code' => 'EQ-002',
            'name' => 'Drone B',
            'type' => 'drone',
            'maintenance_hour_threshold' => 50,
            'current_status' => 'available'
        ]);

        // Create an existing booking that overlaps
        $existingStart = Carbon::now()->addDays(3);
        $existingEnd = Carbon::now()->addDays(5);

        Booking::create([
            'job_number' => 'JOB-20251227-001',
            'customer_id' => $customer->id,
            'equipment_id' => $equipment->id,
            'scheduled_start' => $existingStart,
            'scheduled_end' => $existingEnd,
            'total_price' => 1000,
            'actual_area' => 10,
            'status' => 'scheduled'
        ]);

        // Attempt to create a booking that overlaps the existing one
        $payload = [
            'customer_id' => $customer->id,
            'equipment_id' => $equipment->id,
            'scheduled_start' => Carbon::now()->addDays(4)->toDateTimeString(), // overlaps
            'scheduled_end' => Carbon::now()->addDays(6)->toDateTimeString(),
            'actual_area' => 10,
        ];

        $response = $this->postJson('/api/bookings', $payload);

        $response->assertStatus(422)
                 ->assertJson([ 'message' => 'จองไม่สำเร็จ' ]);
    }
}
