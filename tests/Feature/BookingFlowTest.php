<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Equipment;
use App\Models\Booking;

class BookingFlowTest extends TestCase
{
    // ใช้ RefreshDatabase เพื่อรีเซ็ตฐานข้อมูลทุกครั้งที่รันเทส (ข้อมูลจริงไม่หาย ถ้าตั้งค่า phpunit ถูกต้อง)
    use RefreshDatabase;

    protected $admin;
    protected $staff;
    protected $customer;
    protected $equipment;

    protected function setUp(): void
    {
        parent::setUp();
        
        // 1. จำลองข้อมูล User (Admin & Staff)
        // หมายเหตุ: UserFactory ของคุณมี username แล้ว ดังนั้น create ได้เลย
        $this->admin = User::factory()->create(['role' => 'admin', 'username' => 'admin_test']); 
        $this->staff = User::factory()->create(['role' => 'staff', 'username' => 'staff_test']); 
        
        // 2. จำลองข้อมูล Customer
        $this->customer = Customer::create([
            'name' => 'Test Customer', 
            'phone' => '0800000000',
            'password' => '1234'
        ]);
        
        // 3. จำลองข้อมูล Equipment (จุดที่แก้!)
        // ต้องใส่ field ให้ครบตามที่ Database บังคับ (NOT NULL)
        $this->equipment = Equipment::create([
            'name' => 'Drone T30', 
            'equipment_code' => 'EQ-001', 
            'current_status' => 'available',
            
            // ✅ เพิ่ม 2 บรรทัดนี้ ตามที่ SQL บังคับ
            'type' => 'drone', 
            'maintenance_hour_threshold' => 100.00, 
            
            // (แถม) ใส่ราคาต่อชั่วโมงไปด้วยให้สมจริง
            'hourly_rate' => 500.00
        ]);
    }

    // ✅ เคสที่ 1: จองปกติ ต้องผ่าน
    public function test_admin_can_create_normal_booking()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.jobs.store'), [
            'customer_id' => $this->customer->id,
            'equipment_id' => $this->equipment->id,
            'assigned_staff_id' => $this->staff->id,
            'scheduled_start' => '2025-12-30 09:00:00',
            'scheduled_end' => '2025-12-30 10:00:00',
            'actual_area' => 10,
            'deposit_amount' => 1000,
        ]);

        // เช็คว่า Redirect กลับไปหน้า Index และมีข้อความ Success
        $response->assertRedirect(route('admin.jobs.index'));
        $response->assertSessionHas('success');
        
        // เช็คว่าข้อมูลลง Database จริง
        $this->assertDatabaseHas('bookings', [
            'scheduled_start' => '2025-12-30 09:00:00',
        ]);
    }

    // ❌ เคสที่ 2: จองเวลาทับกัน (Overlap) ต้องไม่ผ่าน
    public function test_cannot_create_overlapping_booking()
    {
        // สร้างงานเก่าไว้ก่อน (09:00 - 10:00)
        Booking::create([
            'job_number' => 'TEST-001',
            'customer_id' => $this->customer->id,
            'equipment_id' => $this->equipment->id,
            'assigned_staff_id' => $this->staff->id,
            'scheduled_start' => '2025-12-30 09:00:00',
            'scheduled_end' => '2025-12-30 10:00:00',
            'total_price' => 5000,
            'actual_area' => 10,
            'status' => 'scheduled'
        ]);

        // พยายามจองใหม่เวลาทับกัน (09:30 - 10:30)
        $response = $this->actingAs($this->admin)->post(route('admin.jobs.store'), [
            'customer_id' => $this->customer->id,
            'equipment_id' => $this->equipment->id, // เครื่องเดิม
            'assigned_staff_id' => $this->staff->id,
            'scheduled_start' => '2025-12-30 09:30:00', // ทับกับงานเก่า
            'scheduled_end' => '2025-12-30 10:30:00',
            'actual_area' => 10,
        ]);

        // ต้องเจอ Error ใน Session
        $response->assertSessionHas('error'); 
        
        // จำนวนงานใน DB ต้องมีแค่ 1 งาน (งานใหม่ต้องไม่ถูกบันทึก)
        $this->assertDatabaseCount('bookings', 1);
    }

    // ✅ เคสที่ 3: จองวันเดียวกัน แต่เวลาไม่ชน ต้องผ่าน
    public function test_can_create_same_day_different_time_booking()
    {
        // งานเก่า (09:00 - 10:00)
        Booking::create([
            'job_number' => 'TEST-001',
            'customer_id' => $this->customer->id,
            'equipment_id' => $this->equipment->id,
            'assigned_staff_id' => $this->staff->id,
            'scheduled_start' => '2025-12-30 09:00:00',
            'scheduled_end' => '2025-12-30 10:00:00',
            'total_price' => 5000,
            'actual_area' => 10,
            'status' => 'scheduled'
        ]);

        // งานใหม่ (10:30 - 11:30) - เวลาไม่ชน
        $response = $this->actingAs($this->admin)->post(route('admin.jobs.store'), [
            'customer_id' => $this->customer->id,
            'equipment_id' => $this->equipment->id, // เครื่องเดิม
            'assigned_staff_id' => $this->staff->id,
            'scheduled_start' => '2025-12-30 10:30:00', 
            'scheduled_end' => '2025-12-30 11:30:00',
            'actual_area' => 10,
        ]);

        // ต้องผ่าน
        $response->assertRedirect(route('admin.jobs.index'));
        
        // ใน DB ต้องมี 2 งาน
        $this->assertDatabaseCount('bookings', 2);
    }
}