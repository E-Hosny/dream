<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\ZoomMeeting;

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

    /**
     * إنشاء رابط انضمام للضيوف (الطلاب)
     */
    public function generateGuestJoinUrl($meetingId, $password = null)
    {
        try {
            Log::info('Generating guest join URL for meeting ID: ' . $meetingId);
            Log::info('Password: ' . ($password ?: 'null'));
            
            // البحث عن الاجتماع في قاعدة البيانات بـ zoom_meeting_id
            $meeting = \App\Models\ZoomMeeting::where('zoom_meeting_id', $meetingId)->first();
            
            if (!$meeting) {
                Log::error('Meeting not found in database with zoom_meeting_id: ' . $meetingId);
                
                // تجربة البحث بـ ID العادي كخطة بديلة
                $meeting = \App\Models\ZoomMeeting::where('id', $meetingId)->first();
                
                if (!$meeting) {
                    Log::error('Meeting not found with regular ID either: ' . $meetingId);
                return [
                    'success' => false,
                    'message' => 'الاجتماع غير موجود في قاعدة البيانات'
                ];
            }
            
                Log::info('Meeting found using regular ID instead of zoom_meeting_id');
            }
            
            Log::info('Meeting found in database: ID=' . $meeting->id . ', zoom_meeting_id=' . $meeting->zoom_meeting_id . ', status=' . $meeting->status);
            
            // التحقق من حالة الاجتماع
            if ($meeting->status !== 'started') {
                Log::warning('Meeting is not started. Current status: ' . $meeting->status);
                return [
                    'success' => false,
                    'message' => 'الاجتماع ليس نشطاً حالياً'
                ];
            }
            
            // إنشاء رابط انضمام للضيوف
            $guestJoinUrl = $meeting->join_url;
            Log::info('Original join URL from database: ' . $guestJoinUrl);
            
            // إذا كان هناك كلمة مرور، أضفها للرابط
            if ($meeting->password) {
                $separator = strpos($guestJoinUrl, '?') !== false ? '&' : '?';
                $guestJoinUrl .= $separator . 'pwd=' . $meeting->password;
                Log::info('Password added to URL: ' . $guestJoinUrl);
            }
            
            $result = [
                'success' => true,
                'guest_join_url' => $guestJoinUrl,
                'meeting_id' => $meeting->zoom_meeting_id,
                'password' => $meeting->password
            ];
            
            Log::info('Guest join URL generated successfully from database: ' . json_encode($result));
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Exception in generateGuestJoinUrl: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return [
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ];
        }
    }
}
