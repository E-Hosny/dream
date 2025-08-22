<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                تعديل التسجيل
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Course Selection -->
                            <div>
                                <InputLabel for="course_id" value="الكورس" />
                                <select
                                    id="course_id"
                                    v-model="form.course_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                    <option value="">اختر الكورس</option>
                                    <option v-for="course in courses" :key="course.id" :value="course.id">
                                        {{ course.title_ar || course.title }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.course_id" class="mt-2" />
                            </div>

                            <!-- Student Selection -->
                            <div>
                                <InputLabel for="student_id" value="الطالب" />
                                <select
                                    id="student_id"
                                    v-model="form.student_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                    <option value="">اختر الطالب</option>
                                    <option v-for="student in students" :key="student.id" :value="student.id">
                                        {{ student.name }} ({{ student.email }})
                                    </option>
                                </select>
                                <InputError :message="form.errors.student_id" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <InputLabel for="status" value="الحالة" />
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                    <option value="enrolled">مسجل</option>
                                    <option value="completed">مكتمل</option>
                                    <option value="dropped">منسحب</option>
                                </select>
                                <InputError :message="form.errors.status" class="mt-2" />
                            </div>

                            <!-- Progress -->
                            <div>
                                <InputLabel for="progress" value="التقدم (%)" />
                                <TextInput
                                    id="progress"
                                    type="number"
                                    v-model="form.progress"
                                    class="mt-1 block w-full"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                />
                                <InputError :message="form.errors.progress" class="mt-2" />
                            </div>

                            <!-- Enrollment Date -->
                            <div>
                                <InputLabel for="enrolled_at" value="تاريخ التسجيل" />
                                <TextInput
                                    id="enrolled_at"
                                    type="date"
                                    v-model="form.enrolled_at"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.enrolled_at" class="mt-2" />
                            </div>

                            <!-- Completion Date (only show if status is completed) -->
                            <div v-if="form.status === 'completed'">
                                <InputLabel for="completed_at" value="تاريخ الإكمال" />
                                <TextInput
                                    id="completed_at"
                                    type="date"
                                    v-model="form.completed_at"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.completed_at" class="mt-2" />
                            </div>

                            <!-- Final Grade (only show if status is completed) -->
                            <div v-if="form.status === 'completed'">
                                <InputLabel for="final_grade" value="الدرجة النهائية (%)" />
                                <TextInput
                                    id="final_grade"
                                    type="number"
                                    v-model="form.final_grade"
                                    class="mt-1 block w-full"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                />
                                <InputError :message="form.errors.final_grade" class="mt-2" />
                            </div>

                            <!-- Current Enrollment Info -->
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <h4 class="font-medium text-gray-900 mb-2">معلومات التسجيل الحالي:</h4>
                                <div class="text-sm text-gray-700 space-y-1">
                                    <p><strong>تاريخ الإنشاء:</strong> {{ formatDate(enrollment.created_at) }}</p>
                                    <p><strong>آخر تحديث:</strong> {{ formatDate(enrollment.updated_at) }}</p>
                                    <p v-if="enrollment.progress"><strong>التقدم الحالي:</strong> {{ enrollment.progress }}%</p>
                                    <p v-if="enrollment.final_grade"><strong>الدرجة النهائية:</strong> {{ enrollment.final_grade }}%</p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end space-x-3 rtl:space-x-reverse">
                                <Link
                                    :href="route('admin.enrollments.index')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    إلغاء
                                </Link>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    تحديث التسجيل
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    enrollment: Object,
    courses: Array,
    students: Array
})

const form = useForm({
    course_id: props.enrollment.course_id,
    student_id: props.enrollment.student_id,
    status: props.enrollment.status,
    progress: props.enrollment.progress || '',
    enrolled_at: props.enrollment.enrolled_at ? props.enrollment.enrolled_at.split('T')[0] : '',
    completed_at: props.enrollment.completed_at ? props.enrollment.completed_at.split('T')[0] : '',
    final_grade: props.enrollment.final_grade || ''
})

const submit = () => {
    form.put(route('admin.enrollments.update', props.enrollment.id))
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('ar-SA')
}
</script>
