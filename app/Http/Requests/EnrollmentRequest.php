<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EnrollmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'course_id' => ['required', 'exists:courses,id'],
            'student_id' => ['required', 'exists:users,id'],
            'status' => ['required', 'in:enrolled,completed,dropped'],
            'progress' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'enrolled_at' => ['nullable', 'date'],
        ];

        // إضافة قواعد خاصة بالتحديث
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['completed_at'] = ['nullable', 'date'];
            $rules['final_grade'] = ['nullable', 'numeric', 'min:0', 'max:100'];
        }

        // إضافة قواعد خاصة بالإنشاء
        if ($this->isMethod('POST')) {
            $rules['progress'] = ['nullable', 'numeric', 'min:0', 'max:100'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'course_id.required' => 'يجب اختيار الكورس',
            'course_id.exists' => 'الكورس المختار غير موجود',
            'student_id.required' => 'يجب اختيار الطالب',
            'student_id.exists' => 'الطالب المختار غير موجود',
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

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array
     */
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
