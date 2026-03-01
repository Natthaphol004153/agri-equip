<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // 1. ดึงรายชื่อลูกค้าพร้อม pagination และค้นหา
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $q = $request->query('q');

        $query = Customer::orderBy('id', 'desc');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('customer_code', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        return $query->paginate($perPage);
    }

    // 2. เพิ่มลูกค้าใหม่
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'customer_type' => 'required|in:individual,farm',
            'address' => 'nullable|string',
            'password' => 'required|string|min:4', // บังคับรับรหัสผ่านด้วย
        ]);

        $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);

        $customer = Customer::create($validated);
        return response()->json($customer, 201);
    }

    // 3. แก้ไขข้อมูลลูกค้า
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'customer_type' => 'required|in:individual,farm',
            'address' => 'nullable|string',
            'password' => 'nullable|string|min:4'
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $customer->update($validated);
        return $customer;
    }

    // 4. ลบลูกค้า
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        // เช็คก่อนว่ามีประวัติจองงานไหม (ถ้ามีไม่ควรลบ)
        if ($customer->bookings()->exists()) {
             return response()->json(['message' => 'ไม่สามารถลบได้ เนื่องจากมีประวัติการจองงานอยู่'], 400);
        }
        
        $customer->delete();
        return response()->json(['message' => 'ลบข้อมูลเรียบร้อย']);
    }
}