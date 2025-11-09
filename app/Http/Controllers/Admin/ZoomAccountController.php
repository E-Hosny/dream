<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ZoomAccount;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ZoomAccountController extends Controller
{
    /**
     * عرض قائمة حسابات Zoom
     */
    public function index(Request $request)
    {
        $query = ZoomAccount::with(['creator', 'updater'])
            ->withCount('teachers', 'meetings');

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // فلترة حسب البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('account_id', 'like', "%{$search}%");
            });
        }

        $accounts = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Admin/ZoomAccounts/Index', [
            'accounts' => $accounts,
            'filters' => $request->only(['status', 'search'])
        ]);
    }

    /**
     * عرض صفحة إنشاء حساب Zoom جديد
     */
    public function create()
    {
        return Inertia::render('Admin/ZoomAccounts/Create');
    }

    /**
     * حفظ حساب Zoom جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:zoom_accounts,email',
            'account_id' => 'required|string|max:255',
            'client_id' => 'required|string|max:255',
            'client_secret' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_meetings_per_day' => 'nullable|integer|min:1|max:1000',
            'max_participants' => 'nullable|integer|min:1|max:1000',
            'features' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // إنشاء حساب Zoom
            $zoomAccount = ZoomAccount::create([
                'name' => $request->name,
                'email' => $request->email,
                'account_id' => $request->account_id,
                'client_id' => $request->client_id,
                'client_secret' => $request->client_secret,
                'description' => $request->description,
                'max_meetings_per_day' => $request->max_meetings_per_day ?? 100,
                'max_participants' => $request->max_participants ?? 300,
                'features' => $request->features ?? [],
                'is_active' => $request->is_active ?? true,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ]);

            // اختبار الاتصال بحساب Zoom
            try {
                $zoomService = new ZoomService($zoomAccount);
                $token = $zoomService->getAccessToken();
                
                Log::info('Zoom account verified successfully', [
                    'account_id' => $zoomAccount->id,
                    'name' => $zoomAccount->name
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to verify Zoom account credentials', [
                    'account_id' => $zoomAccount->id,
                    'error' => $e->getMessage()
                ]);
                
                // نواصل حفظ الحساب حتى لو فشل الاختبار
                // سيظهر تحذير للمستخدم
            }

            DB::commit();

            return redirect()->route('admin.zoom-accounts.index')
                ->with('success', 'تم إضافة حساب Zoom بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Zoom Account Creation Error: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'حدث خطأ أثناء إضافة حساب Zoom: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * عرض حساب Zoom معين
     */
    public function show(ZoomAccount $zoomAccount)
    {
        $zoomAccount->load([
            'teachers' => function($query) {
                $query->select('id', 'name', 'email', 'zoom_account_id');
            },
            'meetings' => function($query) {
                $query->latest()->take(10);
            },
            'creator',
            'updater'
        ]);

        return Inertia::render('Admin/ZoomAccounts/Show', [
            'account' => $zoomAccount
        ]);
    }

    /**
     * عرض صفحة تعديل حساب Zoom
     */
    public function edit(ZoomAccount $zoomAccount)
    {
        return Inertia::render('Admin/ZoomAccounts/Edit', [
            'account' => $zoomAccount
        ]);
    }

    /**
     * تحديث حساب Zoom
     */
    public function update(Request $request, ZoomAccount $zoomAccount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:zoom_accounts,email,' . $zoomAccount->id,
            'account_id' => 'required|string|max:255',
            'client_id' => 'required|string|max:255',
            'client_secret' => 'nullable|string|max:255', // يمكن تركه فارغاً للإبقاء على القديم
            'description' => 'nullable|string',
            'max_meetings_per_day' => 'nullable|integer|min:1|max:1000',
            'max_participants' => 'nullable|integer|min:1|max:1000',
            'features' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // تحديث البيانات
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'account_id' => $request->account_id,
                'client_id' => $request->client_id,
                'description' => $request->description,
                'max_meetings_per_day' => $request->max_meetings_per_day ?? 100,
                'max_participants' => $request->max_participants ?? 300,
                'features' => $request->features ?? [],
                'is_active' => $request->is_active ?? true,
                'updated_by' => Auth::id()
            ];

            // تحديث client_secret فقط إذا تم إدخاله
            if ($request->filled('client_secret')) {
                $data['client_secret'] = $request->client_secret;
            }

            $zoomAccount->update($data);

            // اختبار الاتصال بحساب Zoom إذا تم تحديث بيانات الاعتماد
            if ($request->filled('client_secret') || 
                $request->account_id !== $zoomAccount->getOriginal('account_id') ||
                $request->client_id !== $zoomAccount->getOriginal('client_id')) {
                
                try {
                    $zoomService = new ZoomService($zoomAccount);
                    $token = $zoomService->getAccessToken();
                    
                    Log::info('Zoom account credentials verified successfully', [
                        'account_id' => $zoomAccount->id,
                        'name' => $zoomAccount->name
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to verify updated Zoom account credentials', [
                        'account_id' => $zoomAccount->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.zoom-accounts.index')
                ->with('success', 'تم تحديث حساب Zoom بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Zoom Account Update Error: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'حدث خطأ أثناء تحديث حساب Zoom: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * حذف حساب Zoom
     */
    public function destroy(ZoomAccount $zoomAccount)
    {
        try {
            DB::beginTransaction();

            // التحقق من عدم وجود اجتماعات نشطة
            $activeMeetingsCount = $zoomAccount->meetings()
                ->whereIn('status', ['scheduled', 'started'])
                ->count();

            if ($activeMeetingsCount > 0) {
                return back()->withErrors([
                    'error' => 'لا يمكن حذف الحساب لأنه يحتوي على ' . $activeMeetingsCount . ' اجتماع نشط. يرجى إنهاء أو إلغاء الاجتماعات أولاً.'
                ]);
            }

            // فك ارتباط المعلمين من هذا الحساب
            $zoomAccount->teachers()->update(['zoom_account_id' => null]);

            // حذف الحساب
            $zoomAccount->delete();

            DB::commit();

            return redirect()->route('admin.zoom-accounts.index')
                ->with('success', 'تم حذف حساب Zoom بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Zoom Account Deletion Error: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'حدث خطأ أثناء حذف حساب Zoom: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * تفعيل/تعطيل حساب Zoom
     */
    public function toggleActive(ZoomAccount $zoomAccount)
    {
        try {
            $zoomAccount->update([
                'is_active' => !$zoomAccount->is_active,
                'updated_by' => Auth::id()
            ]);

            $status = $zoomAccount->is_active ? 'تفعيل' : 'تعطيل';

            return back()->with('success', "تم {$status} حساب Zoom بنجاح");

        } catch (\Exception $e) {
            Log::error('Zoom Account Toggle Active Error: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'حدث خطأ أثناء تعديل حالة الحساب'
            ]);
        }
    }

    /**
     * اختبار الاتصال بحساب Zoom
     */
    public function testConnection(ZoomAccount $zoomAccount)
    {
        try {
            $zoomService = new ZoomService($zoomAccount);
            $token = $zoomService->getAccessToken();

            return response()->json([
                'success' => true,
                'message' => 'تم الاتصال بحساب Zoom بنجاح'
            ]);

        } catch (\Exception $e) {
            Log::error('Zoom Account Test Connection Error: ' . $e->getMessage(), [
                'account_id' => $zoomAccount->id,
                'account_name' => $zoomAccount->name
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل الاتصال بحساب Zoom: ' . $e->getMessage()
            ], 400);
        }
    }
}
