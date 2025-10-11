<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;
use Exception;

class MailTestController extends Controller
{
    /**
     * إرسال إيميل تجريبي
     */
    public function sendTestEmail(Request $request)
    {
        try {
            $email = $request->input('email', 'ebrahimhosny511@gmail.com');
            $userName = $request->input('name', 'إبراهيم حسني');
            $message = $request->input('message', 'مرحباً! هذا إيميل تجريبي من منصة إيدو دريم التعليمية لاختبار خدمة Mailgun.');

            // إرسال الإيميل
            Mail::to($email)->send(new TestEmail($userName, $message));

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال الإيميل التجريبي بنجاح!',
                'email_sent_to' => $email,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في إرسال الإيميل',
                'error' => $e->getMessage(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 500);
        }
    }

    /**
     * عرض صفحة اختبار الإيميل
     */
    public function showTestForm()
    {
        return view('emails.test-form');
    }

    /**
     * اختبار إعدادات Mailgun
     */
    public function testMailgunSettings()
    {
        try {
            $mailgunDomain = config('services.mailgun.domain');
            $mailgunSecret = config('services.mailgun.secret');
            $mailgunEndpoint = config('services.mailgun.endpoint');
            
            if (!$mailgunDomain || !$mailgunSecret) {
                return response()->json([
                    'success' => false,
                    'message' => 'إعدادات Mailgun غير مكتملة',
                    'missing' => [
                        'domain' => !$mailgunDomain ? 'غير موجود' : 'موجود',
                        'secret' => !$mailgunSecret ? 'غير موجود' : 'موجود',
                        'endpoint' => $mailgunEndpoint ?: 'افتراضي'
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'إعدادات Mailgun صحيحة ومكتملة',
                'settings' => [
                    'domain' => $mailgunDomain,
                    'endpoint' => $mailgunEndpoint,
                    'mailer' => config('mail.default'),
                    'from_address' => config('mail.from.address'),
                    'from_name' => config('mail.from.name')
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في فحص الإعدادات',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
