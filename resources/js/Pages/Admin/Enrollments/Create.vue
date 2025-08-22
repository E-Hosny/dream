<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                إضافة تسجيل جديد
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border-0">
                    <div class="p-8 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-8">
                            <!-- Course Selection -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-100">
                                <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.966 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                                    </svg>
                                    اختيار الكورس
                                </h3>
                                <div>
                                    <InputLabel for="course_id" value="الكورس" class="text-blue-800 font-medium" />
                                    <select
                                        id="course_id"
                                        v-model="form.course_id"
                                        class="mt-2 block w-full border-blue-200 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white"
                                        required
                                    >
                                        <option value="">اختر الكورس</option>
                                        <option v-for="course in courses" :key="course.id" :value="course.id">
                                            {{ course.title_ar || course.title }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.course_id" class="mt-2" />
                                </div>
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
                                <p class="text-sm text-gray-500 mt-1">اترك هذا الحقل فارغاً إذا كنت تريد تعيينه تلقائياً</p>
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
                                <p class="text-sm text-gray-500 mt-1">اترك هذا الحقل فارغاً إذا كنت تريد تعيين التاريخ الحالي</p>
                            </div>

                            <!-- Course Info Display -->
                            <div v-if="selectedCourse" class="p-4 bg-blue-50 rounded-lg">
                                <h4 class="font-medium text-blue-900 mb-2">معلومات الكورس المختار:</h4>
                                <div class="text-sm text-blue-800 space-y-1">
                                    <p><strong>العنوان:</strong> {{ selectedCourse.title_ar || selectedCourse.title }}</p>
                                    <p><strong>المستوى:</strong> {{ getLevelText(selectedCourse.level) }}</p>
                                    <p><strong>المدة:</strong> {{ selectedCourse.duration_hours }} ساعة</p>
                                    <p><strong>الطلاب المسجلون:</strong> {{ selectedCourse.enrolled_students_count || 0 }}</p>
                                    <p v-if="selectedCourse.max_students"><strong>الحد الأقصى:</strong> {{ selectedCourse.max_students }}</p>
                                    <p v-if="selectedCourse.max_students"><strong>المساحات المتاحة:</strong> {{ selectedCourse.available_spots || 'غير محدود' }}</p>
                                </div>
                            </div>

                            <!-- Student Info Display -->
                            <div v-if="selectedStudent" class="p-4 bg-green-50 rounded-lg">
                                <h4 class="font-medium text-green-900 mb-2">معلومات الطالب المختار:</h4>
                                <div class="text-sm text-green-800 space-y-1">
                                    <p><strong>الاسم:</strong> {{ selectedStudent.name }}</p>
                                    <p><strong>البريد الإلكتروني:</strong> {{ selectedStudent.email }}</p>
                                    <p><strong>الكورسات المسجلة:</strong> {{ selectedStudent.enrolled_courses_count || 0 }}</p>
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
                                    إضافة التسجيل
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
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    courses: Array,
    students: Array
})

const form = useForm({
    course_id: '',
    student_id: '',
    status: 'enrolled',
    progress: '',
    enrolled_at: ''
})

const selectedCourse = computed(() => {
    if (!form.course_id) return null
    return props.courses.find(course => course.id == form.course_id)
})

const selectedStudent = computed(() => {
    if (!form.student_id) return null
    return props.students.find(student => student.id == form.student_id)
})

const submit = () => {
    form.post(route('admin.enrollments.store'))
}

const getLevelText = (level) => {
    switch (level) {
        case 'beginner':
            return 'مبتدئ'
        case 'intermediate':
            return 'متوسط'
        case 'advanced':
            return 'متقدم'
        default:
            return level
    }
}
</script>
