<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCoursePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:users,id'],
            'course_id' => ['required', 'exists:courses,id'],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'يجب اختيار الطالب',
            'student_id.exists' => 'الطالب المحدد غير موجود',
            'course_id.required' => 'يجب اختيار الكورس',
            'course_id.exists' => 'الكورس المحدد غير موجود',
            'amount.required' => 'يجب إدخال المبلغ',
            'amount.numeric' => 'المبلغ يجب أن يكون رقماً',
            'amount.min' => 'الحد الأدنى للمبلغ هو 1',
            'amount.max' => 'المبلغ كبير جداً',
        ];
    }
}
