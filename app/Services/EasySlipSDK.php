<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class EasySlipSDK
{
    private $apiKey;
    private $apiUrl;

    public function __construct()
    {
        $this->apiKey = env('EASYSLIP_API_KEY');
        $this->apiUrl = env('EASYSLIP_API_URL');
    }

    public function verify(UploadedFile $file): array
    {
        try {
            $realMimeType = $file->getMimeType();
            $realName = $file->getClientOriginalName();
            $fileContent = file_get_contents($file->getPathname());

            Log::info("EasySlip: Sending file...", [
                'file' => $realName,
                'size' => strlen($fileContent)
            ]);

            $response = Http::timeout(45)
                ->withHeaders(['Authorization' => 'Bearer ' . $this->apiKey])
                ->attach('file', $fileContent, $realName, ['Content-Type' => $realMimeType])
                ->post($this->apiUrl);

            if ($response->failed()) {
                $serverMsg = $response->json('message') ?? $response->body();
                Log::error("EasySlip API Error: " . json_encode($serverMsg, JSON_UNESCAPED_UNICODE));
                
                return [
                    'success' => false, 
                    'message' => "EasySlip ปฏิเสธ ({$response->status()}): " . json_encode($serverMsg, JSON_UNESCAPED_UNICODE)
                ];
            }

            return $this->processResponse($response->json());

        } catch (\Exception $e) {
            Log::error("EasySlip Exception: " . $e->getMessage());
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาดระบบ: ' . $e->getMessage()];
        }
    }

    private function processResponse($data): array
    {
        $status = $data['status'] ?? 0;
        $slipData = $data['data'] ?? [];

        // 1. ตรวจสอบ Status 200
        if ($status != 200) {
             $failMsg = $data['message'] ?? json_encode($data, JSON_UNESCAPED_UNICODE);
             return ['success' => false, 'message' => 'สลิปไม่ผ่าน: ' . $failMsg];
        }

        // 2. ดึงยอดเงิน (แก้จุดนี้ให้รองรับทั้งแบบตัวเลขตรงๆ และแบบมีไส้ใน)
        $amount = 0;
        
        if (isset($slipData['amount'])) {
            if (is_numeric($slipData['amount'])) {
                // กรณี: amount: 100.00
                $amount = (float) $slipData['amount'];
            } elseif (is_array($slipData['amount']) && isset($slipData['amount']['amount'])) {
                // กรณี: amount: { "amount": 100.00, ... } (แบบที่ KBANK ส่งมา)
                $amount = (float) $slipData['amount']['amount'];
            }
        }

        // 3. ตรวจสอบว่ายอดเงินถูกต้องหรือไม่
        if ($amount <= 0) {
            return [
                'success' => false,
                'message' => 'สลิปไม่ผ่าน: ระบบอ่านยอดเงินได้ 0.00 บาท หรือรูปแบบข้อมูลไม่ตรงกัน'
            ];
        }

        // 4. ดึงข้อมูลอื่นๆ
        $transRef = $slipData['transRef'] ?? ($slipData['trans_ref'] ?? '');
        $sender = $slipData['sender']['en_name'] ?? ($slipData['sender']['th_name'] ?? 'ไม่ระบุ');
        $bank = $slipData['sender']['bank']['short_name'] ?? 'Unknown';
        $date = $slipData['date'] ?? now()->format('Y-m-d H:i:s');

        return [
            'success' => true,
            'message' => 'ตรวจสอบสลิปสำเร็จ',
            'data' => [
                'amount' => $amount,
                'ref' => (string) $transRef,
                'sender' => (string) $sender,
                'bank' => (string) $bank,
                'date' => (string) $date
            ]
        ];
    }
}