<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ZoomService
{
    protected $accountId;
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl = 'https://api.zoom.us/v2';

    public function __construct()
    {
        $this->accountId = config('services.zoom.account_id');
        $this->clientId = config('services.zoom.client_id');
        $this->clientSecret = config('services.zoom.client_secret');
    }

    /**
     * الحصول على Access Token
     */
    public function getAccessToken()
    {
        // التحقق من وجود token في cache
        if (Cache::has('zoom_access_token')) {
            return Cache::get('zoom_access_token');
        }

        try {
            $response = Http::asForm()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->post('https://zoom.us/oauth/token', [
                    'grant_type' => 'account_credentials',
                    'account_id' => $this->accountId,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'];
                
                // حفظ token في cache لمدة 50 دقيقة (Zoom token صالح لمدة ساعة)
                Cache::put('zoom_access_token', $accessToken, now()->addMinutes(50));
                
                return $accessToken;
            }

            Log::error('Zoom API Error: ' . $response->body());
            throw new \Exception('Failed to get Zoom access token: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Zoom Service Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * إنشاء اجتماع جديد
     */
    public function createMeeting($userId, $meetingData)
    {
        try {
            $accessToken = $this->getAccessToken();

            // استخدام account_id بدلاً من user email
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/users/me/meetings', [
                'topic' => $meetingData['topic'],
                'type' => $meetingData['type'] ?? 2, // Use type from meetingData or default to scheduled
                'start_time' => $meetingData['start_time'],
                'duration' => $meetingData['duration'],
                'timezone' => $meetingData['timezone'] ?? 'Asia/Riyadh',
                'password' => $meetingData['password'] ?? $this->generatePassword(),
                'settings' => [
                    'host_video' => true,
                    'participant_video' => true,
                    'join_before_host' => true,
                    'mute_upon_entry' => false,
                    'watermark' => false,
                    'use_pmi' => false,
                    'approval_type' => 0,
                    'audio' => 'both',
                    'auto_recording' => 'none'
                ]
            ]);

            if ($response->successful()) {
                $meeting = $response->json();
                
                return [
                    'zoom_meeting_id' => $meeting['id'],
                    'join_url' => $meeting['join_url'],
                    'start_url' => $meeting['start_url'],
                    'password' => $meeting['password'],
                    'host_email' => $meeting['host_email'],
                    'status' => 'created'
                ];
            }

            Log::error('Zoom Create Meeting Error: ' . $response->body());
            throw new \Exception('Failed to create Zoom meeting: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Zoom Create Meeting Service Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * تحديث اجتماع موجود
     */
    public function updateMeeting($meetingId, $meetingData)
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->patch($this->baseUrl . '/meetings/' . $meetingId, [
                'topic' => $meetingData['topic'],
                'start_time' => $meetingData['start_time'],
                'duration' => $meetingData['duration'],
                'timezone' => $meetingData['timezone'] ?? 'Asia/Riyadh',
                'settings' => [
                    'host_video' => true,
                    'participant_video' => true,
                    'join_before_host' => true,
                    'mute_upon_entry' => false
                ]
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Zoom Update Meeting Error: ' . $response->body());
            throw new \Exception('Failed to update Zoom meeting: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Zoom Update Meeting Service Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * حذف اجتماع
     */
    public function deleteMeeting($meetingId)
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->delete($this->baseUrl . '/meetings/' . $meetingId);

            if ($response->successful()) {
                return true;
            }

            Log::error('Zoom Delete Meeting Error: ' . $response->body());
            throw new \Exception('Failed to delete Zoom meeting: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Zoom Delete Meeting Service Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * الحصول على معلومات اجتماع
     */
    public function getMeeting($meetingId)
    {
        try {
            $accessToken = $this->getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->get($this->baseUrl . '/meetings/' . $meetingId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Zoom Get Meeting Error: ' . $response->body());
            throw new \Exception('Failed to get Zoom meeting: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Zoom Get Meeting Service Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * إنشاء كلمة مرور عشوائية
     */
    private function generatePassword()
    {
        return strtoupper(substr(md5(uniqid()), 0, 8));
    }
}
