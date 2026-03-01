<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Equipment;
use App\Models\Booking;

class AdminJobUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $customer;
    protected $equipment;
    protected $job;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin', 'username' => 'admin_test2']); 
        
        $this->customer = Customer::create([
            'name' => 'Farm Owner', 
            'phone' => '0822222222',
            'password' => '1234'
        ]);
        
        $this->equipment = Equipment::create([
            'name' => 'Harvester H1', 
            'equipment_code' => 'HV-001', 
            'current_status' => 'available',
            'type' => 'harvester', 
            'maintenance_hour_threshold' => 500.00, 
            'price_per_rai' => 200 // Important for calculation
        ]);

        $this->job = Booking::create([
            'job_number' => 'JOB-ADM-001',
            'customer_id' => $this->customer->id,
            'equipment_id' => $this->equipment->id,
            'scheduled_start' => '2025-12-10 09:00:00',
            'scheduled_end' => '2025-12-10 17:00:00',
            'total_price' => 0,
            'actual_area' => 0,
            'status' => 'scheduled',
            'price_per_rai_at_booking' => 200 // Snapshot price
        ]);
    }

    public function test_updating_actual_area_recalculates_total_price()
    {
        $response = $this->actingAs($this->admin)->put(route('admin.jobs.update', $this->job->id), [
            'status' => 'in_progress',
            'actual_area' => 15, // Change area to 15
        ]);

        $response->assertRedirect(route('admin.jobs.index'));
        
        // 15 * 200 = 3000
        $this->assertDatabaseHas('bookings', [
            'id' => $this->job->id,
            'actual_area' => 15,
            'total_price' => 3000,
            'status' => 'in_progress'
        ]);
    }

    public function test_admin_can_override_total_price()
    {
        $response = $this->actingAs($this->admin)->put(route('admin.jobs.update', $this->job->id), [
            'status' => 'in_progress',
            'actual_area' => 15,
            'total_price' => 2500, // Override with discount
        ]);

        $response->assertRedirect(route('admin.jobs.index'));
        
        $this->assertDatabaseHas('bookings', [
            'id' => $this->job->id,
            'actual_area' => 15,
            'total_price' => 2500 // Overriden value
        ]);
    }
}
