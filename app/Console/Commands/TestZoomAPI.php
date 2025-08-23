<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZoomService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestZoomAPI extends Command
{
    protected $signature = 'zoom:test';
    protected $description = 'Test Zoom API connectivity and functionality';

    public function handle()
    {
        $this->info('🧪 Testing Zoom API...');
        
        try {
            // اختبار الاتصال الأساسي
            $this->info('1. Testing basic connection...');
            
            $accountId = config('services.zoom.account_id');
            $clientId = config('services.zoom.client_id');
            $clientSecret = config('services.zoom.client_secret');
            
            $this->info("Account ID: {$accountId}");
            $this->info("Client ID: {$clientId}");
            $this->info("Client Secret: " . ($clientSecret ? 'SET' : 'NOT SET'));
            
            // اختبار طلب Access Token
            $this->info('2. Testing OAuth token request...');
            
            $response = Http::asForm()
                ->withBasicAuth($clientId, $clientSecret)
                ->post('https://zoom.us/oauth/token', [
                    'grant_type' => 'account_credentials',
                    'account_id' => $accountId,
                ]);
            
            $this->info("Response Status: {$response->status()}");
            $this->info("Response Body: " . $response->body());
            
            if ($response->successful()) {
                $data = $response->json();
                $this->info('✅ Access Token obtained successfully!');
                $this->info("Token: " . substr($data['access_token'], 0, 20) . '...');
                $this->info("Expires in: {$data['expires_in']} seconds");
                
                // اختبار إنشاء اجتماع
                $this->info('3. Testing meeting creation...');
                
                $zoomService = new ZoomService();
                $meetingData = [
                    'topic' => 'Test Meeting',
                    'type' => 1, // Instant meeting
                    'start_time' => now()->addMinutes(5)->format('Y-m-d\TH:i:s'),
                    'duration' => 30,
                    'timezone' => 'Asia/Riyadh'
                ];
                
                $meeting = $zoomService->createMeeting('teacher@edudream.com', $meetingData);
                $this->info('✅ Meeting created successfully!');
                $this->info("Meeting ID: {$meeting['zoom_meeting_id']}");
                $this->info("Join URL: {$meeting['join_url']}");
                $this->info("Start URL: {$meeting['start_url']}");
                
            } else {
                $this->error('❌ Failed to get access token');
                $this->error("Status: {$response->status()}");
                $this->error("Body: " . $response->body());
                
                // محاولة تحليل الخطأ
                $errorData = $response->json();
                if (isset($errorData['error'])) {
                    $this->error("Error: {$errorData['error']}");
                    $this->error("Reason: " . ($errorData['reason'] ?? 'Unknown'));
                }
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Exception occurred: ' . $e->getMessage());
            Log::error('Zoom Test Command Error: ' . $e->getMessage());
        }
        
        $this->info('🏁 Test completed!');
    }
}
