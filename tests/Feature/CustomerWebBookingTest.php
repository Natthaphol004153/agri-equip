<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\Equipment;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CustomerWebBookingTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $equipment;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customer = Customer::create([
            'name' => 'Web Customer', 
            'phone' => '0999999999',
            'password' => Hash::make('password123'),
            'customer_type' => 'individual',
            'customer_code' => 'CUST-009'
        ]);
        
        $this->equipment = Equipment::create([
            'name' => 'Drone Z1', 
            'equipment_code' => 'DR-001', 
            'current_status' => 'available',
            'type' => 'drone', 
            'maintenance_hour_threshold' => 100.00, 
            'hourly_rate' => 500.00,
            'price_per_rai' => 150
        ]);
    }

    public function test_customer_can_create_booking_via_web_dashboard()
    {
        $start = Carbon::now()->addDays(2)->format('Y-m-d');
        
        $response = $this->actingAs($this->customer, 'customer')->post(route('customer.bookings.store'), [
            'equipment_id' => $this->equipment->id,
            'start_date' => $start,
            'start_time' => '09:00',
            'end_time' => '12:00',
            'note' => 'Please arrive early'
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('bookings', [
            'customer_id' => $this->customer->id,
            'equipment_id' => $this->equipment->id,
            'note' => 'Please arrive early',
            'status' => 'pending_approval',
            'payment_status' => 'pending',
            'total_price' => 0 // Initially 0 as per requirements
        ]);
    }

    public function test_customer_cannot_double_book_equipment()
    {
        $start = Carbon::now()->addDays(3)->format('Y-m-d');
        
        // 1. Create an existing booking
        Booking::create([
            'job_number' => 'JOB-WEB-001',
            'customer_id' => $this->customer->id,
            'equipment_id' => $this->equipment->id,
            'scheduled_start' => $start . ' 09:00:00',
            'scheduled_end' => $start . ' 12:00:00',
            'status' => 'pending_approval',
            'total_price' => 0,
            'actual_area' => 0
        ]);

        // 2. Try to book the same time
        $response = $this->actingAs($this->customer, 'customer')->post(route('customer.bookings.store'), [
            'equipment_id' => $this->equipment->id,
            'start_date' => $start,
            'start_time' => '10:00',
            'end_time' => '13:00', // overlaps
        ]);

        $response->assertSessionHasErrors(['time_slot']);
        
        // Ensure only the first one exists
        $this->assertDatabaseCount('bookings', 1);
    }
}
