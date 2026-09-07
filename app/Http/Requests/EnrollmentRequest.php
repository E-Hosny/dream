<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    protected function prepareForValidation(): void
    {
        $payload = [];

        foreach (['enrolled_at', 'completed_at', 'progress', 'final_grade'] as $field) {
            if ($this->exists($field) && $this->input($field) === '') {
                $payload[$field] = null;
            }
        }

        if ($payload !== []) {
            $this->merge($payload);
        }
    }

    public function rules(): array
    {
        $rules = [
            'course_id' => ['required', 'exists:courses,id'],
            'student_id' => ['required', 'exists:users,id'],
            'status' => ['required', 'in:enrolled,completed,dropped'],
            'progress' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'enrolled_at' => ['nullable', 'date'],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $enrollmentId = $this->route('enrollment')?->id ?? $this->route('enrollment');

            $rules['completed_at'] = ['nullable', 'date'];
            $rules['final_grade'] = ['nullable', 'numeric', 'min:0', 'max:100'];
            $rules['student_id'][] = Rule::unique('course_enrollments', 'student_id')
                ->where(fn ($query) => $query->where('course_id', $this->input('course_id')))
                ->ignore($enrollmentId);
        }

        if ($this->isMethod('POST')) {
            $rules['student_id'][] = Rule::unique('course_enrollments', 'student_id')
                ->where(fn ($query) => $query->where('course_id', $this->input('course_id')));
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'course_id.required' => 'يجب اختيار الكورس',
            'course_id.exists' => 'الكورس المختار غير موجود',
            'student_id.required' => 'يجب اختيار الطالب',
            'student_id.exists' => 'الطالب المختار غير موجود',
            'student_id.unique' => 'هذا الطالب مسجل بالفعل في هذا الكورس',
            'status.required' => 'يجب تحديد حالة التسجيل',
            'status.in' => 'حالة التسجيل غير صحيحة',
            'progress.numeric' => 'التقدم يجب أن يكون رقماً',
            'progress.min' => 'التقدم لا يمكن أن يكون أقل من 0%',
            'progress.max' => 'التقدم لا يمكن أن يكون أكثر من 100%',
            'enrolled_at.date' => 'تاريخ التسجيل غير صحيح',
            'completed_at.date' => 'تاريخ الإكمال غير صحيح',
            'final_grade.numeric' => 'الدرجة النهائية يجب أن تكون رقماً',
            'final_grade.min' => 'الدرجة النهائية لا يمكن أن تكون أقل من 0%',
            'final_grade.max' => 'الدرجة النهائية لا يمكن أن تكون أكثر من 100%',
        ];
    }

    public function attributes(): array
    {
        return [
            'course_id' => 'الكورس',
            'student_id' => 'الطالب',
            'status' => 'الحالة',
            'progress' => 'التقدم',
            'enrolled_at' => 'تاريخ التسجيل',
            'completed_at' => 'تاريخ الإكمال',
            'final_grade' => 'الدرجة النهائية',
        ];
    }
}
