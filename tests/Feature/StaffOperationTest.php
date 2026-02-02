<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile; // ✅ เพิ่ม: สำหรับจำลองไฟล์รูป
use Illuminate\Support\Facades\Storage; // ✅ เพิ่ม: สำหรับ Mock Storage
use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Customer;
use App\Models\FuelTank;

class StaffOperationTest extends TestCase
{
    use RefreshDatabase;

    protected $staff;
    protected $equipment;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();
        // Mock Storage เพื่อไม่ให้ไฟล์ขยะรกเครื่องจริง
        Storage::fake('public');

        $this->staff = User::factory()->create(['role' => 'staff', 'username' => 'staff01']);
        $this->customer = Customer::create(['name' => 'Lung Mee', 'phone' => '0811111111']);
        $this->equipment = Equipment::create([
            'name' => 'Tractor T1',
            'equipment_code' => 'TR-001',
            'current_status' => 'available',
            'type' => 'tractor',
            'maintenance_hour_threshold' => 100
        ]);
    }

    public function test_staff_can_start_and_finish_job()
    {
        // 1. สร้างงาน (จ่ายเงินครบแล้ว เพื่อเลี่ยงการตรวจสลิป EasySlip ใน Test)
        $job = Booking::create([
            'job_number' => 'JOB-TEST-001',
            'customer_id' => $this->customer->id,
            'equipment_id' => $this->equipment->id,
            'assigned_staff_id' => $this->staff->id,
            'scheduled_start' => now()->addHour(),
            'scheduled_end' => now()->addHours(2),
            'total_price' => 1000,
            'deposit_amount' => 1000, // ✅ จ่ายครบ Balance = 0 (ไม่ต้องตรวจสลิป)
            'status' => 'scheduled'
        ]);

        // 2. พนักงานกด Start
        $this->actingAs($this->staff)->post(route('staff.jobs.start', $job->id));
        
        $this->assertDatabaseHas('bookings', [
            'id' => $job->id,
            'status' => 'in_progress'
        ]);

        // 3. พนักงานกด Finish (ต้องส่งรูปภาพด้วย)
        $responseFinish = $this->actingAs($this->staff)->post(route('staff.jobs.finish', $job->id), [
            'job_image' => UploadedFile::fake()->image('job_done.jpg'), // ✅ จำลองรูปหน้างาน
            'note' => 'Done without issues'
        ]);

        $responseFinish->assertRedirect(); // เช็คว่าไม่ Error (Validation ผ่าน)

        // 4. เช็คสถานะ (Controller คุณใช้ 'completed_pending_approval')
        $job->refresh();
        $this->assertEquals('completed_pending_approval', $job->status);
    }

    public function test_staff_fuel_usage_deducts_stock()
    {
        $tank = FuelTank::create([
            'name' => 'Diesel Tank',
            'capacity' => 1000,
            'current_balance' => 100,
            'fuel_type' => 'Diesel'
        ]);

        // พนักงานเบิกน้ำมัน (ต้องระบุ fuel_source)
        $response = $this->actingAs($this->staff)->post(route('staff.fuel.store'), [
            'equipment_id' => $this->equipment->id,
            'fuel_source' => 'internal', // ✅ เพิ่ม: ระบุว่าเป็นของบริษัท
            'fuel_tank_id' => $tank->id,
            'liters' => 20,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success'); // เช็คว่าสำเร็จจริง

        // ตรวจสอบว่าสต็อกลดลงจริง (100 - 20 = 80)
        $this->assertDatabaseHas('fuel_tanks', [
            'id' => $tank->id,
            'current_balance' => 80
        ]);
    }
    // ✅ 3. ทดสอบว่า: ถ้ายังจ่ายไม่ครบ จะปิดงานไม่ได้ถ้าไม่แนบสลิป
    public function test_staff_cannot_finish_unpaid_job_without_slip()
    {
        // 1. สร้างงานที่ยังจ่ายไม่ครบ (ยอด 5000 จ่ายมัดจำแค่ 1000 -> ค้าง 4000)
        $job = Booking::create([
            'job_number' => 'JOB-TEST-UNPAID',
            'customer_id' => $this->customer->id,
            'equipment_id' => $this->equipment->id,
            'assigned_staff_id' => $this->staff->id,
            'scheduled_start' => now()->subHour(),
            'scheduled_end' => now()->addHour(),
            'total_price' => 5000,
            'deposit_amount' => 1000, // ยังค้างจ่าย!
            'status' => 'in_progress'
        ]);

        // 2. พนักงานกด Finish (แนบแค่รูปงาน แต่ "ไม่แนบสลิป")
        $response = $this->actingAs($this->staff)->post(route('staff.jobs.finish', $job->id), [
            'job_image' => UploadedFile::fake()->image('job_done.jpg'),
            // 'payment_proof' => ไม่ส่งไฟล์นี้มา
        ]);

        // 3. ต้องเจอ Error แจ้งเตือนที่ field 'payment_proof'
        $response->assertSessionHasErrors('payment_proof');
        
        // สถานะต้องยังไม่เปลี่ยนเป็น completed
        $this->assertDatabaseHas('bookings', [
            'id' => $job->id,
            'status' => 'in_progress'
        ]);
    }
}