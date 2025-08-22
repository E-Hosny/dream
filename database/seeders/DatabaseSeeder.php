<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // تشغيل seeder الأدوار والصلاحيات أولاً
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // البحث عن المستخدمين الموجودين
        $admin = User::where('email', 'admin@edudream.com')->first();
        $teacher = User::where('email', 'teacher@edudream.com')->first();
        $student = User::where('email', 'student@edudream.com')->first();

        // إنشاء مستخدمين إضافيين إذا لم يكونوا موجودين
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'),
            ]);
            $admin->assignRole('admin');
        }

        if (!$teacher) {
            $teacher = User::create([
                'name' => 'Test Teacher',
                'email' => 'teacher@teacher.com',
                'password' => Hash::make('password'),
            ]);
            $teacher->assignRole('teacher');
        }

        if (!$student) {
            $student = User::create([
                'name' => 'Student One',
                'email' => 'student1@student.com',
                'password' => Hash::make('password'),
            ]);
            $student->assignRole('student');

            // إنشاء طالب إضافي
            User::create([
                'name' => 'Student Two',
                'email' => 'student2@student.com',
                'password' => Hash::make('password'),
            ])->assignRole('student');
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@admin.com / password');
        $this->command->info('Teacher: teacher@teacher.com / password');
        $this->command->info('Student: student1@student.com / password');
    }
}
