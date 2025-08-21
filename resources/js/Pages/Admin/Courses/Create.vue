<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');

defineProps({
    teachers: Array
});

const form = useForm({
    title: '',
    title_ar: '',
    description: '',
    description_ar: '',
    price: 0,
    duration_hours: 1,
    level: 'beginner',
    status: 'draft',
    instructor_id: '',
    max_students: '',
    start_date: '',
    end_date: '',
    requirements: [],
    learning_outcomes: []
});

const newRequirement = ref('');
const newOutcome = ref('');

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            create_course: 'Create New Course',
            back_to_courses: 'Back to Courses',
            basic_info: 'Basic Information',
            additional_info: 'Additional Information',
            title: 'Course Title (English)',
            title_ar: 'Course Title (Arabic)',
            description: 'Description (English)',
            description_ar: 'Description (Arabic)',
            instructor: 'Instructor',
            price: 'Price',
            duration: 'Duration (Hours)',
            level: 'Level',
            status: 'Status',
            max_students: 'Max Students',
            start_date: 'Start Date',
            end_date: 'End Date',
            requirements: 'Requirements',
            learning_outcomes: 'Learning Outcomes',
            create: 'Create Course',
            cancel: 'Cancel',
            select_instructor: 'Select an instructor',
            beginner: 'Beginner',
            intermediate: 'Intermediate',
            advanced: 'Advanced',
            draft: 'Draft',
            published: 'Published',
            archived: 'Archived',
            add_requirement: 'Add Requirement',
            add_outcome: 'Add Learning Outcome',
            optional: 'Optional'
        },
        ar: {
            create_course: 'إنشاء كورس جديد',
            back_to_courses: 'العودة للكورسات',
            basic_info: 'المعلومات الأساسية',
            additional_info: 'معلومات إضافية',
            title: 'عنوان الكورس (إنجليزي)',
            title_ar: 'عنوان الكورس (عربي)',
            description: 'الوصف (إنجليزي)',
            description_ar: 'الوصف (عربي)',
            instructor: 'المدرس',
            price: 'السعر',
            duration: 'المدة (ساعات)',
            level: 'المستوى',
            status: 'الحالة',
            max_students: 'الحد الأقصى للطلاب',
            start_date: 'تاريخ البداية',
            end_date: 'تاريخ النهاية',
            requirements: 'المتطلبات',
            learning_outcomes: 'مخرجات التعلم',
            create: 'إنشاء كورس',
            cancel: 'إلغاء',
            select_instructor: 'اختر مدرس',
            beginner: 'مبتدئ',
            intermediate: 'متوسط',
            advanced: 'متقدم',
            draft: 'مسودة',
            published: 'منشور',
            archived: 'مؤرشف',
            add_requirement: 'إضافة متطلب',
            add_outcome: 'إضافة مخرج تعلم',
            optional: 'اختياري'
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

const addRequirement = () => {
    if (newRequirement.value.trim()) {
        form.requirements.push(newRequirement.value.trim());
        newRequirement.value = '';
    }
};

const removeRequirement = (index) => {
    form.requirements.splice(index, 1);
};

const addOutcome = () => {
    if (newOutcome.value.trim()) {
        form.learning_outcomes.push(newOutcome.value.trim());
        newOutcome.value = '';
    }
};

const removeOutcome = (index) => {
    form.learning_outcomes.splice(index, 1);
};

const submit = () => {
    form.post(route('admin.courses.store'));
};
</script>

<template>
    <Head :title="t('create_course')" />

    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ t('create_course') }}</h1>
                <p class="text-gray-600 mt-2">{{ currentLocale === 'ar' ? 'إضافة كورس جديد للمنصة' : 'Add a new course to the platform' }}</p>
            </div>
            <Link :href="route('admin.courses.index')" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-md hover:shadow-lg">
                <svg class="w-5 h-5 inline mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ t('back_to_courses') }}
            </Link>
        </div>

        <!-- Create Form -->
        <div class="bg-white rounded-2xl shadow-xl border-0 overflow-hidden backdrop-blur-sm bg-white/95">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-6">
                <h2 class="text-xl font-semibold text-white">{{ currentLocale === 'ar' ? 'بيانات الكورس الجديد' : 'New Course Information' }}</h2>
                <p class="text-emerald-100 mt-1">{{ currentLocale === 'ar' ? 'أدخل جميع بيانات الكورس التعليمي' : 'Enter all educational course details' }}</p>
            </div>
            <form @submit.prevent="submit" class="p-8 space-y-8">
                <!-- Basic Information -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ t('basic_info') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="group">
                            <label for="title" class="block text-sm font-semibold text-gray-800 mb-3 transition-colors group-focus-within:text-emerald-600">
                                {{ t('title') }}
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 flex items-center pl-4 rtl:pl-0 rtl:pr-4 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                                    </svg>
                                </div>
                                <input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md focus:shadow-lg"
                                    :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.title }"
                                    required
                                >
                            </div>
                            <div v-if="form.errors.title" class="mt-2 flex items-center text-sm text-red-600">
                                <svg class="w-4 h-4 mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ form.errors.title }}
                            </div>
                        </div>

                        <!-- Title Arabic -->
                        <div>
                            <label for="title_ar" class="block text-sm font-medium text-gray-700 mb-2">{{ t('title_ar') }} <span class="text-gray-500">({{ t('optional') }})</span></label>
                            <input
                                id="title_ar"
                                v-model="form.title_ar"
                                type="text"
                                class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                :class="{ 'border-red-500': form.errors.title_ar }"
                            >
                            <div v-if="form.errors.title_ar" class="mt-1 text-sm text-red-600">
                                {{ form.errors.title_ar }}
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">{{ t('description') }}</label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                :class="{ 'border-red-500': form.errors.description }"
                                required
                            ></textarea>
                            <div v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                                {{ form.errors.description }}
                            </div>
                        </div>

                        <!-- Description Arabic -->
                        <div class="md:col-span-2">
                            <label for="description_ar" class="block text-sm font-medium text-gray-700 mb-2">{{ t('description_ar') }} <span class="text-gray-500">({{ t('optional') }})</span></label>
                            <textarea
                                id="description_ar"
                                v-model="form.description_ar"
                                rows="4"
                                class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                :class="{ 'border-red-500': form.errors.description_ar }"
                            ></textarea>
                            <div v-if="form.errors.description_ar" class="mt-1 text-sm text-red-600">
                                {{ form.errors.description_ar }}
                            </div>
                        </div>

                        <!-- Instructor -->
                        <div>
                            <label for="instructor_id" class="block text-sm font-medium text-gray-700 mb-2">{{ t('instructor') }}</label>
                            <select
                                id="instructor_id"
                                v-model="form.instructor_id"
                                class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                :class="{ 'border-red-500': form.errors.instructor_id }"
                                required
                            >
                                <option value="">{{ t('select_instructor') }}</option>
                                <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                                    {{ teacher.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.instructor_id" class="mt-1 text-sm text-red-600">
                                {{ form.errors.instructor_id }}
                            </div>
                        </div>

                        <!-- Price -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">{{ t('price') }}</label>
                            <input
                                id="price"
                                v-model="form.price"
                                type="number"
                                min="0"
                                step="0.01"
                                class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                :class="{ 'border-red-500': form.errors.price }"
                                required
                            >
                            <div v-if="form.errors.price" class="mt-1 text-sm text-red-600">
                                {{ form.errors.price }}
                            </div>
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-2">{{ t('duration') }}</label>
                            <input
                                id="duration_hours"
                                v-model="form.duration_hours"
                                type="number"
                                min="1"
                                class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                :class="{ 'border-red-500': form.errors.duration_hours }"
                                required
                            >
                            <div v-if="form.errors.duration_hours" class="mt-1 text-sm text-red-600">
                                {{ form.errors.duration_hours }}
                            </div>
                        </div>

                        <!-- Level -->
                        <div>
                            <label for="level" class="block text-sm font-medium text-gray-700 mb-2">{{ t('level') }}</label>
                            <select
                                id="level"
                                v-model="form.level"
                                class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                :class="{ 'border-red-500': form.errors.level }"
                                required
                            >
                                <option value="beginner">{{ t('beginner') }}</option>
                                <option value="intermediate">{{ t('intermediate') }}</option>
                                <option value="advanced">{{ t('advanced') }}</option>
                            </select>
                            <div v-if="form.errors.level" class="mt-1 text-sm text-red-600">
                                {{ form.errors.level }}
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">{{ t('status') }}</label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                :class="{ 'border-red-500': form.errors.status }"
                                required
                            >
                                <option value="draft">{{ t('draft') }}</option>
                                <option value="published">{{ t('published') }}</option>
                                <option value="archived">{{ t('archived') }}</option>
                            </select>
                            <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                                {{ form.errors.status }}
                            </div>
                        </div>

                        <!-- Max Students -->
                        <div>
                            <label for="max_students" class="block text-sm font-medium text-gray-700 mb-2">{{ t('max_students') }} <span class="text-gray-500">({{ t('optional') }})</span></label>
                            <input
                                id="max_students"
                                v-model="form.max_students"
                                type="number"
                                min="1"
                                class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                :class="{ 'border-red-500': form.errors.max_students }"
                            >
                            <div v-if="form.errors.max_students" class="mt-1 text-sm text-red-600">
                                {{ form.errors.max_students }}
                            </div>
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">{{ t('start_date') }} <span class="text-gray-500">({{ t('optional') }})</span></label>
                            <input
                                id="start_date"
                                v-model="form.start_date"
                                type="date"
                                class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                :class="{ 'border-red-500': form.errors.start_date }"
                            >
                            <div v-if="form.errors.start_date" class="mt-1 text-sm text-red-600">
                                {{ form.errors.start_date }}
                            </div>
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">{{ t('end_date') }} <span class="text-gray-500">({{ t('optional') }})</span></label>
                            <input
                                id="end_date"
                                v-model="form.end_date"
                                type="date"
                                class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                :class="{ 'border-red-500': form.errors.end_date }"
                            >
                            <div v-if="form.errors.end_date" class="mt-1 text-sm text-red-600">
                                {{ form.errors.end_date }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requirements -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('requirements') }}</h3>
                    <div class="space-y-3">
                        <div v-for="(requirement, index) in form.requirements" :key="index" class="flex items-center space-x-2 rtl:space-x-reverse">
                            <span class="flex-1 px-3 py-2 bg-gray-50 rounded-lg text-sm">{{ requirement }}</span>
                            <button @click="removeRequirement(index)" type="button" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex space-x-2 rtl:space-x-reverse">
                            <input
                                v-model="newRequirement"
                                type="text"
                                placeholder="Enter a requirement..."
                                class="flex-1 rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                @keyup.enter="addRequirement"
                            >
                            <button @click="addRequirement" type="button" class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors">
                                {{ t('add_requirement') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Learning Outcomes -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('learning_outcomes') }}</h3>
                    <div class="space-y-3">
                        <div v-for="(outcome, index) in form.learning_outcomes" :key="index" class="flex items-center space-x-2 rtl:space-x-reverse">
                            <span class="flex-1 px-3 py-2 bg-gray-50 rounded-lg text-sm">{{ outcome }}</span>
                            <button @click="removeOutcome(index)" type="button" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex space-x-2 rtl:space-x-reverse">
                            <input
                                v-model="newOutcome"
                                type="text"
                                placeholder="Enter a learning outcome..."
                                class="flex-1 rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"
                                @keyup.enter="addOutcome"
                            >
                            <button @click="addOutcome" type="button" class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors">
                                {{ t('add_outcome') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-gray-50 -mx-8 -mb-8 px-8 py-6 mt-8">
                    <div class="flex items-center justify-between">
                        <Link :href="route('admin.courses.index')" class="group flex items-center px-6 py-3 text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md">
                            <svg class="w-5 h-5 mr-2 rtl:mr-0 rtl:ml-2 group-hover:-translate-x-1 rtl:group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            {{ t('cancel') }}
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="group flex items-center px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 focus:ring-4 focus:ring-emerald-300 transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-lg transform hover:scale-105 active:scale-95"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5 mr-2 rtl:mr-0 rtl:ml-2 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ form.processing ? (currentLocale === 'ar' ? 'جاري الإنشاء...' : 'Creating...') : t('create') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
