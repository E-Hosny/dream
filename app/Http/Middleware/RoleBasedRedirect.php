<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleBasedRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // تحقق من تسجيل الدخول
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $currentRoute = $request->route()->getName();

        // إذا كان المستخدم في الصفحة الرئيسية أو dashboard العادي، وجهه حسب دوره
        if ($currentRoute === 'dashboard' || $request->path() === 'dashboard') {
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('teacher')) {
                return redirect()->route('teacher.dashboard');
            } elseif ($user->hasRole('student')) {
                return redirect()->route('student.dashboard');
            }
        }

        // تحقق من صلاحية الوصول للمسارات
        if (str_starts_with($currentRoute, 'admin.') && !$user->hasRole('admin')) {
            abort(403, 'Unauthorized access to admin area.');
        }

        if (str_starts_with($currentRoute, 'teacher.') && !$user->hasRole(['admin', 'teacher'])) {
            abort(403, 'Unauthorized access to teacher area.');
        }

        if (str_starts_with($currentRoute, 'student.') && !$user->hasRole(['admin', 'teacher', 'student'])) {
            abort(403, 'Unauthorized access to student area.');
        }

        return $next($request);
    }
}
