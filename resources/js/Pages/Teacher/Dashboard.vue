<script setup>
import TeacherLayout from '@/Layouts/TeacherLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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
    user: Object,
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
            start_meeting: 'Start Meeting',
            end_meeting: 'End Meeting',
            meeting_active: 'Meeting Active',
            meeting_topic: 'Topic',
        },
        ar: {
            teacher_dashboard: 'لوحة تحكم المعلم',
            welcome_message: 'مرحباً بك في بوابة التدريس',
            my_courses: 'الدروس',
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
            start_meeting: 'ابدأ الاجتماع',
            end_meeting: 'إنهاء الاجتماع',
            meeting_active: 'اجتماع نشط',
            meeting_topic: 'الموضوع',
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

// استخدام user من props أو من auth
const currentUser = computed(() => props.user || user.value);

// Debug logging
console.log('Teacher Dashboard - Props:', props);
console.log('Teacher Dashboard - Current User:', currentUser.value);
console.log('Teacher Dashboard - Auth User:', user.value);

// Meeting state
const showStartMeetingModal = ref(false);
const selectedCourse = ref(null);
const meetingForm = ref({
    course_id: '',
    course_schedule_id: '',
    topic: '',
    duration: 60
});
const meetingLoading = ref(false);

// Helper function to format currency
const formatCurrency = (amount) => {
    return new Intl.NumberFormat(currentLocale.value, {
        style: 'currency',
        currency: 'EGP', // Assuming EGP for Egyptian Pound
    }).format(amount);
};

// Start meeting functions
const startInstantMeeting = (course) => {
    selectedCourse.value = course;
    meetingForm.value = {
        course_id: course.id,
        course_schedule_id: course.nextSchedule?.id || null,
        topic: `${course.title_ar || course.title} - اجتماع فوري`,
        duration: 60
    };
    showStartMeetingModal.value = true;
};

// بدء اجتماع موجود
const startMeeting = async (meetingId) => {
    try {
        const response = await fetch(`/zoom-meetings/${meetingId}/start`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        const data = await response.json();

        if (data.success) {
            alert(currentLocale.value === 'ar' ? 'تم بدء الاجتماع بنجاح!' : 'Meeting started successfully!');
            
            // فتح Zoom في نافذة جديدة
            if (data.start_url) {
                window.open(data.start_url, '_blank');
            }
            
            // إعادة تحميل الصفحة لتحديث البيانات
            window.location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء بدء الاجتماع');
        }
    } catch (error) {
        console.error('Error starting meeting:', error);
        alert('حدث خطأ أثناء بدء الاجتماع');
    }
};

const submitMeeting = async () => {
    if (!meetingForm.value.course_id || !meetingForm.value.topic) {
        alert(currentLocale.value === 'ar' ? 'يرجى ملء جميع الحقول المطلوبة' : 'Please fill all required fields');
        return;
    }

    meetingLoading.value = true;

    try {
        const response = await fetch('/zoom-meetings/start-instant', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(meetingForm.value)
        });

        const data = await response.json();

        if (data.success) {
            alert(currentLocale.value === 'ar' ? 'تم بدء الاجتماع بنجاح!' : 'Meeting started successfully!');
            
            // فتح Zoom في نافذة جديدة
            if (data.data && data.data.start_url) {
                window.open(data.data.start_url, '_blank');
            }
            
            showStartMeetingModal.value = false;
            // إعادة تحميل الصفحة لتحديث البيانات
            window.location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء بدء الاجتماع');
        }
    } catch (error) {
        console.error('Error starting meeting:', error);
        alert('حدث خطأ أثناء بدء الاجتماع');
    } finally {
        meetingLoading.value = false;
    }
};

// إنهاء الاجتماع
const endMeeting = async (courseId) => {
    if (!confirm(currentLocale.value === 'ar' ? 'هل أنت متأكد من إنهاء الاجتماع؟' : 'Are you sure you want to end the meeting?')) {
        return;
    }

    try {
        const response = await fetch(`/teacher/courses/${courseId}/end-meeting`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        const data = await response.json();

        if (data.success) {
            alert(currentLocale.value === 'ar' ? 'تم إنهاء الاجتماع بنجاح!' : 'Meeting ended successfully!');
            // إعادة تحميل الصفحة لتحديث البيانات
            window.location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء إنهاء الاجتماع');
        }
    } catch (error) {
        console.error('Error ending meeting:', error);
        alert('حدث خطأ أثناء إنهاء الاجتماع');
    }
};
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
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

                <!-- Completed Students -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">{{ stats.completedStudents || 0 }}</h3>
                            <p class="text-sm text-gray-500">{{ currentLocale === 'en' ? 'Completed Students' : 'الطلاب المكتملين' }}</p>
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
                
                <div class="space-y-6">
                    <div v-for="course in courses" :key="course.id" class="border border-gray-200 rounded-lg p-6 hover:border-emerald-300 transition-colors">
                        <!-- Course Header -->
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-gray-900 mb-3">{{ currentLocale === 'ar' ? course.title : course.titleEn }}</h3>
                                <p class="text-gray-600 text-base line-clamp-2">{{ currentLocale === 'ar' ? course.description : course.descriptionEn }}</p>
                            </div>
                            <div class="flex flex-col items-end space-y-3 rtl:space-x-reverse">
                                <span class="px-4 py-2 text-sm font-medium rounded-full" 
                                      :class="course.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                                    {{ course.status === 'published' ? (currentLocale === 'en' ? 'Published' : 'منشور') : (currentLocale === 'en' ? 'Draft' : 'مسودة') }}
                                </span>
                                <span class="px-4 py-2 text-sm font-medium rounded-full bg-purple-100 text-purple-800">
                                    {{ course.level === 'beginner' ? (currentLocale === 'en' ? 'Beginner' : 'مبتدئ') : 
                                       course.level === 'intermediate' ? (currentLocale === 'en' ? 'Intermediate' : 'متوسط') : 
                                       course.level === 'advanced' ? (currentLocale === 'en' ? 'Advanced' : 'متقدم') : course.level }}
                                </span>
                            </div>
                        </div>

                        <!-- Course Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                            <div class="flex items-center space-x-3 rtl:space-x-reverse p-4 bg-gray-50 rounded-lg">
                                <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-500">{{ t('course_price') }}</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ formatCurrency(course.price) }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3 rtl:space-x-reverse p-4 bg-gray-50 rounded-lg">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-500">{{ t('course_duration') }}</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ course.duration_hours }}h</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3 rtl:space-x-reverse p-4 bg-gray-50 rounded-lg">
                                <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-500">{{ t('course_students') }}</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ course.enrollments?.length || 0 }}</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3 rtl:space-x-reverse p-4 bg-gray-50 rounded-lg">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 002.25 2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 012.25 2.25v7.5"></path>
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-500">{{ t('course_start_date') }}</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ course.start_date ? new Date(course.start_date).toLocaleDateString(currentLocale === 'ar' ? 'ar-SA-u-ca-gregory' : 'en-US') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Students List -->
                        <div v-if="course.enrollments && course.enrollments.length > 0" class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-4">{{ currentLocale === 'en' ? 'Enrolled Students' : 'الطلاب المسجلين' }}:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div v-for="enrollment in course.enrollments" :key="enrollment.id" 
                                     class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200 hover:border-emerald-300 transition-colors">
                                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-emerald-600">{{ enrollment.student.name.charAt(0) }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ enrollment.student.name }}</p>
                                            <p class="text-xs text-gray-500">{{ enrollment.student.email }}</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end space-y-2">
                                        <span class="px-3 py-1 text-xs font-medium rounded-full"
                                              :class="{
                                                  'bg-blue-100 text-blue-800': enrollment.status === 'enrolled',
                                                  'bg-green-100 text-green-800': enrollment.status === 'completed',
                                                  'bg-red-100 text-red-800': enrollment.status === 'dropped'
                                              }">
                                            {{ enrollment.status === 'enrolled' ? (currentLocale === 'en' ? 'Enrolled' : 'مسجل') :
                                               enrollment.status === 'completed' ? (currentLocale === 'en' ? 'Completed' : 'مكتمل') :
                                               enrollment.status === 'dropped' ? (currentLocale === 'en' ? 'Dropped' : 'منسحب') : enrollment.status }}
                                        </span>
                                        <span v-if="enrollment.progress !== null" class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded">
                                            {{ enrollment.progress }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Requirements & Outcomes -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div v-if="course.requirements && course.requirements.length > 0">
                                <h4 class="text-lg font-semibold text-gray-700 mb-3">{{ t('course_requirements') }}:</h4>
                                <div class="space-y-2">
                                    <div v-for="(req, index) in course.requirements" :key="index" 
                                         class="flex items-center space-x-2 rtl:space-x-reverse p-3 bg-gray-50 rounded-lg">
                                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                        <span class="text-sm text-gray-700">{{ req }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-if="course.learning_outcomes && course.learning_outcomes.length > 0">
                                <h4 class="text-lg font-semibold text-gray-700 mb-3">{{ t('course_outcomes') }}:</h4>
                                <div class="space-y-2">
                                    <div v-for="(outcome, index) in course.learning_outcomes" :key="index" 
                                         class="flex items-center space-x-2 rtl:space-x-reverse p-3 bg-gray-50 rounded-lg">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                        <span class="text-sm text-gray-700">{{ outcome }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Actions -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200 mb-6">
                            <div class="text-sm text-gray-500">
                                {{ currentLocale === 'en' ? 'Created' : 'تم الإنشاء' }}: {{ new Date(course.created_at).toLocaleDateString(currentLocale === 'ar' ? 'ar-SA-u-ca-gregory' : 'en-US') }}
                            </div>
                            <div class="flex space-x-3 rtl:space-x-reverse">
                                <Link :href="route('teacher.courses.show', course.id)"
                                      class="px-4 py-2 text-sm font-medium text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ t('view_course') }}
                                </Link>
                                <a :href="`/admin/courses/${course.id}/edit`" 
                                   class="px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                    {{ t('edit_course') }}
                                </a>
                            </div>
                        </div>

                        <!-- Course Schedules -->
                        <div v-if="course.schedules && course.schedules.length > 0" class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-4">{{ currentLocale === 'en' ? 'Course Schedule' : 'جدول الكورس' }}:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div v-for="schedule in course.schedules" :key="schedule.id" 
                                     class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-blue-900">{{ schedule.day }}</p>
                                            <p class="text-sm text-blue-700">{{ schedule.start_time }} - {{ schedule.end_time }}</p>
                                        </div>
                                    </div>
                                    <span :class="`px-3 py-1 text-xs rounded-full ${schedule.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`">
                                        {{ schedule.is_active ? (currentLocale === 'en' ? 'Active' : 'نشط') : (currentLocale === 'en' ? 'Inactive' : 'غير نشط') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Next Session -->
                        <div v-if="course.nextSchedule" class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-700 mb-4">{{ currentLocale === 'en' ? 'Next Session' : 'الموعد التالي' }}:</h4>
                            <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-lg font-semibold text-blue-900">
                                                {{ course.nextSchedule.day }} {{ currentLocale === 'ar' ? 'الساعة' : 'at' }} {{ course.nextSchedule.time }}
                                            </p>
                                            <p class="text-sm text-blue-700">
                                                {{ course.nextSchedule.nextOccurrence }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Active Meeting Info -->
                                    <div v-if="course.hasActiveMeeting && course.activeMeeting" class="mb-4 p-4 bg-green-50 rounded-lg border border-green-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-600 text-white">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-green-900">{{ t('meeting_active') }}</p>
                                                    <p class="text-xs text-green-700">{{ t('meeting_topic') }}: {{ course.activeMeeting.topic }}</p>
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                <svg class="w-2 h-2 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="4" />
                                                </svg>
                                                {{ currentLocale === 'ar' ? 'نشط' : 'Live' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Meeting Control Buttons -->
                                    <div class="flex items-center justify-end space-x-3 rtl:space-x-reverse">
                                        <!-- End Meeting Button -->
                                        <button
                                            v-if="course.hasActiveMeeting"
                                            @click="endMeeting(course.id)"
                                            class="px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors flex items-center space-x-2 rtl:space-x-reverse"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                            </svg>
                                            <span>{{ t('end_meeting') }}</span>
                                        </button>
                                        
                                        <!-- Start Meeting Button -->
                                        <button
                                            v-if="!course.hasActiveMeeting && currentUser && currentUser.zoom_account_id"
                                            @click="startInstantMeeting(course)"
                                            class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors flex items-center space-x-2 rtl:space-x-reverse"
                                        >
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            <span>{{ t('start_meeting') }}</span>
                                        </button>
                                        
                                        <!-- Debug Info -->
                                        <div v-if="!course.hasActiveMeeting && (!currentUser || !currentUser.zoom_account_id)" class="text-sm text-gray-500">
                                            {{ currentLocale === 'ar' ? 'لا يوجد حساب Zoom مرتبط' : 'No Zoom account linked' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Start Meeting Modal -->
        <div v-if="showStartMeetingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ currentLocale === 'ar' ? 'ابدأ اجتماع Zoom' : 'Start Zoom Meeting' }}
                    </h3>
                    
                    <form @submit.prevent="submitMeeting">
                        <!-- Course Info -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ currentLocale === 'ar' ? 'الكورس' : 'Course' }}
                            </label>
                            <div class="px-3 py-2 bg-gray-100 rounded-md text-gray-700">
                                {{ selectedCourse?.title_ar || selectedCourse?.title }}
                            </div>
                        </div>

                        <!-- Topic -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ currentLocale === 'ar' ? 'عنوان الاجتماع' : 'Meeting Topic' }}
                            </label>
                            <input
                                v-model="meetingForm.topic"
                                type="text"
                                required
                                :placeholder="currentLocale === 'ar' ? 'أدخل عنوان الاجتماع' : 'Enter meeting topic'"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <!-- Duration -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ currentLocale === 'ar' ? 'مدة الاجتماع (دقائق)' : 'Duration (minutes)' }}
                            </label>
                            <select
                                v-model="meetingForm.duration"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="30">30 {{ currentLocale === 'ar' ? 'دقيقة' : 'minutes' }}</option>
                                <option value="45">45 {{ currentLocale === 'ar' ? 'دقيقة' : 'minutes' }}</option>
                                <option value="60">60 {{ currentLocale === 'ar' ? 'دقيقة' : 'minutes' }}</option>
                                <option value="90">90 {{ currentLocale === 'ar' ? 'دقيقة' : 'minutes' }}</option>
                                <option value="120">120 {{ currentLocale === 'ar' ? 'دقيقة' : 'minutes' }}</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end space-x-3 rtl:space-x-reverse">
                            <button
                                type="button"
                                @click="showStartMeetingModal = false"
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
                            >
                                {{ currentLocale === 'ar' ? 'إلغاء' : 'Cancel' }}
                            </button>
                            <button
                                type="submit"
                                :disabled="meetingLoading"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="meetingLoading">
                                    {{ currentLocale === 'ar' ? 'جاري البدء...' : 'Starting...' }}
                                </span>
                                <span v-else>
                                    {{ currentLocale === 'ar' ? 'ابدأ الآن' : 'Start Now' }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>
