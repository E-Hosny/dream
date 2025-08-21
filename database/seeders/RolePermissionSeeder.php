<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء الصلاحيات
        $permissions = [
            // صلاحيات المستخدمين
            "users.view",
            "users.create", 
            "users.edit",
            "users.delete",
            
            // صلاحيات الأدوار
            "roles.view",
            "roles.create",
            "roles.edit", 
            "roles.delete",
            
            // صلاحيات الصلاحيات
            "permissions.view",
            "permissions.create",
            "permissions.edit",
            "permissions.delete",
            
            // صلاحيات الكورسات
            "courses.view",
            "courses.create",
            "courses.edit",
            "courses.delete",
            
            // صلاحيات الدروس
            "lessons.view", 
            "lessons.create",
            "lessons.edit",
            "lessons.delete",
            
            // صلاحيات التقييمات
            "assessments.view",
            "assessments.create", 
            "assessments.edit",
            "assessments.delete",
            
            // صلاحيات الطلاب
            "students.view",
            "students.enroll",
            "students.progress",
            
            // صلاحيات التقارير
            "reports.view",
            "reports.generate",
            
            // صلاحيات النظام
            "system.settings",
            "system.backup",
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(["name" => $permission]);
        }

        // إنشاء الأدوار
        $adminRole = Role::firstOrCreate(["name" => "admin"]);
        $teacherRole = Role::firstOrCreate(["name" => "teacher"]);  
        $studentRole = Role::firstOrCreate(["name" => "student"]);

        // إعطاء جميع الصلاحيات للمدير
        $adminRole->givePermissionTo(Permission::all());

        // إعطاء صلاحيات المعلم
        $teacherRole->givePermissionTo([
            "courses.view", "courses.create", "courses.edit",
            "lessons.view", "lessons.create", "lessons.edit", "lessons.delete",
            "assessments.view", "assessments.create", "assessments.edit", "assessments.delete", 
            "students.view", "students.progress",
            "reports.view",
        ]);

        // إعطاء صلاحيات الطالب
        $studentRole->givePermissionTo([
            "courses.view",
            "lessons.view", 
            "assessments.view",
        ]);

        // إنشاء مستخدم مدير
        $admin = User::firstOrCreate([
            "email" => "admin@edudream.com",
        ], [
            "name" => "مدير النظام",
            "password" => bcrypt("password"),
        ]);
        $admin->assignRole("admin");

        // إنشاء مستخدم معلم
        $teacher = User::firstOrCreate([
            "email" => "teacher@edudream.com",
        ], [
            "name" => "أ. محمد أحمد",
            "password" => bcrypt("password"),
        ]);
        $teacher->assignRole("teacher");

        // إنشاء مستخدم طالب
        $student = User::firstOrCreate([
            "email" => "student@edudream.com",
        ], [
            "name" => "سارة محمود",
            "password" => bcrypt("password"),
        ]);
        $student->assignRole("student");
    }
}
