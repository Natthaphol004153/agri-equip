<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\FuelTank;
use App\Models\FuelPurchase;

class FuelStockTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // สร้าง Admin เพื่อใช้ Login
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    // ทดสอบสร้างถังน้ำมัน
    public function test_admin_can_create_fuel_tank()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.fuel.tank.store'), [
            'name' => 'Main Tank',
            'capacity' => 1000,
            'fuel_type' => 'Diesel'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('fuel_tanks', ['name' => 'Main Tank', 'current_balance' => 0]);
    }

    // ⭐ ทดสอบไฮไลท์: การคำนวณต้นทุนเฉลี่ย (Weighted Average)
    public function test_purchase_fuel_updates_average_price_correctly()
    {
        // 1. สร้างถังเปล่า
        $tank = FuelTank::create([
            'name' => 'Tank A',
            'capacity' => 1000,
            'fuel_type' => 'Diesel',
            'current_balance' => 0,
            'average_price' => 0
        ]);

        // 2. ซื้อรอบที่ 1: 100 ลิตร @ 30 บาท
        // มูลค่า = 3,000 | จำนวน = 100 | เฉลี่ย = 30.00
        $this->actingAs($this->admin)->post(route('admin.fuel.store_purchase'), [
            'fuel_tank_id' => $tank->id,
            'liters' => 100,
            'price_per_liter' => 30,
            'purchase_date' => now(),
        ]);

        $tank->refresh();
        $this->assertEquals(100, $tank->current_balance);
        $this->assertEquals(30, $tank->average_price);

        // 3. ซื้อรอบที่ 2: 100 ลิตร @ 40 บาท (แพงขึ้น)
        // ของเดิม (100*30) + ของใหม่ (100*40) = 3000 + 4000 = 7000 บาท
        // ปริมาณรวม = 200 ลิตร
        // ราคาเฉลี่ยใหม่ต้องเป็น = 7000 / 200 = 35.00 บาท
        $this->actingAs($this->admin)->post(route('admin.fuel.store_purchase'), [
            'fuel_tank_id' => $tank->id,
            'liters' => 100,
            'price_per_liter' => 40,
            'purchase_date' => now(),
        ]);

        $tank->refresh();
        $this->assertEquals(200, $tank->current_balance);
        $this->assertEquals(35, $tank->average_price);
    }

    public function test_cannot_delete_tank_with_remaining_fuel()
    {
        $tank = FuelTank::create([
            'name' => 'Tank B',
            'capacity' => 500,
            'current_balance' => 10,
            'fuel_type' => 'Gasohol'
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.fuel.tank.destroy', $tank->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('fuel_tanks', ['id' => $tank->id]);
    }
}