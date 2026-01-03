<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $customers = [
            ['name' => 'กำนันแม้น', 'type' => 'individual', 'phone' => '081-111-1111', 'address' => '12/3 หมู่ 1 ต.บ้านนา อ.เมือง จ.ขอนแก่น'],
            ['name' => 'เจ๊แต๋ว สวนผลไม้', 'type' => 'farm', 'phone' => '089-222-2222', 'address' => 'สวนป้าแต๋ว ระยอง ฮิ'],
            ['name' => 'บจก. เกษตรรุ่งเรือง', 'type' => 'company', 'phone' => '02-333-4444', 'address' => '88 นิคมอุตสาหกรรมนวนคร'],
            ['name' => 'ลุงมี นาข้าว', 'type' => 'individual', 'phone' => '085-555-5555', 'address' => 'ทุ่งกุลาร้องไห้ จ.ร้อยเอ็ด'],
            ['name' => 'ไร่อ้อย สุขใจ', 'type' => 'farm', 'phone' => '090-666-6666', 'address' => 'อ.ท่าม่วง จ.กาญจนบุรี'],
            ['name' => 'ผู้ใหญ่บ้าน สมยศ', 'type' => 'individual', 'phone' => '087-777-8888', 'address' => 'หมู่ 5 ต.หนองหญ้าไซ'],
            ['name' => 'สหกรณ์โคนม วังน้ำเขียว', 'type' => 'company', 'phone' => '044-999-9999', 'address' => 'อ.วังน้ำเขียว จ.นครราชสีมา'],
        ];

        foreach ($customers as $index => $c) {
            Customer::firstOrCreate(
                ['name' => $c['name']],
                [
                    'customer_code' => 'CUS-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'customer_type' => $c['type'],
                    'phone' => $c['phone'],
                    'address' => $c['address'],
                ]
            );
        }
    }
}