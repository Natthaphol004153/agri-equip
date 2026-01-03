<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // 1. ข้อมูลบริษัท
            ['key' => 'company_name', 'value' => 'AgriTech Service Co., Ltd.', 'group' => 'general'],
            ['key' => 'company_address', 'value' => '123 หมู่ 4 ต.หนองนา อ.เมือง จ.ขอนแก่น 40000', 'group' => 'general'],
            ['key' => 'company_phone', 'value' => '081-234-5678', 'group' => 'general'],
            ['key' => 'company_tax_id', 'value' => '0125556001234', 'group' => 'general'],

            // 2. การเงิน
            ['key' => 'promptpay_number', 'value' => '0812345678', 'group' => 'payment'],
            ['key' => 'bank_account_name', 'value' => 'บจก. แอกกริเทค เซอร์วิส', 'group' => 'payment'],
            ['key' => 'bank_name', 'value' => 'กสิกรไทย (KBANK)', 'group' => 'payment'],
            ['key' => 'bank_account_number', 'value' => '123-4-56789-0', 'group' => 'payment'],
            ['key' => 'deposit_percentage', 'value' => '30', 'group' => 'payment'], // มัดจำ 30%

            // 3. การแจ้งเตือน Line Notify
            ['key' => 'line_token_admin', 'value' => '', 'group' => 'notification'], // แจ้งแอดมิน
            ['key' => 'line_token_staff', 'value' => '', 'group' => 'notification'], // แจ้งกลุ่มงานช่าง
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}