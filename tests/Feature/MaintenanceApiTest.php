<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Equipment;
use App\Models\MaintenanceLog;

class MaintenanceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_maintenance_changes_status_and_returns_201()
    {
        $equipment = Equipment::create([
            'equipment_code' => 'EQ-100',
            'name' => 'Harvester 1',
            'type' => 'harvester',
            'maintenance_hour_threshold' => 200,
            'current_status' => 'available',
            'current_hours' => 150
        ]);

        $payload = [
            'equipment_id' => $equipment->id,
            'maintenance_type' => 'corrective',
            'description' => 'Engine failure'
        ];

        $response = $this->postJson('/api/maintenance/report', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'data' => ['id','equipment_id','maintenance_type']]);

        $this->assertDatabaseHas('maintenance_logs', [
            'equipment_id' => $equipment->id,
            'maintenance_type' => 'corrective'
        ]);

        $this->assertDatabaseHas('equipment', [
            'id' => $equipment->id,
            'current_status' => 'breakdown'
        ]);
    }

    public function test_complete_maintenance_updates_log_without_resetting_meter()
    {
        $equipment = Equipment::create([
            'equipment_code' => 'EQ-200',
            'name' => 'Sprayer A',
            'type' => 'sprayer',
            'maintenance_hour_threshold' => 100,
            'current_status' => 'maintenance',
            'current_hours' => 500
        ]);

        $log = MaintenanceLog::create([
            'equipment_id' => $equipment->id,
            'maintenance_type' => 'preventive',
            'description' => 'Routine check'
        ]);

        $payload = [
            'total_cost' => 1500,
            'service_provider' => 'Local Shop',
            'reset_counter' => true
        ];

        $response = $this->postJson("/api/maintenance/{$log->id}/complete", $payload);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'ปิดงานซ่อมสำเร็จ เครื่องจักรพร้อมใช้งาน']);

        $this->assertDatabaseHas('maintenance_logs', [
            'id' => $log->id,
            'total_cost' => 1500,
            'reset_counter' => 1,
            'service_meter_reading' => 500,
        ]);

        $this->assertDatabaseHas('equipment', [
            'id' => $equipment->id,
            'current_status' => 'available',
            'current_hours' => 500,
        ]);
    }
}
