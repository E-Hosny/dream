<script setup>
import StudentLayout from '@/Layouts/StudentLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');
const user = computed(() => page.props.auth.user);

// استقبال البيانات من الخادم
const enrollments = computed(() => page.props.enrollments || []);

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            student_dashboard: 'Student Dashboard',
            welcome_message: 'Welcome to your learning journey',
            my_courses: 'My Courses',
            continue_learning: 'Continue Learning',
            view_course: 'View Course',
            completed: 'Completed',
            in_progress: 'In Progress',
            not_started: 'Not Started',
            progress: 'Progress',
            instructor: 'Instructor',
            last_accessed: 'Last accessed',
            next_lesson: 'Next lesson',
            no_courses: 'No courses enrolled yet',
            browse_courses: 'Browse Available Courses',
        },
        ar: {
            student_dashboard: 'لوحة تحكم الطالب',
            welcome_message: 'مرحباً بك في رحلة التعلم',
            my_courses: 'كورساتي',
            continue_learning: 'متابعة التعلم',
            view_course: 'عرض الكورس',
            completed: 'مكتمل',
            in_progress: 'قيد التقدم',
            not_started: 'لم يبدأ',
            progress: 'التقدم',
            instructor: 'المدرب',
            last_accessed: 'آخر وصول',
            next_lesson: 'الدرس التالي',
            no_courses: 'لا توجد كورسات مسجلة بعد',
            browse_courses: 'تصفح الكورسات المتاحة',
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

const getStatusColor = (status) => {
    switch (status) {
        case 'completed':
            return 'bg-green-100 text-green-800';
        case 'in_progress':
            return 'bg-blue-100 text-blue-800';
        case 'not_started':
            return 'bg-gray-100 text-gray-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const getStatusText = (status) => {
    return t(status);
};
</script>

<template>
    <Head :title="t('student_dashboard')" />

    <StudentLayout>
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ t('student_dashboard') }}</h1>
            <p class="text-gray-600">{{ t('welcome_message') }}</p>
            <div class="mt-4 text-sm text-gray-500">
                {{ currentLocale === 'en' ? 'Keep learning,' : 'واصل التعلم،' }} {{ user.name }}
            </div>
        </div>

        <!-- My Courses Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">{{ t('my_courses') }}</h2>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    {{ t('browse_courses') }}
                </button>
            </div>
            
            <div v-if="enrollments.length > 0" class="space-y-6">
                <div v-for="enrollment in enrollments" :key="enrollment.id" 
                     class="p-6 rounded-lg border border-gray-200 hover:border-blue-300 transition-all duration-200 hover:shadow-md">
                    
                    <!-- Course Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                {{ currentLocale === 'ar' ? enrollment.course.title : enrollment.course.titleEn }}
                            </h3>
                            <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                <span class="text-sm text-gray-600">
                                    {{ currentLocale === 'en' ? 'by' : 'بواسطة' }} {{ enrollment.course.instructor }}
                                </span>
                                <span :class="`px-3 py-1 text-xs font-medium rounded-full ${getStatusColor(enrollment.status)}`">
                                    {{ getStatusText(enrollment.status) }}
                                </span>
                            </div>
                        </div>
                        <button class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            {{ t('view_course') }}
                        </button>
                    </div>
                    
                    <!-- Progress Section -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-600 font-medium">{{ t('progress') }}</span>
                            <span class="text-gray-500">{{ enrollment.progress }}% {{ t('completed') }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div :class="`h-3 rounded-full bg-gradient-to-r ${enrollment.color}`" 
                                 :style="`width: ${enrollment.progress}%`"></div>
                        </div>
                    </div>
                    
                    <!-- Course Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-600">{{ t('last_accessed') }}: {{ enrollment.lastAccessed }}</span>
                        </div>
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                            </svg>
                            <span class="text-gray-600">{{ t('next_lesson') }}: {{ currentLocale === 'ar' ? enrollment.nextLesson : enrollment.nextLessonEn }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ t('no_courses') }}</h3>
                <p class="text-gray-500 mb-4">{{ t('browse_courses') }}</p>
                <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    {{ t('browse_courses') }}
                </button>
            </div>
        </div>
    </StudentLayout>
</template>
