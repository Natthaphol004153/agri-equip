<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. ตรวจสอบว่าส่ง username และ password มาหรือไม่
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // 2. เช็ค Username/Password กับฐานข้อมูล
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // เช็ค Role: ถ้าชื่อ admin ให้เป็นแอดมิน ที่เหลือเป็น staff
            $role = ($user->username === 'admin') ? 'admin' : 'staff';


            // Create API token for the user
            if (method_exists($user, 'createToken')) {
                $token = $user->createToken('api-token')->plainTextToken;
            } else {
                $token = null;
            }

            return response()->json([
                'success' => true,
                'message' => 'ยินดีต้อนรับ ' . $user->name,
                'user' => $user,
                'role' => $role,
                'access_token' => $token,
                'token' => $token
            ]);
        }

        // 3. ถ้าล็อกอินไม่ผ่าน
        return response()->json(['success' => false, 'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'], 401);
    }

    public function logout(Request $request)
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Logged out']);
    }
}