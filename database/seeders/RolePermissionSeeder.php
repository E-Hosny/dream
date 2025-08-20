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
            "users.view",
            "users.create",
            "users.edit",
            "users.delete",
            "roles.view",
            "roles.create",
            "roles.edit",
            "roles.delete",
            "permissions.view",
            "permissions.create",
            "permissions.edit",
            "permissions.delete",
        ];

        foreach ($permissions as $permission) {
            Permission::create(["name" => $permission]);
        }

        // إنشاء الأدوار
        $adminRole = Role::create(["name" => "admin"]);
        $userRole = Role::create(["name" => "user"]);

        // إعطاء جميع الصلاحيات للمدير
        $adminRole->givePermissionTo(Permission::all());

        // إعطاء صلاحيات محدودة للمستخدم العادي
        $userRole->givePermissionTo([
            "users.view",
        ]);

        // إنشاء مستخدم مدير
        $admin = User::create([
            "name" => "مدير النظام",
            "email" => "admin@dream.com",
            "password" => bcrypt("password"),
        ]);

        $admin->assignRole("admin");
    }
}
