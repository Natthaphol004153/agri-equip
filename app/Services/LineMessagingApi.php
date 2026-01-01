<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineMessagingApi
{
    public static function send($messageText)
    {
        $token = '1zcITAcP2atvhSmOjLLLiad53SFzbjJCczV+MQIr4X7Nc1kd0cBMfNWTJdoForwEo13+EMVy+Fzx7z7WqK4Etyswz8EJU+C3DP6NNKw3IAZbzCEtXmHGtl40TuoFJpnqnlBIl9b/gZ3EcCJCtK3YCAdB04t89/1O/w1cDnyilFU=';
        
        $userId = 'U34e1d06767c75ef2efd4fc5e37ab7da0'; // ลบ env() ออกเช่นกัน

        if (!$token || !$userId) {
            return;
        }

        try {
            // เตรียมข้อมูลสำหรับส่ง
            $payload = [
                'to' => $userId,
                'messages' => [
                    [
                        'type' => 'text',
                        'text' => $messageText
                    ]
                ]
            ];

            // ยิง Request ไปที่ Messaging API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post('https://api.line.me/v2/bot/message/push', $payload);

            // ตรวจสอบผลลัพธ์
            if ($response->failed()) {
                Log::error("LINE API Error: " . $response->body());
            }

        } catch (\Exception $e) {
            Log::error("LINE Service Error: " . $e->getMessage());
        }
    }
}