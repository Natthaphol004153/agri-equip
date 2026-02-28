<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('customer_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $customers = $query->latest()->paginate(10);
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        // 1. เคลียร์เบอร์โทร
        $cleanPhone = str_replace(['-', ' '], '', $request->phone);
        $request->merge(['phone' => $cleanPhone]);

        // 2. Validate ข้อมูล (เพิ่ม farm_area, latitude, longitude)
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'customer_type' => 'required|in:individual,farm,company',
            'tax_id' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'province' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'farm_area' => 'nullable|numeric|min:0', // ✅ เพิ่ม
            'latitude' => 'nullable|string|max:50',  // ✅ เพิ่ม
            'longitude' => 'nullable|string|max:50', // ✅ เพิ่ม
            'profile_image' => 'nullable|image|max:5120',
        ]);

        // 3. Auto Generate Code
        $latestCustomer = Customer::latest('id')->first();
        if ($latestCustomer && preg_match('/CUS-(\d+)/', $latestCustomer->customer_code, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        $data['customer_code'] = 'CUS-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // 4. Create Password
        $rawPassword = substr($cleanPhone, -4);
        $data['password'] = Hash::make($rawPassword);

        // 5. อัปโหลดรูปภาพ (ถ้ามี)
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('customers', 'public');
            $data['profile_image'] = $path;
        }

        Customer::create($data);

        return redirect()->route('admin.customers.index')
            ->with('success', "เพิ่มลูกค้าสำเร็จ! รหัส: {$data['customer_code']} | รหัสผ่าน: {$rawPassword}");
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $cleanPhone = str_replace(['-', ' '], '', $request->phone);
        $request->merge(['phone' => $cleanPhone]);

        // Validate ข้อมูล (เพิ่ม farm_area, latitude, longitude)
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone,' . $id,
            'customer_type' => 'required|in:individual,farm,company',
            'tax_id' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'province' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'farm_area' => 'nullable|numeric|min:0', // ✅ เพิ่ม
            'latitude' => 'nullable|string|max:50',  // ✅ เพิ่ม
            'longitude' => 'nullable|string|max:50', // ✅ เพิ่ม
            'profile_image' => 'nullable|image|max:5120',
        ]);

        // อัปเดตรูปภาพ
        if ($request->hasFile('profile_image')) {
            if ($customer->profile_image) {
                Storage::disk('public')->delete($customer->profile_image);
            }
            $path = $request->file('profile_image')->store('customers', 'public');
            $data['profile_image'] = $path;
        }

        $customer->update($data);

        return redirect()->route('admin.customers.index')
            ->with('success', "แก้ไขข้อมูลลูกค้าเรียบร้อยแล้ว");
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        
        if ($customer->profile_image) {
            Storage::disk('public')->delete($customer->profile_image);
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'ลบข้อมูลลูกค้าเรียบร้อยแล้ว');
    }
}