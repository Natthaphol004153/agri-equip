<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class StaffLoginController extends Controller
{
    // แสดงหน้าเลือกพนักงาน
    public function showLoginForm()
    {
        // ดึงเฉพาะพนักงาน (Staff) ที่มี PIN และบัญชียัง Active อยู่
        $staffs = User::where('role', 'staff')
                      ->whereNotNull('pin')
                      ->where('is_active', true)
                      ->get();

        return view('auth.staff-login', compact('staffs'));
    }

    // ตรวจสอบ PIN และ Login
    public function login(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pin' => 'required|string',
        ]);

        $user = User::find($request->user_id);

        // เช็คว่า PIN ตรงกับที่ Hash ไว้ใน Database หรือไม่
        if ($user && Hash::check($request->pin, $user->pin)) {
            Auth::login($user);
            
            // Login สำเร็จ -> ไปหน้า Dashboard พนักงาน
            return redirect()->route('staff.dashboard'); 
        }

        // ถ้าผิด ให้กลับไปหน้าเดิมพร้อมแจ้งเตือน
        return back()->with('error', 'รหัส PIN ไม่ถูกต้อง!');
    }
}