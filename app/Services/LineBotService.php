<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineBotService
{
    /**
     * ส่งข้อความแจ้งเตือนหา Admin (Push Message)
     * @param string $message ข้อความที่ต้องการส่ง
     * @return bool
     */
    public static function sendAdminNotify($message)
    {
        // Support both legacy and current .env key names.
        $token = env('LINE_BOT_TOKEN') ?: env('LINE_CHANNEL_ACCESS_TOKEN');
        $adminId = env('LINE_ADMIN_ID') ?: env('LINE_ADMIN_USER_ID');
        
        $url = 'https://api.line.me/v2/bot/message/push';

        if (!$token || !$adminId) {
            Log::warning('LINE Bot credentials missing', [
                'has_token' => (bool) $token,
                'has_admin_id' => (bool) $adminId,
                'expected_keys' => [
                    'LINE_BOT_TOKEN or LINE_CHANNEL_ACCESS_TOKEN',
                    'LINE_ADMIN_ID or LINE_ADMIN_USER_ID',
                ],
            ]);
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'to' => $adminId, // ส่งไปหา Admin ID ที่เราตั้งไว้ใน .env
                'messages' => [
                    [
                        'type' => 'text',
                        'text' => $message
                    ]
                ]
            ]);

            if (!$response->successful()) {
                Log::error('LINE push failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('LINE push exception', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }
}