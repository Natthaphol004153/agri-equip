<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAuthController extends Controller
{
    // 1. แสดงฟอร์ม Login
    public function showLoginForm()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }
        return view('auth.customer-login');
    }

    // 2. ประมวลผล Login
    public function login(Request $request)
    {
        // Validate ข้อมูล
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        // ลอง Login ด้วย Guard 'customer'
        // ข้อมูลที่ใช้เช็คคือ phone และ password
        $credentials = [
            'phone' => $request->phone,
            'password' => $request->password
        ];

        if (Auth::guard('customer')->attempt($credentials, $request->filled('remember'))) {
            // ถ้าผ่าน ให้ Regenerate Session เพื่อความปลอดภัย
            $request->session()->regenerate();

            return redirect()->intended(route('customer.dashboard'));
        }

        // ถ้าไม่ผ่าน
        return back()->withErrors([
            'phone' => 'เบอร์โทรศัพท์หรือรหัสผ่านไม่ถูกต้อง',
        ])->withInput($request->only('phone'));
    }

    // 3. Logout
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }
}