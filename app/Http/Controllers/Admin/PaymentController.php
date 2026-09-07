<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCoursePaymentRequest;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CoursePayment;
use App\Models\User;
use App\Services\CoursePaymentSyncService;
use App\Services\PaddleService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index(Request $request, CoursePaymentSyncService $paymentSync)
    {
        $paymentSync->syncAllUnpaidPayments();

        $payments = CoursePayment::with(['student', 'course', 'creator'])
            ->when($request->course_id, fn ($q, $id) => $q->where('course_id', $id))
            ->when($request->student_id, fn ($q, $id) => $q->where('student_id', $id))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->whereHas('student', fn ($sq) => $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"))
                        ->orWhereHas('course', fn ($cq) => $cq->where('title', 'like', "%{$search}%")
                            ->orWhere('title_ar', 'like', "%{$search}%"));
                });
            })
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => CoursePayment::count(),
            'unpaid' => CoursePayment::unpaid()->count(),
            'paid' => CoursePayment::where('status', 'paid')->count(),
        ];

        $courses = $this->coursesForSelect();
        $students = User::role('student')->get(['id', 'name', 'email']);

        return Inertia::render('Admin/Payments/Index', [
            'payments' => $payments,
            'courses' => $courses,
            'students' => $students,
            'stats' => $stats,
            'filters' => $request->only(['course_id', 'student_id', 'status', 'search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Payments/Create', [
            'courses' => $this->coursesForSelect(),
            'currency' => config('services.paddle.display_currency', 'SAR'),
        ]);
    }

    public function enrolledStudents(Course $course)
    {
        $students = CourseEnrollment::where('course_id', $course->id)
            ->whereIn('status', ['enrolled', 'completed'])
            ->with(['student:id,name,email'])
            ->get()
            ->map(fn ($enrollment) => $enrollment->student)
            ->filter()
            ->unique('id')
            ->values();

        return response()->json(['students' => $students]);
    }

    public function store(StoreCoursePaymentRequest $request, PaddleService $paddle)
    {
        $validated = $request->validated();

        $isEnrolled = CourseEnrollment::where('student_id', $validated['student_id'])
            ->where('course_id', $validated['course_id'])
            ->whereIn('status', ['enrolled', 'completed'])
            ->exists();

        if (!$isEnrolled) {
            return back()->withErrors(['student_id' => 'الطالب غير مسجل في هذا الكورس']);
        }

        $existingUnpaid = CoursePayment::where('student_id', $validated['student_id'])
            ->where('course_id', $validated['course_id'])
            ->unpaid()
            ->exists();

        if ($existingUnpaid) {
            return back()->withErrors(['error' => 'يوجد فاتورة غير مدفوعة مسبقاً لهذا الطالب في هذا الكورس']);
        }

        $student = User::findOrFail($validated['student_id']);
        $course = Course::findOrFail($validated['course_id']);
        $sarMinor = PaddleService::toMinorUnits((float) $validated['amount']);
        $usdMinor = $paddle->convertSarToUsdMinor($sarMinor);
        $displayCurrency = $paddle->displayCurrency();
        $courseTitle = $course->title_ar ?: $course->title;
        $description = $validated['description']
            ?? "رسوم كورس: {$courseTitle} - {$student->name}";

        if ($usdMinor < 70) {
            $minimumSar = number_format(0.70 * $paddle->usdSarRate(), 2);

            return back()->withErrors([
                'amount' => "المبلغ بعد التحويل للدولار أقل من الحد الأدنى لدى Paddle. الحد الأدنى تقريباً {$minimumSar} ر.س",
            ]);
        }

        try {
            $transaction = $paddle->createTransaction(
                $usdMinor,
                $description,
                $courseTitle,
                [
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'source' => 'inskola',
                ],
                $student->routeNotificationForMail(),
                $student->name
            );

            CoursePayment::create([
                'student_id' => $validated['student_id'],
                'course_id' => $validated['course_id'],
                'created_by' => $request->user()->id,
                'amount' => $sarMinor,
                'amount_format' => PaddleService::formatAmount($sarMinor, $displayCurrency),
                'currency' => $displayCurrency,
                'description' => $description,
                'paddle_transaction_id' => $transaction['id'],
                'paddle_checkout_url' => $transaction['checkout_url'],
                'status' => 'initiated',
            ]);

            return redirect()->route('admin.payments.index')
                ->with('success', 'تم إنشاء فاتورة الدفع بنجاح');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => 'فشل إنشاء الفاتورة: ' . $e->getMessage()]);
        }
    }

    public function cancel(CoursePayment $payment, CoursePaymentSyncService $paymentSync, PaddleService $paddle)
    {
        if ($response = $this->guardUnpaidAction($payment, $paymentSync, 'إلغاء')) {
            return $response;
        }

        $paddle->cancelTransaction($payment->paddle_transaction_id);

        $payment->update([
            'status' => 'canceled',
        ]);

        return back()->with('success', 'تم إلغاء الفاتورة بنجاح');
    }

    public function destroy(CoursePayment $payment, CoursePaymentSyncService $paymentSync, PaddleService $paddle)
    {
        if ($response = $this->guardUnpaidAction($payment, $paymentSync, 'حذف')) {
            return $response;
        }

        $paddle->cancelTransaction($payment->paddle_transaction_id);
        $payment->delete();

        return back()->with('success', 'تم حذف الفاتورة بنجاح');
    }

    private function guardUnpaidAction(CoursePayment $payment, CoursePaymentSyncService $paymentSync, string $action)
    {
        if ($payment->paddle_transaction_id) {
            $paymentSync->syncPayment($payment);
            $payment->refresh();
        }

        if ($payment->isPaid()) {
            return back()->withErrors(['error' => "لا يمكن {$action} فاتورة مدفوعة"]);
        }

        if (!$payment->isUnpaid() && $action === 'إلغاء') {
            return back()->withErrors(['error' => 'يمكن إلغاء الفواتير غير المدفوعة فقط']);
        }

        return null;
    }

    private function coursesForSelect()
    {
        return Course::orderByDesc('created_at')
            ->get(['id', 'title', 'title_ar', 'status']);
    }
}
