<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ZoomAccount;
use App\Models\User;

class ZoomAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء حسابات Zoom تجريبية
        $accounts = [
            [
                'name' => 'حساب Zoom الرئيسي',
                'email' => 'main@zoom.edu-dream.com',
                'account_id' => 'm8VMK4ZyRkeAN0btuHP_mA',
                'client_id' => 'A_YMIa68Rky5zPRCGHyxOw',
                'client_secret' => 'bUKVISRcjhcxMuViOj39hqzi5lt5z44n6',
                'description' => 'الحساب الرئيسي لمنصة إيدو دريم',
                'max_meetings_per_day' => 100,
                'max_participants' => 300,
                'features' => ['recording', 'transcription', 'breakout_rooms', 'polling'],
                'is_active' => true,
            ],
            [
                'name' => 'حساب Zoom للمعلمين',
                'email' => 'teachers@zoom.edu-dream.com',
                'account_id' => 'teacher_account_001',
                'client_id' => 'teacher_client_001',
                'client_secret' => 'teacher_secret_001',
                'description' => 'حساب مخصص للمعلمين',
                'max_meetings_per_day' => 50,
                'max_participants' => 100,
                'features' => ['recording', 'polling'],
                'is_active' => true,
            ],
            [
                'name' => 'حساب Zoom للدورات المتقدمة',
                'email' => 'advanced@zoom.edu-dream.com',
                'account_id' => 'advanced_account_001',
                'client_id' => 'advanced_client_001',
                'client_secret' => 'advanced_secret_001',
                'description' => 'حساب للدورات المتقدمة مع ميزات إضافية',
                'max_meetings_per_day' => 200,
                'max_participants' => 500,
                'features' => ['recording', 'transcription', 'breakout_rooms', 'polling'],
                'is_active' => true,
            ]
        ];

        foreach ($accounts as $accountData) {
            // البحث عن مستخدم admin أو إنشاء واحد
            $admin = User::role('admin')->first();
            if (!$admin) {
                $admin = User::first();
            }

            ZoomAccount::create([
                ...$accountData,
                'created_by' => $admin ? $admin->id : 1,
                'updated_by' => $admin ? $admin->id : 1,
            ]);
        }

        $this->command->info('تم إنشاء حسابات Zoom التجريبية بنجاح!');
    }
}
