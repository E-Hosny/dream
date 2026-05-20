<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCoursePaymentRequest;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CoursePayment;
use App\Models\User;
use App\Services\CoursePaymentSyncService;
use App\Services\MoyasarService;
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

    public function store(StoreCoursePaymentRequest $request, MoyasarService $moyasar)
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
        $amountHalalas = MoyasarService::sarToHalalas((float) $validated['amount']);
        $description = $validated['description']
            ?? "رسوم كورس: {$course->title_ar} - {$student->name}";

        try {
            $invoice = $moyasar->createInvoice(
                $amountHalalas,
                $description,
                route('moyasar.webhook'),
                route('student.payments.success')
            );

            CoursePayment::create([
                'student_id' => $validated['student_id'],
                'course_id' => $validated['course_id'],
                'created_by' => $request->user()->id,
                'amount' => $invoice['amount'],
                'amount_format' => $invoice['amount_format'] ?? null,
                'currency' => $invoice['currency'] ?? 'SAR',
                'description' => $description,
                'moyasar_invoice_id' => $invoice['id'],
                'moyasar_invoice_url' => $invoice['url'],
                'status' => $invoice['status'] ?? 'initiated',
            ]);

            return redirect()->route('admin.payments.index')
                ->with('success', 'تم إنشاء فاتورة الدفع بنجاح');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => 'فشل إنشاء الفاتورة: ' . $e->getMessage()]);
        }
    }

    private function coursesForSelect()
    {
        return Course::orderByDesc('created_at')
            ->get(['id', 'title', 'title_ar', 'status']);
    }
}
