<script setup>
import TeacherLayout from '@/Layouts/TeacherLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');
const user = computed(() => page.props.auth.user);

// البيانات من Controller
const props = defineProps({
    stats: Object,
    courses: Array,
    recentActivity: Array,
    topCourses: Array,
    upcomingClasses: Array,
    messages: Object,
    hasData: Boolean,
    teacherName: String,
    teacherEmail: String,
});

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            teacher_dashboard: 'Teacher Dashboard',
            welcome_message: 'Welcome to your teaching portal',
            my_courses: 'My Courses',
            course_details: 'Course Details',
            course_title: 'Course Title',
            course_description: 'Description',
            course_price: 'Price',
            course_duration: 'Duration',
            course_level: 'Level',
            course_status: 'Status',
            course_students: 'Students',
            course_start_date: 'Start Date',
            course_end_date: 'End Date',
            course_requirements: 'Requirements',
            course_outcomes: 'Learning Outcomes',
            no_courses: 'No courses assigned to you yet',
            no_courses_message: 'You don\'t have any courses assigned to you yet. Contact the administrator to get courses assigned.',
            contact_admin: 'Contact Administrator',
            course_management: 'Course Management',
            view_course: 'View Course',
            edit_course: 'Edit Course',
            course_info: 'Course Information',
            total_courses: 'Total Courses',
            active_courses: 'Active Courses',
            total_students: 'Total Students',
        },
        ar: {
            teacher_dashboard: 'لوحة تحكم المعلم',
            welcome_message: 'مرحباً بك في بوابة التدريس',
            my_courses: 'كورساتي',
            course_details: 'تفاصيل الكورس',
            course_title: 'عنوان الكورس',
            course_description: 'الوصف',
            course_price: 'السعر',
            course_duration: 'المدة',
            course_level: 'المستوى',
            course_status: 'الحالة',
            course_students: 'الطلاب',
            course_start_date: 'تاريخ البداية',
            course_end_date: 'تاريخ الانتهاء',
            course_requirements: 'المتطلبات',
            course_outcomes: 'النتائج التعليمية',
            no_courses: 'لا توجد كورسات مخصصة لك بعد',
            no_courses_message: 'لا توجد كورسات مخصصة لك بعد. اتصل بالإدارة للحصول على كورسات.',
            contact_admin: 'اتصل بالإدارة',
            course_management: 'إدارة الكورسات',
            view_course: 'عرض الكورس',
            edit_course: 'تعديل الكورس',
            course_info: 'معلومات الكورس',
            total_courses: 'إجمالي الكورسات',
            active_courses: 'الكورسات النشطة',
            total_students: 'إجمالي الطلاب',
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

// استخدام البيانات من Controller أو القيم الافتراضية
const stats = computed(() => props.stats || {
    activeCourses: 0,
    totalStudents: 0,
});

const courses = computed(() => props.courses || []);
const hasData = computed(() => props.hasData || false);
const teacherName = computed(() => props.teacherName || user.value?.name);
const teacherEmail = computed(() => props.teacherEmail || user.value?.email);
</script>

<template>
    <Head :title="t('teacher_dashboard')" />

    <TeacherLayout>
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ t('teacher_dashboard') }}</h1>
            <p class="text-gray-600">{{ t('welcome_message') }}</p>
            <div class="mt-4 text-sm text-gray-500">
                {{ currentLocale === 'en' ? 'Good morning, Professor' : 'صباح الخير، أستاذ' }} {{ teacherName }}
            </div>
        </div>

        <!-- Empty Dashboard Message -->
        <div v-if="!hasData" class="text-center py-16">
            <div class="max-w-2xl mx-auto">
                <!-- Empty State Icon -->
                <div class="mx-auto h-24 w-24 text-gray-300 mb-6">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                    </svg>
                </div>
                
                <!-- Empty State Title -->
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    {{ t('no_courses') }}
                </h2>
                
                <!-- Empty State Message -->
                <p class="text-lg text-gray-600 mb-8">
                    {{ t('no_courses_message') }}
                </p>
                
                <!-- Action Button -->
                <div class="flex justify-center">
                    <a href="/admin/courses" 
                       class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="h-5 w-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ t('contact_admin') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Dashboard Content - Only show when there are courses -->
        <div v-else>
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Courses -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ courses.length }}</h3>
                            <p class="text-sm text-gray-500">{{ t('total_courses') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Active Courses -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ stats.activeCourses }}</h3>
                            <p class="text-sm text-gray-500">{{ t('active_courses') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Students -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-orange-500 to-red-600 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ stats.totalStudents }}</h3>
                            <p class="text-sm text-gray-500">{{ t('total_students') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Courses Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">{{ t('my_courses') }}</h2>
                    <span class="text-sm text-gray-500">{{ courses.length }} {{ currentLocale === 'en' ? 'course(s)' : 'كورس' }}</span>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div v-for="course in courses" :key="course.id" class="border border-gray-200 rounded-lg p-6 hover:border-emerald-300 transition-colors">
                        <!-- Course Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ course.localized_title }}</h3>
                                <p class="text-gray-600 text-sm line-clamp-2">{{ course.localized_description }}</p>
                            </div>
                            <div class="flex flex-col items-end space-y-2 rtl:space-y-reverse">
                                <span class="px-3 py-1 text-xs font-medium rounded-full" 
                                      :class="course.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                    {{ course.status === 'published' ? (currentLocale === 'en' ? 'Published' : 'منشور') : (currentLocale === 'en' ? 'Draft' : 'مسودة') }}
                                </span>
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                    {{ course.level === 'beginner' ? (currentLocale === 'en' ? 'Beginner' : 'مبتدئ') : 
                                       course.level === 'intermediate' ? (currentLocale === 'en' ? 'Intermediate' : 'متوسط') : 
                                       course.level === 'advanced' ? (currentLocale === 'en' ? 'Advanced' : 'متقدم') : course.level }}
                                </span>
                            </div>
                        </div>

                        <!-- Course Details Grid -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-gray-600">{{ t('course_price') }}: <strong>${{ course.price }}</strong></span>
                            </div>
                            
                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-gray-600">{{ t('course_duration') }}: <strong>{{ course.duration_hours }}h</strong></span>
                            </div>
                            
                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                                </svg>
                                <span class="text-sm text-gray-600">{{ t('course_students') }}: <strong>{{ course.enrollments?.length || 0 }}</strong></span>
                            </div>
                            
                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 002.25 2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 012.25 2.25v7.5"></path>
                                </svg>
                                <span class="text-sm text-gray-600">{{ t('course_start_date') }}: <strong>{{ course.start_date ? new Date(course.start_date).toLocaleDateString() : 'N/A' }}</strong></span>
                            </div>
                        </div>

                        <!-- Course Requirements & Outcomes -->
                        <div class="space-y-3 mb-4">
                            <div v-if="course.requirements && course.requirements.length > 0">
                                <h4 class="text-sm font-medium text-gray-700 mb-1">{{ t('course_requirements') }}:</h4>
                                <ul class="text-xs text-gray-600 space-y-1">
                                    <li v-for="(req, index) in course.requirements" :key="index" class="flex items-center">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-2 rtl:mr-0 rtl:ml-2"></span>
                                        {{ req }}
                                    </li>
                                </ul>
                            </div>
                            
                            <div v-if="course.learning_outcomes && course.learning_outcomes.length > 0">
                                <h4 class="text-sm font-medium text-gray-700 mb-1">{{ t('course_outcomes') }}:</h4>
                                <ul class="text-xs text-gray-600 space-y-1">
                                    <li v-for="(outcome, index) in course.learning_outcomes" :key="index" class="flex items-center">
                                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-2 rtl:mr-0 rtl:ml-2"></span>
                                        {{ outcome }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Course Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="text-sm text-gray-500">
                                {{ currentLocale === 'en' ? 'Created' : 'تم الإنشاء' }}: {{ new Date(course.created_at).toLocaleDateString() }}
                            </div>
                            <div class="flex space-x-2 rtl:space-x-reverse">
                                <a :href="`/admin/courses/${course.id}`" 
                                   class="px-3 py-1 text-xs font-medium text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-md transition-colors">
                                    {{ t('view_course') }}
                                </a>
                                <a :href="`/admin/courses/${course.id}/edit`" 
                                   class="px-3 py-1 text-xs font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-md transition-colors">
                                    {{ t('edit_course') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>
