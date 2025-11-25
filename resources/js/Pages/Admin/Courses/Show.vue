<template>
    <AdminLayout>
        <Head :title="t('course_details')" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ t('course_details') }}
                                </h1>
                                <p class="text-gray-600 dark:text-gray-400 mt-1">
                                    {{ t('view_course_information') }}
                                </p>
                            </div>
                            <div class="flex space-x-3 rtl:space-x-reverse">
                                <Link
                                    :href="route('admin.courses.edit', course.id)"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    <svg class="w-4 h-4 ml-2 rtl:ml-0 rtl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    {{ t('edit_course') }}
                                </Link>
                                <Link
                                    :href="route('admin.courses.index')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    <svg class="w-4 h-4 ml-2 rtl:ml-0 rtl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    {{ t('back_to_courses') }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Information Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Course Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Course Information -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    {{ t('basic_information') }}
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ t('title') }}
                                        </label>
                                        <p class="text-gray-900 dark:text-white">{{ course.localized_title }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ t('status') }}
                                        </label>
                                        <span :class="getStatusClass(course.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                            {{ t(course.status) }}
                                        </span>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ t('level') }}
                                        </label>
                                        <span :class="getLevelClass(course.level)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                            {{ t(course.level) }}
                                        </span>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ t('price') }}
                                        </label>
                                        <p class="text-gray-900 dark:text-white">{{ formatCurrency(course.price) }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ t('duration') }}
                                        </label>
                                        <p class="text-gray-900 dark:text-white">{{ course.duration_hours }} {{ t('hours') }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ t('max_students') }}
                                        </label>
                                        <p class="text-gray-900 dark:text-white">
                                            {{ course.max_students ? course.max_students : t('unlimited') }}
                                        </p>
                                    </div>
                                    <div v-if="course.start_date">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ t('start_date') }}
                                        </label>
                                        <p class="text-gray-900 dark:text-white">{{ formatDate(course.start_date) }}</p>
                                    </div>
                                    <div v-if="course.end_date">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ t('end_date') }}
                                        </label>
                                        <p class="text-gray-900 dark:text-white">{{ formatDate(course.end_date) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    {{ t('description') }}
                                </h2>
                                <p class="text-gray-700 dark:text-gray-300">{{ course.localized_description }}</p>
                            </div>
                        </div>

                        <!-- Requirements and Learning Outcomes -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">
                                            {{ t('requirements') }}
                                        </h3>
                                        <ul v-if="course.requirements && course.requirements.length > 0" class="list-disc list-inside space-y-1">
                                            <li v-for="requirement in course.requirements" :key="requirement" class="text-gray-700 dark:text-gray-300">
                                                {{ requirement }}
                                            </li>
                                        </ul>
                                        <p v-else class="text-gray-500 dark:text-gray-400">{{ t('no_requirements') }}</p>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">
                                            {{ t('learning_outcomes') }}
                                        </h3>
                                        <ul v-if="course.learning_outcomes && course.learning_outcomes.length > 0" class="list-disc list-inside space-y-1">
                                            <li v-for="outcome in course.learning_outcomes" :key="outcome" class="text-gray-700 dark:text-gray-300">
                                                {{ outcome }}
                                            </li>
                                        </ul>
                                        <p v-else class="text-gray-500 dark:text-gray-400">{{ t('no_outcomes') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Schedules -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    {{ t('course_schedules') }}
                                </h2>
                                <div v-if="course.schedules && course.schedules.length > 0" class="space-y-3">
                                    <div v-for="schedule in course.schedules" :key="schedule.id" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">{{ t(schedule.day_of_week) }}</p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ schedule.start_time }} - {{ schedule.end_time }}
                                                </p>
                                            </div>
                                        </div>
                                        <span :class="schedule.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 text-xs font-medium rounded-full">
                                            {{ schedule.is_active ? t('active') : t('inactive') }}
                                        </span>
                                    </div>
                                </div>
                                <p v-else class="text-gray-500 dark:text-gray-400">{{ t('no_schedules') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Instructor Information -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    {{ t('instructor') }}
                                </h2>
                                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                    <div class="w-12 h-12 bg-gray-300 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ course.instructor.name }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ course.instructor.email }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Statistics -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    {{ t('statistics') }}
                                </h2>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">{{ t('enrolled_students') }}</span>
                                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                            {{ course.enrollments ? course.enrollments.length : 0 }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">{{ t('available_spots') }}</span>
                                        <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ course.available_spots !== null ? course.available_spots : t('unlimited') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">{{ t('created_at') }}</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ formatDate(course.created_at) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    {{ t('quick_actions') }}
                                </h2>
                                <div class="space-y-3">
                                    <Link
                                        :href="route('admin.enrollments.index', { course: course.id })"
                                        class="w-full flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    >
                                        <svg class="w-4 h-4 ml-2 rtl:ml-0 rtl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                        {{ t('view_enrollments') }}
                                    </Link>
                                    <button
                                        @click="deleteCourse"
                                        class="w-full flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    >
                                        <svg class="w-4 h-4 ml-2 rtl:ml-0 rtl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        {{ t('delete_course') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enrolled Students Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ t('enrolled_students') }} ({{ course.enrollments ? course.enrollments.length : 0 }})
                            </h2>
                            <Link
                                :href="route('admin.enrollments.create', { course: course.id })"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 ml-2 rtl:ml-0 rtl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                {{ t('add_student') }}
                            </Link>
                        </div>
                        
                        <div v-if="course.enrollments && course.enrollments.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            {{ t('student') }}
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            {{ t('enrollment_date') }}
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            {{ t('status') }}
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            {{ t('progress') }}
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            {{ t('actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="enrollment in course.enrollments" :key="enrollment.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gray-300 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                                <div class="mr-4 rtl:mr-0 rtl:ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ enrollment.student.name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ enrollment.student.email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ formatDate(enrollment.enrolled_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getEnrollmentStatusClass(enrollment.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                                {{ t(enrollment.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" :style="{ width: (enrollment.progress || 0) + '%' }"></div>
                                                </div>
                                                <span class="text-sm text-gray-900 dark:text-white">{{ enrollment.progress || 0 }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <Link
                                                :href="route('admin.enrollments.edit', enrollment.id)"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 ml-4 rtl:ml-0 rtl:mr-4"
                                            >
                                                {{ t('edit') }}
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ t('no_students') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ t('no_students_enrolled') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'ar');

const props = defineProps({
    course: Object
});

const form = useForm({});

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            course_details: 'Course Details',
            view_course_information: 'View course information, instructor details, schedules, and enrolled students',
            edit_course: 'Edit Course',
            back_to_courses: 'Back to Courses',
            basic_information: 'Basic Information',
            title: 'Title',
            status: 'Status',
            level: 'Level',
            price: 'Price',
            duration: 'Duration',
            max_students: 'Max Students',
            start_date: 'Start Date',
            end_date: 'End Date',
            hours: 'hours',
            unlimited: 'Unlimited',
            description: 'Description',
            requirements: 'Requirements',
            learning_outcomes: 'Learning Outcomes',
            no_requirements: 'No requirements specified',
            no_outcomes: 'No learning outcomes specified',
            course_schedules: 'Course Schedules',
            no_schedules: 'No schedules available',
            active: 'Active',
            inactive: 'Inactive',
            instructor: 'Instructor',
            statistics: 'Statistics',
            enrolled_students: 'Enrolled Students',
            available_spots: 'Available Spots',
            created_at: 'Created At',
            quick_actions: 'Quick Actions',
            view_enrollments: 'View Enrollments',
            delete_course: 'Delete Course',
            add_student: 'Add Student',
            student: 'Student',
            enrollment_date: 'Enrollment Date',
            progress: 'Progress',
            actions: 'Actions',
            edit: 'Edit',
            no_students: 'No students enrolled',
            no_students_enrolled: 'No students have enrolled in this course yet.',
            // Status translations
            draft: 'Draft',
            published: 'Published',
            archived: 'Archived',
            // Level translations
            beginner: 'Beginner',
            intermediate: 'Intermediate',
            advanced: 'Advanced',
            // Enrollment status translations
            enrolled: 'Enrolled',
            completed: 'Completed',
            dropped: 'Dropped',
            // Day translations
            saturday: 'Saturday',
            sunday: 'Sunday',
            monday: 'Monday',
            tuesday: 'Tuesday',
            wednesday: 'Wednesday',
            thursday: 'Thursday',
            friday: 'Friday'
        },
        ar: {
            course_details: 'تفاصيل الكورس',
            view_course_information: 'عرض معلومات الكورس وتفاصيل المعلم والجدولة والطلاب المسجلين',
            edit_course: 'تعديل الكورس',
            back_to_courses: 'العودة للكورسات',
            basic_information: 'المعلومات الأساسية',
            title: 'العنوان',
            status: 'الحالة',
            level: 'المستوى',
            price: 'السعر',
            duration: 'المدة',
            max_students: 'الحد الأقصى للطلاب',
            start_date: 'تاريخ البداية',
            end_date: 'تاريخ الانتهاء',
            hours: 'ساعات',
            unlimited: 'غير محدود',
            description: 'الوصف',
            requirements: 'المتطلبات',
            learning_outcomes: 'النتائج التعليمية',
            no_requirements: 'لا توجد متطلبات محددة',
            no_outcomes: 'لا توجد نتائج تعليمية محددة',
            course_schedules: 'جدولة الكورس',
            no_schedules: 'لا توجد جداول متاحة',
            active: 'نشط',
            inactive: 'غير نشط',
            instructor: 'المعلم',
            statistics: 'الإحصائيات',
            enrolled_students: 'الطلاب المسجلين',
            available_spots: 'الأماكن المتاحة',
            created_at: 'تاريخ الإنشاء',
            quick_actions: 'إجراءات سريعة',
            view_enrollments: 'عرض التسجيلات',
            delete_course: 'حذف الكورس',
            add_student: 'إضافة طالب',
            student: 'الطالب',
            enrollment_date: 'تاريخ التسجيل',
            progress: 'التقدم',
            actions: 'الإجراءات',
            edit: 'تعديل',
            no_students: 'لا يوجد طلاب مسجلين',
            no_students_enrolled: 'لم يسجل أي طالب في هذا الكورس بعد.',
            // Status translations
            draft: 'مسودة',
            published: 'منشور',
            archived: 'مؤرشف',
            // Level translations
            beginner: 'مبتدئ',
            intermediate: 'متوسط',
            advanced: 'متقدم',
            // Enrollment status translations
            enrolled: 'مسجل',
            completed: 'مكتمل',
            dropped: 'منسحب',
            // Day translations
            saturday: 'السبت',
            sunday: 'الأحد',
            monday: 'الاثنين',
            tuesday: 'الثلاثاء',
            wednesday: 'الأربعاء',
            thursday: 'الخميس',
            friday: 'الجمعة'
        }
    };
    
    return translations[currentLocale.value]?.[key] || key;
};

// Helper functions
const getStatusClass = (status) => {
    const classes = {
        draft: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        published: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-300',
        archived: 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-300'
    };
    return classes[status] || classes.draft;
};

const getLevelClass = (level) => {
    const classes = {
        beginner: 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-300',
        intermediate: 'bg-purple-100 text-purple-800 dark:bg-purple-700 dark:text-purple-300',
        advanced: 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-300'
    };
    return classes[level] || classes.beginner;
};

const getEnrollmentStatusClass = (status) => {
    const classes = {
        enrolled: 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-300',
        completed: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-300',
        dropped: 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-300'
    };
    return classes[status] || classes.enrolled;
};

const formatDate = (date) => {
    if (!date) return '';
    // استخدام التقويم الميلادي دائماً (gregory) بدلاً من الهجري
    return new Date(date).toLocaleDateString(currentLocale.value === 'ar' ? 'ar-SA-u-ca-gregory' : 'en-US');
};

const formatCurrency = (price) => {
    if (price === null || price === undefined) return 'N/A';
    return new Intl.NumberFormat(currentLocale.value === 'ar' ? 'ar-EG' : 'en-US', {
        style: 'currency',
        currency: 'EGP' // Assuming EGP for Egyptian Pound
    }).format(price);
};

const deleteCourse = () => {
    if (confirm(t('delete_course_confirmation'))) {
        form.delete(route('admin.courses.destroy', props.course.id));
    }
};
</script>
