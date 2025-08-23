<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ZoomAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->role, function ($query, $role) {
                $query->role($role);
            })
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
            ->paginate(10)
            ->withQueryString();

        $roles = Role::all();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => $request->only(['search', 'role'])
        ]);
    }

    public function create()
    {
        $roles = Role::all();
        
        return Inertia::render('Admin/Users/Create', [
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'exists:roles,name']
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'teachingCourses', 'enrolledCourses']);
        
        return Inertia::render('Admin/Users/Show', [
            'user' => $user
        ]);
    }

    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::all();
        
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => $roles
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'exists:roles,name']
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')->with('success', 'تم تحديث المستخدم بنجاح');
    }

    public function destroy(User $user)
    {
        // منع حذف المدير الرئيسي
        if ($user->hasRole('admin') && User::role('admin')->count() <= 1) {
            return back()->with('error', 'لا يمكن حذف المدير الوحيد في النظام');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم بنجاح');
    }

    // صفحات منفصلة للمعلمين والطلاب
    public function teachers(Request $request)
    {
        $teachers = User::role('teacher')
            ->with(['roles', 'teachingCourses', 'zoomAccount'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
            ->paginate(10)
            ->withQueryString();

        $zoomAccounts = ZoomAccount::active()->with('teachers')->get(['id', 'name', 'email', 'is_active']);

        return Inertia::render('Admin/Teachers/Index', [
            'teachers' => $teachers,
            'zoomAccounts' => $zoomAccounts,
            'filters' => $request->only(['search'])
        ]);
    }

    public function students(Request $request)
    {
        $students = User::role('student')
            ->with(['roles', 'enrolledCourses'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Students/Index', [
            'students' => $students,
            'filters' => $request->only(['search'])
        ]);
    }

    /**
     * ربط المعلم بحساب Zoom
     */
    public function linkZoomAccount(Request $request, User $teacher)
    {
        $request->validate([
            'zoom_account_id' => 'required|exists:zoom_accounts,id'
        ]);

        // التحقق من أن المستخدم معلم
        if (!$teacher->hasRole('teacher')) {
            return response()->json([
                'success' => false,
                'message' => 'يمكن ربط حسابات Zoom بالمعلمين فقط'
            ], 400);
        }

        // التحقق من أن الحساب متاح
        $zoomAccount = ZoomAccount::find($request->zoom_account_id);
        if ($zoomAccount->teachers()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'هذا الحساب مرتبط بمعلم آخر'
            ], 400);
        }

        try {
            $teacher->update(['zoom_account_id' => $request->zoom_account_id]);

            return response()->json([
                'success' => true,
                'message' => 'تم ربط المعلم بحساب Zoom بنجاح',
                'zoom_account' => $zoomAccount->only(['id', 'name', 'email'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء ربط الحساب'
            ], 500);
        }
    }

    /**
     * إلغاء ربط المعلم بحساب Zoom
     */
    public function unlinkZoomAccount(User $teacher)
    {
        try {
            $teacher->update(['zoom_account_id' => null]);

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء ربط المعلم بحساب Zoom بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إلغاء ربط الحساب'
            ], 500);
        }
    }
}
