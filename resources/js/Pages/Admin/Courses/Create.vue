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
    learning_outcomes: [],
    schedules: []
});

const newRequirement = ref('');
const newOutcome = ref('');
const newSchedule = ref({
    day_of_week: 'saturday',
    start_time: '08:00',
    end_time: '09:00'
});

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
            optional: 'Optional',
            schedules: 'Course Schedules',
            add_schedule: 'Add Schedule',
            day_of_week: 'Day of Week',
            start_time: 'Start Time',
            end_time: 'End Time',
            saturday: 'Saturday',
            sunday: 'Sunday',
            monday: 'Monday',
            tuesday: 'Tuesday',
            wednesday: 'Wednesday',
            thursday: 'Thursday',
            friday: 'Friday'
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
            optional: 'اختياري',
            schedules: 'جدول الكورسات',
            add_schedule: 'إضافة جدول',
            day_of_week: 'اليوم',
            start_time: 'الوقت البدء',
            end_time: 'الوقت النهاية',
            saturday: 'السبت',
            sunday: 'الأحد',
            monday: 'الإثنين',
            tuesday: 'الثلاثاء',
            wednesday: 'الأربعاء',
            thursday: 'الخميس',
            friday: 'الجمعة'
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

const addSchedule = () => {
    if (newSchedule.value.day_of_week && newSchedule.value.start_time && newSchedule.value.end_time) {
        // التحقق من عدم تكرار نفس اليوم
        const exists = form.schedules.some(schedule => schedule.day_of_week === newSchedule.value.day_of_week);
        if (!exists) {
            form.schedules.push({
                day_of_week: newSchedule.value.day_of_week,
                start_time: newSchedule.value.start_time,
                end_time: newSchedule.value.end_time
            });
            // إعادة تعيين النموذج
            newSchedule.value = {
                day_of_week: 'saturday',
                start_time: '08:00',
                end_time: '09:00'
            };
        }
    }
};

const removeSchedule = (index) => {
    form.schedules.splice(index, 1);
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
        <div class="bg-white rounded-3xl shadow-2xl border-0 overflow-hidden backdrop-blur-sm bg-white/95">
            <div class="bg-gradient-to-r from-emerald-600 via-teal-500 to-cyan-600 px-8 py-8">
                <h2 class="text-2xl font-bold text-white mb-2">{{ currentLocale === 'ar' ? 'بيانات الكورس الجديد' : 'New Course Information' }}</h2>
                <p class="text-emerald-100 text-lg">{{ currentLocale === 'ar' ? 'أدخل جميع بيانات الكورس التعليمي' : 'Enter all educational course details' }}</p>
            </div>
            <form @submit.prevent="submit" class="p-8 space-y-10">
                <!-- Basic Information -->
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-8 border border-gray-100 shadow-lg">
                    <div class="flex items-center mb-8">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mr-4 rtl:mr-0 rtl:ml-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ t('basic_info') }}</h2>
                            <p class="text-gray-600">{{ currentLocale === 'ar' ? 'المعلومات الأساسية للكورس' : 'Basic course information' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Title -->
                        <div class="group">
                            <label for="title" class="block text-sm font-bold text-gray-800 mb-3 transition-colors group-focus-within:text-emerald-600">
                                {{ t('title') }} <span class="text-red-500">*</span>
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
                                    class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-300 shadow-sm hover:shadow-md focus:shadow-lg"
                                    :class="{ 'border-red-500 ring-red-100': form.errors.title }"
                                    placeholder="Enter course title in English"
                                    required
                                >
                            </div>
                            <div v-if="form.errors.title" class="mt-3 flex items-center text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ form.errors.title }}
                            </div>
                        </div>

                        <!-- Title Arabic -->
                        <div class="group">
                            <label for="title_ar" class="block text-sm font-bold text-gray-800 mb-3 transition-colors group-focus-within:text-emerald-600">
                                {{ t('title_ar') }} <span class="text-gray-500 text-sm">({{ t('optional') }})</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 flex items-center pl-4 rtl:pl-0 rtl:pr-4 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                    </svg>
                                </div>
                                <input
                                    id="title_ar"
                                    v-model="form.title_ar"
                                    type="text"
                                    class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-300 shadow-sm hover:shadow-md focus:shadow-lg"
                                    :class="{ 'border-red-500 ring-red-100': form.errors.title_ar }"
                                    placeholder="أدخل عنوان الكورس بالعربية"
                                >
                            </div>
                            <div v-if="form.errors.title_ar" class="mt-3 flex items-center text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ form.errors.title_ar }}
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2 group">
                            <label for="description" class="block text-sm font-bold text-gray-800 mb-3 transition-colors group-focus-within:text-emerald-600">
                                {{ t('description') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute top-4 left-4 rtl:left-auto rtl:right-4 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                    </svg>
                                </div>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-300 shadow-sm hover:shadow-md focus:shadow-lg resize-none"
                                    :class="{ 'border-red-500 ring-red-100': form.errors.description }"
                                    placeholder="Enter course description in English"
                                    required
                                ></textarea>
                            </div>
                            <div v-if="form.errors.description" class="mt-3 flex items-center text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ form.errors.description }}
                            </div>
                        </div>

                        <!-- Description Arabic -->
                        <div class="md:col-span-2 group">
                            <label for="description_ar" class="block text-sm font-bold text-gray-800 mb-3 transition-colors group-focus-within:text-emerald-600">
                                {{ t('description_ar') }} <span class="text-gray-500 text-sm">({{ t('optional') }})</span>
                            </label>
                            <div class="relative">
                                <div class="absolute top-4 left-4 rtl:left-auto rtl:right-4 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                    </svg>
                                </div>
                                <textarea
                                    id="description_ar"
                                    v-model="form.description_ar"
                                    rows="4"
                                    class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-300 shadow-sm hover:shadow-md focus:shadow-lg resize-none"
                                    :class="{ 'border-red-500 ring-red-100': form.errors.description_ar }"
                                    placeholder="أدخل وصف الكورس بالعربية"
                                ></textarea>
                            </div>
                            <div v-if="form.errors.description_ar" class="mt-3 flex items-center text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ form.errors.description_ar }}
                            </div>
                        </div>

                        <!-- Instructor -->
                        <div class="md:col-span-2">
                            <label for="instructor_id" class="block text-sm font-bold text-gray-800 mb-3 transition-colors group-focus-within:text-emerald-600">
                                {{ t('instructor') }} <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="instructor_id"
                                v-model="form.instructor_id"
                                class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-300 shadow-sm hover:shadow-md focus:shadow-lg"
                                :class="{ 'border-red-500 ring-red-100': form.errors.instructor_id }"
                                required
                            >
                                <option value="">{{ t('select_instructor') }}</option>
                                <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                                    {{ teacher.name }}
                                </option>
                            </select>
                            <div v-if="form.errors.instructor_id" class="mt-3 flex items-center text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ form.errors.instructor_id }}
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="md:col-span-2">
                            <label for="price" class="block text-sm font-bold text-gray-800 mb-3 transition-colors group-focus-within:text-emerald-600">
                                {{ t('price') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 flex items-center pl-4 rtl:pl-0 rtl:pr-4 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <input
                                    id="price"
                                    v-model="form.price"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-300 shadow-sm hover:shadow-md focus:shadow-lg"
                                    :class="{ 'border-red-500 ring-red-100': form.errors.price }"
                                    placeholder="Enter course price"
                                    required
                                >
                            </div>
                            <div v-if="form.errors.price" class="mt-3 flex items-center text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ form.errors.price }}
                            </div>
                        </div>

                        <!-- Duration -->
                        <div class="md:col-span-2">
                            <label for="duration_hours" class="block text-sm font-bold text-gray-800 mb-3 transition-colors group-focus-within:text-emerald-600">
                                {{ t('duration') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 flex items-center pl-4 rtl:pl-0 rtl:pr-4 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <input
                                    id="duration_hours"
                                    v-model="form.duration_hours"
                                    type="number"
                                    min="1"
                                    class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-300 shadow-sm hover:shadow-md focus:shadow-lg"
                                    :class="{ 'border-red-500 ring-red-100': form.errors.duration_hours }"
                                    placeholder="Enter course duration in hours"
                                    required
                                >
                            </div>
                            <div v-if="form.errors.duration_hours" class="mt-3 flex items-center text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ form.errors.duration_hours }}
                            </div>
                        </div>

                        <!-- Level -->
                        <div class="md:col-span-2">
                            <label for="level" class="block text-sm font-bold text-gray-800 mb-3 transition-colors group-focus-within:text-emerald-600">
                                {{ t('level') }} <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="level"
                                v-model="form.level"
                                class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-300 shadow-sm hover:shadow-md focus:shadow-lg"
                                :class="{ 'border-red-500 ring-red-100': form.errors.level }"
                                required
                            >
                                <option value="beginner">{{ t('beginner') }}</option>
                                <option value="intermediate">{{ t('intermediate') }}</option>
                                <option value="advanced">{{ t('advanced') }}</option>
                            </select>
                            <div v-if="form.errors.level" class="mt-3 flex items-center text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ form.errors.level }}
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="md:col-span-2">
                            <label for="status" class="block text-sm font-bold text-gray-800 mb-3 transition-colors group-focus-within:text-emerald-600">
                                {{ t('status') }} <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-300 shadow-sm hover:shadow-md focus:shadow-lg"
                                :class="{ 'border-red-500 ring-red-100': form.errors.status }"
                                required
                            >
                                <option value="draft">{{ t('draft') }}</option>
                                <option value="published">{{ t('published') }}</option>
                                <option value="archived">{{ t('archived') }}</option>
                            </select>
                            <div v-if="form.errors.status" class="mt-3 flex items-center text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ form.errors.status }}
                            </div>
                        </div>

                        <!-- Max Students -->
                        <div class="md:col-span-2">
                            <label for="max_students" class="block text-sm font-bold text-gray-800 mb-3 transition-colors group-focus-within:text-emerald-600">
                                {{ t('max_students') }} <span class="text-gray-500 text-sm">({{ t('optional') }})</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 flex items-center pl-4 rtl:pl-0 rtl:pr-4 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <input
                                    id="max_students"
                                    v-model="form.max_students"
                                    type="number"
                                    min="1"
                                    class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-300 shadow-sm hover:shadow-md focus:shadow-lg"
                                    :class="{ 'border-red-500 ring-red-100': form.errors.max_students }"
                                    placeholder="Enter max students (optional)"
                                >
                            </div>
                            <div v-if="form.errors.max_students" class="mt-3 flex items-center text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ form.errors.max_students }}
                            </div>
                        </div>

                        <!-- Start Date -->
                        <div class="md:col-span-2">
                            <label for="start_date" class="block text-sm font-bold text-gray-800 mb-3 transition-colors group-focus-within:text-emerald-600">
                                {{ t('start_date') }} <span class="text-gray-500 text-sm">({{ t('optional') }})</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 flex items-center pl-4 rtl:pl-0 rtl:pr-4 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input
                                    id="start_date"
                                    v-model="form.start_date"
                                    type="date"
                                    class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-300 shadow-sm hover:shadow-md focus:shadow-lg"
                                    :class="{ 'border-red-500 ring-red-100': form.errors.start_date }"
                                >
                            </div>
                            <div v-if="form.errors.start_date" class="mt-3 flex items-center text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ form.errors.start_date }}
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="md:col-span-2">
                            <label for="end_date" class="block text-sm font-bold text-gray-800 mb-3 transition-colors group-focus-within:text-emerald-600">
                                {{ t('end_date') }} <span class="text-gray-500 text-sm">({{ t('optional') }})</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 flex items-center pl-4 rtl:pl-0 rtl:pr-4 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input
                                    id="end_date"
                                    v-model="form.end_date"
                                    type="date"
                                    class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-white border-2 border-gray-200 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition-all duration-300 shadow-sm hover:shadow-md focus:shadow-lg"
                                    :class="{ 'border-red-500 ring-red-100': form.errors.end_date }"
                                >
                            </div>
                            <div v-if="form.errors.end_date" class="mt-3 flex items-center text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
                                <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ form.errors.end_date }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requirements -->
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-8 border border-gray-100 shadow-lg">
                    <div class="flex items-center mb-8">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mr-4 rtl:mr-0 rtl:ml-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm3 0a6 6 0 00-9.35-5.14m-.65 5.14a6 6 0 11-12.002 0A6 6 0 0112 12zm0 0c2.21 0 4 1.79 4 4s-1.79 4-4 4-4-1.79-4-4 1.79-4 4-4zm-8 0c2.21 0 4 1.79 4 4s-1.79 4-4 4-4-1.79-4-4 1.79-4 4-4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ t('requirements') }}</h2>
                            <p class="text-gray-600">{{ currentLocale === 'ar' ? 'المتطلبات الأساسية للكورس' : 'Basic course requirements' }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div v-for="(requirement, index) in form.requirements" :key="index" class="flex items-center space-x-3 rtl:space-x-reverse p-4 bg-gray-50 rounded-lg">
                            <span class="flex-1 px-3 py-2 bg-white rounded-lg text-sm font-medium text-gray-900">{{ requirement }}</span>
                            <button @click="removeRequirement(index)" type="button" class="text-red-600 hover:text-red-800 p-2">
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
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-8 border border-gray-100 shadow-lg">
                    <div class="flex items-center mb-8">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mr-4 rtl:mr-0 rtl:ml-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm3 0a6 6 0 00-9.35-5.14m-.65 5.14a6 6 0 11-12.002 0A6 6 0 0112 12zm0 0c2.21 0 4 1.79 4 4s-1.79 4-4 4-4-1.79-4-4 1.79-4 4-4zm-8 0c2.21 0 4 1.79 4 4s-1.79 4-4 4-4-1.79-4-4 1.79-4 4-4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ t('learning_outcomes') }}</h2>
                            <p class="text-gray-600">{{ currentLocale === 'ar' ? 'مخرجات التعلم الأساسية' : 'Basic learning outcomes' }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div v-for="(outcome, index) in form.learning_outcomes" :key="index" class="flex items-center space-x-3 rtl:space-x-reverse p-4 bg-gray-50 rounded-lg">
                            <span class="flex-1 px-3 py-2 bg-white rounded-lg text-sm font-medium text-gray-900">{{ outcome }}</span>
                            <button @click="removeOutcome(index)" type="button" class="text-red-600 hover:text-red-800 p-2">
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

                <!-- Course Schedules -->
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-8 border border-gray-100 shadow-lg">
                    <div class="flex items-center mb-8">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mr-4 rtl:mr-0 rtl:ml-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm3 0a6 6 0 00-9.35-5.14m-.65 5.14a6 6 0 11-12.002 0A6 6 0 0112 12zm0 0c2.21 0 4 1.79 4 4s-1.79 4-4 4-4-1.79-4-4 1.79-4 4-4zm-8 0c2.21 0 4 1.79 4 4s-1.79 4-4 4-4-1.79-4-4 1.79-4 4-4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ t('schedules') }}</h2>
                            <p class="text-gray-600">{{ currentLocale === 'ar' ? 'جدول الكورسات' : 'Course Schedules' }}</p>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <!-- Existing Schedules -->
                        <div v-for="(schedule, index) in form.schedules" :key="index" class="flex items-center space-x-4 rtl:space-x-reverse p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200 shadow-sm">
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center">
                                    <span class="text-sm font-bold text-blue-900 mb-2 block">{{ t('day_of_week') }}</span>
                                    <p class="text-lg font-semibold text-blue-800 bg-white px-4 py-2 rounded-lg shadow-sm">{{ t(schedule.day_of_week) }}</p>
                                </div>
                                <div class="text-center">
                                    <span class="text-sm font-bold text-blue-900 mb-2 block">{{ t('start_time') }}</span>
                                    <p class="text-lg font-semibold text-blue-800 bg-white px-4 py-2 rounded-lg shadow-sm">{{ schedule.start_time }}</p>
                                </div>
                                <div class="text-center">
                                    <span class="text-sm font-bold text-blue-900 mb-2 block">{{ t('end_time') }}</span>
                                    <p class="text-lg font-semibold text-blue-800 bg-white px-4 py-2 rounded-lg shadow-sm">{{ schedule.end_time }}</p>
                                </div>
                            </div>
                            <button @click="removeSchedule(index)" type="button" class="text-red-600 hover:text-red-800 p-3 bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Add New Schedule -->
                        <div class="border-2 border-dashed border-blue-300 rounded-2xl p-8 bg-gradient-to-br from-blue-50 to-indigo-50">
                            <div class="text-center mb-6">
                                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <h4 class="text-xl font-bold text-blue-900 mb-2">{{ t('add_schedule') }}</h4>
                                <p class="text-blue-600">{{ currentLocale === 'ar' ? 'أضف موعد جديد للكورس' : 'Add a new schedule for the course' }}</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="group">
                                    <label class="block text-sm font-bold text-blue-900 mb-3">{{ t('day_of_week') }}</label>
                                    <select v-model="newSchedule.day_of_week" class="w-full px-4 py-4 text-gray-900 bg-white border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 shadow-sm hover:shadow-md focus:shadow-lg">
                                        <option value="saturday">{{ t('saturday') }}</option>
                                        <option value="sunday">{{ t('sunday') }}</option>
                                        <option value="monday">{{ t('monday') }}</option>
                                        <option value="tuesday">{{ t('tuesday') }}</option>
                                        <option value="wednesday">{{ t('wednesday') }}</option>
                                        <option value="thursday">{{ t('thursday') }}</option>
                                        <option value="friday">{{ t('friday') }}</option>
                                    </select>
                                </div>
                                <div class="group">
                                    <label class="block text-sm font-bold text-blue-900 mb-3">{{ t('start_time') }}</label>
                                    <input
                                        v-model="newSchedule.start_time"
                                        type="time"
                                        class="w-full px-4 py-4 text-gray-900 bg-white border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 shadow-sm hover:shadow-md focus:shadow-lg"
                                    >
                                </div>
                                <div class="group">
                                    <label class="block text-sm font-bold text-blue-900 mb-3">{{ t('end_time') }}</label>
                                    <input
                                        v-model="newSchedule.end_time"
                                        type="time"
                                        class="w-full px-4 py-4 text-gray-900 bg-white border-2 border-blue-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 shadow-sm hover:shadow-md focus:shadow-lg"
                                    >
                                </div>
                            </div>
                            <div class="text-center mt-6">
                                <button @click="addSchedule" type="button" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                    <svg class="w-5 h-5 inline mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    {{ t('add_schedule') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-gradient-to-r from-gray-50 to-white -mx-8 -mb-8 px-8 py-8 mt-10 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <Link :href="route('courses.index')" class="group flex items-center px-8 py-4 text-gray-600 bg-white border-2 border-gray-300 rounded-2xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <svg class="w-6 h-6 mr-3 rtl:mr-0 rtl:ml-3 group-hover:-translate-x-1 rtl:group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            <span class="text-lg font-semibold">{{ t('cancel') }}</span>
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="group flex items-center px-10 py-4 bg-gradient-to-r from-emerald-600 via-teal-500 to-cyan-600 text-white rounded-2xl hover:from-emerald-700 hover:via-teal-600 hover:to-cyan-700 focus:ring-4 focus:ring-emerald-300 transition-all duration-300 shadow-2xl hover:shadow-3xl disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-2xl transform hover:scale-105 active:scale-95"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-6 h-6 mr-3 rtl:mr-0 rtl:ml-3 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span class="text-lg font-bold">{{ form.processing ? (currentLocale === 'ar' ? 'جاري الإنشاء...' : 'Creating...') : t('create') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
