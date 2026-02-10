<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash; // ✅ 1. เพิ่มบรรทัดนี้เพื่อใช้การเข้ารหัส

class CustomerController extends Controller
{
    // หน้ารายชื่อลูกค้า
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

    // หน้าฟอร์มเพิ่ม
    public function create()
    {
        return view('admin.customers.create');
    }

    // ✅ บันทึกข้อมูลใหม่ (แก้ส่วนนี้เป็นหลัก)
    public function store(Request $request)
    {
        // 1. เคลียร์เบอร์โทร (ลบขีด - และช่องว่างออกก่อน)
        // แอดมินจะพิมพ์ 081-234-5678 หรือ 0812345678 ก็ได้ ค่าจะถูกแก้เป็น 0812345678
        $cleanPhone = str_replace(['-', ' '], '', $request->phone);
        $request->merge(['phone' => $cleanPhone]);

        // 2. Validate ข้อมูล
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone', // เพิ่ม unique เพื่อไม่ให้เบอร์ซ้ำ
            'customer_type' => 'required|in:individual,farm,company',
            'tax_id' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'province' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
        ]);

        // 3. Auto Generate Customer Code (CUS-001)
        $latestCustomer = Customer::latest('id')->first();
        if ($latestCustomer && preg_match('/CUS-(\d+)/', $latestCustomer->customer_code, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        $newCode = 'CUS-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $data['customer_code'] = $newCode;

        // ✅ 4. สร้าง Password จาก 4 ตัวท้ายของเบอร์โทร
        // ตัดเอา 4 ตัวขวาสุดจากเบอร์ที่คลีนแล้ว
        $rawPassword = substr($cleanPhone, -4);
        $data['password'] = Hash::make($rawPassword); // เข้ารหัสก่อนบันทึก

        Customer::create($data);

        return redirect()->route('admin.customers.index')
            ->with('success', "เพิ่มลูกค้าสำเร็จ! รหัสสมาชิก: $newCode | รหัสผ่านเริ่มต้นคือ: $rawPassword");
    }

    // หน้าฟอร์มแก้ไข
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    // ✅ บันทึกการแก้ไข (ปรับปรุงเรื่องเบอร์โทรนิดหน่อย)
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        // เคลียร์เบอร์โทรเหมือนกัน
        $cleanPhone = str_replace(['-', ' '], '', $request->phone);
        $request->merge(['phone' => $cleanPhone]);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone,' . $id, // อนุญาตให้ใช้เบอร์เดิมของตัวเองได้
            'customer_type' => 'required|in:individual,farm,company',
            'tax_id' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'province' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
        ]);

        // หมายเหตุ: การแก้ไขข้อมูลปกติจะไม่เปลี่ยนรหัสผ่านลูกค้า 
        // นอกจากลูกค้าแจ้งลืมรหัส เราอาจทำปุ่ม "รีเซ็ตรหัสผ่าน" แยกต่างหากในอนาคต

        $customer->update($data);

        return redirect()->route('admin.customers.index')
            ->with('success', "แก้ไขข้อมูลลูกค้า '{$customer->name}' เรียบร้อยแล้ว");
    }

    // ลบข้อมูล
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'ลบข้อมูลลูกค้าเรียบร้อยแล้ว');
    }
}