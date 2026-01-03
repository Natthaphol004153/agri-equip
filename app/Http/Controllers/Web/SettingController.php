<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        // ดึงค่าทั้งหมดมาจัดกลุ่มเพื่อให้เรียกใช้ใน View ง่ายๆ
        $settings = Setting::all()->pluck('value', 'key');
        
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // รับค่าทั้งหมดจาก Form แล้ววนลูปบันทึก
        $data = $request->except(['_token']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
    }
}