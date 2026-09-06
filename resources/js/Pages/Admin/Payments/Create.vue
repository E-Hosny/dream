<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                إنشاء فاتورة دفع
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border-0">
                    <div class="p-8 text-gray-900">
                        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                            {{ $page.props.flash.success }}
                        </div>
                        <div v-if="form.errors.error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
                            {{ form.errors.error }}
                        </div>

                        <form @submit.prevent="submit" class="space-y-8">
                            <div class="bg-gradient-to-r from-amber-50 to-orange-50 p-6 rounded-xl border border-amber-100">
                                <h3 class="text-lg font-semibold text-amber-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    تفاصيل الفاتورة
                                </h3>

                                <div class="space-y-6">
                                    <div>
                                        <InputLabel for="course_id" value="الكورس" class="text-amber-800 font-medium" />
                                        <select
                                            id="course_id"
                                            v-model="form.course_id"
                                            class="mt-2 block w-full rounded-lg border border-amber-200 bg-white px-3 py-2.5 text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                            required
                                            @change="onCourseChange"
                                        >
                                            <option value="" class="text-gray-900">اختر الكورس</option>
                                            <option
                                                v-for="course in courses"
                                                :key="course.id"
                                                :value="String(course.id)"
                                                class="text-gray-900"
                                            >
                                                {{ course.title_ar || course.title }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.course_id" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="student_id" value="الطالب" class="text-amber-800 font-medium" />
                                        <select
                                            id="student_id"
                                            v-model="form.student_id"
                                            class="mt-2 block w-full rounded-lg border border-amber-200 bg-white px-3 py-2.5 text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 disabled:cursor-not-allowed disabled:bg-gray-100"
                                            :disabled="!form.course_id || loadingStudents"
                                            required
                                        >
                                            <option value="" class="text-gray-900">
                                                {{ studentPlaceholder }}
                                            </option>
                                            <option
                                                v-for="student in enrolledStudents"
                                                :key="student.id"
                                                :value="String(student.id)"
                                                class="text-gray-900"
                                            >
                                                {{ student.name }} ({{ student.email }})
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.student_id" class="mt-2" />
                                        <p v-if="form.course_id && !loadingStudents && !enrolledStudents.length" class="text-sm text-amber-700 mt-1">
                                            لا يوجد طلاب مسجلون في هذا الكورس. أضف تسجيلاً من قسم التسجيلات أولاً.
                                        </p>
                                    </div>

                                    <div>
                                        <InputLabel for="amount" :value="`المبلغ (${currency})`" class="text-amber-800 font-medium" />
                                        <TextInput
                                            id="amount"
                                            type="number"
                                            v-model="form.amount"
                                            class="mt-2 block w-full"
                                            min="1"
                                            step="0.01"
                                            placeholder="مثال: 10.00"
                                            required
                                        />
                                        <InputError :message="form.errors.amount" class="mt-2" />
                                        <p class="text-sm text-amber-700 mt-1">
                                            يُدخل المبلغ بالريال السعودي ويظهر كذلك للطالب. عند الدفع يُحوَّل إلى دولار بسعر 1 دولار = 3.75 ر.س.
                                        </p>
                                    </div>

                                    <div>
                                        <InputLabel for="description" value="الوصف (اختياري)" class="text-amber-800 font-medium" />
                                        <TextInput
                                            id="description"
                                            type="text"
                                            v-model="form.description"
                                            class="mt-2 block w-full"
                                            placeholder="سيتم إنشاء وصف تلقائي إذا تُرك فارغاً"
                                        />
                                        <InputError :message="form.errors.description" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end space-x-3 rtl:space-x-reverse">
                                <Link
                                    :href="route('admin.payments.index')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 transition ease-in-out duration-150"
                                >
                                    إلغاء
                                </Link>
                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing || !canSubmit }"
                                    :disabled="form.processing || !canSubmit"
                                >
                                    إنشاء الفاتورة وإرسال رابط الدفع
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
import { computed, ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

defineProps({
    courses: Array,
    currency: {
        type: String,
        default: 'SAR',
    },
})

const form = useForm({
    course_id: '',
    student_id: '',
    amount: '',
    description: '',
})

const enrolledStudents = ref([])
const loadingStudents = ref(false)

const studentPlaceholder = computed(() => {
    if (!form.course_id) {
        return 'اختر الكورس أولاً'
    }

    if (loadingStudents.value) {
        return 'جاري تحميل الطلاب...'
    }

    return enrolledStudents.value.length ? 'اختر الطالب' : 'لا يوجد طلاب مسجلون'
})

const loadEnrolledStudents = async (courseId) => {
    enrolledStudents.value = []

    if (!courseId) {
        return
    }

    loadingStudents.value = true

    try {
        const response = await fetch(route('admin.api.courses.enrolled-students', courseId), {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })

        if (!response.ok) {
            throw new Error('Failed to load students')
        }

        const data = await response.json()
        enrolledStudents.value = data.students ?? []
    } catch (error) {
        console.error('Error loading enrolled students:', error)
        enrolledStudents.value = []
    } finally {
        loadingStudents.value = false
    }
}

const onCourseChange = async () => {
    form.student_id = ''
    await loadEnrolledStudents(form.course_id)
}

const canSubmit = computed(() => {
    return form.course_id && form.student_id && enrolledStudents.value.length > 0
})

const submit = () => {
    form.post(route('admin.payments.store'))
}
</script>
