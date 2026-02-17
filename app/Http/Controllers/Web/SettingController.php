<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage; // ✅ เพิ่มบรรทัดนี้

class SettingController extends Controller
{
    public function index()
    {
        // ดึงการตั้งค่าทั้งหมดมาแสดง
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // 1. จัดการข้อมูล Settings ทั่วไป (Loop เก็บลง DB)
        $data = $request->except(['_token', 'banner_images', 'delete_banners']);
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // 2. จัดการรูปภาพ Banner (JSON Array)
        // ดึงข้อมูลรูปเก่าจาก DB (ถ้าไม่มีให้เป็น Array ว่าง)
        $currentBanners = json_decode(Setting::where('key', 'home_banners')->value('value'), true) ?? [];

        // 2.1 ถ้ามีการติ๊กเลือก "ลบรูป"
        if ($request->has('delete_banners')) {
            foreach ($request->delete_banners as $pathToDelete) {
                // ลบไฟล์จริงออกจาก Storage
                if (Storage::disk('public')->exists($pathToDelete)) {
                    Storage::disk('public')->delete($pathToDelete);
                }
                // ลบออกจาก Array (ค้นหาค่าแล้วลบออก)
                $currentBanners = array_diff($currentBanners, [$pathToDelete]);
            }
        }

        // 2.2 ถ้ามีการ "อัปโหลดรูปใหม่"
        if ($request->hasFile('banner_images')) {
            foreach ($request->file('banner_images') as $file) {
                // Save ลงโฟลเดอร์ banners ใน storage/app/public
                $path = $file->store('banners', 'public');
                $currentBanners[] = $path; // เพิ่ม path ลงใน Array
            }
        }

        // 2.3 บันทึก Array กลับลง DB ในรูปแบบ JSON (เรียง index ใหม่ด้วย array_values)
        Setting::updateOrCreate(
            ['key' => 'home_banners'], 
            ['value' => json_encode(array_values($currentBanners))]
        );

        return redirect()->back()->with('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
    }
}