<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ZoomAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ZoomAccountController extends Controller
{
    public function index()
    {
        $zoomAccounts = ZoomAccount::with('teachers')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/ZoomAccounts/Index', [
            'zoomAccounts' => $zoomAccounts
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/ZoomAccounts/Create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'account_id' => 'required|string|max:255|unique:zoom_accounts',
            'client_id' => 'required|string|max:255',
            'client_secret' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $admin = auth()->user();
            
            $zoomAccount = ZoomAccount::create([
                'name' => $request->name,
                'email' => $request->email,
                'account_id' => $request->account_id,
                'client_id' => $request->client_id,
                'client_secret' => $request->client_secret,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'created_by' => $admin->id,
                'updated_by' => $admin->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء حساب Zoom بنجاح',
                'zoom_account' => $zoomAccount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الحساب: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(ZoomAccount $zoomAccount)
    {
        $zoomAccount->load('teachers');
        
        return Inertia::render('Admin/ZoomAccounts/Show', [
            'zoomAccount' => $zoomAccount
        ]);
    }

    public function edit(ZoomAccount $zoomAccount)
    {
        return Inertia::render('Admin/ZoomAccounts/Edit', [
            'zoomAccount' => $zoomAccount
        ]);
    }

    public function update(Request $request, ZoomAccount $zoomAccount)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'account_id' => 'required|string|max:255|unique:zoom_accounts,account_id,' . $zoomAccount->id,
            'client_id' => 'required|string|max:255',
            'client_secret' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $admin = auth()->user();
            
            $zoomAccount->update([
                'name' => $request->name,
                'email' => $request->email,
                'account_id' => $request->account_id,
                'client_id' => $request->client_id,
                'client_secret' => $request->client_secret,
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'updated_by' => $admin->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حساب Zoom بنجاح',
                'zoom_account' => $zoomAccount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الحساب: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(ZoomAccount $zoomAccount)
    {
        try {
            // التحقق من عدم وجود معلمين مرتبطين
            if ($zoomAccount->teachers()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف الحساب لأنه مرتبط بمعلمين'
                ], 400);
            }

            // التحقق من عدم وجود اجتماعات مرتبطة
            if ($zoomAccount->meetings()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف الحساب لأنه مرتبط باجتماعات'
                ], 400);
            }

            $zoomAccount->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف حساب Zoom بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الحساب: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(ZoomAccount $account)
    {
        try {
            $account->update(['is_active' => !$account->is_active]);

            return response()->json([
                'success' => true,
                'message' => $account->is_active ? 'تم تفعيل الحساب بنجاح' : 'تم إلغاء تفعيل الحساب بنجاح',
                'is_active' => $account->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تغيير حالة الحساب'
            ], 500);
        }
    }

    public function available()
    {
        $availableAccounts = ZoomAccount::active()
            ->whereDoesntHave('teachers')
            ->get(['id', 'name', 'email']);

        return response()->json($availableAccounts);
    }
}
