<script setup>
import StudentLayout from '@/Layouts/StudentLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');
const user = computed(() => page.props.auth.user);

// استقبال البيانات من الخادم
const enrollments = computed(() => page.props.enrollments || []);

// State للاجتماعات النشطة
const activeMeetings = ref([]);
const loadingMeetings = ref(false);

// جلب الاجتماعات النشطة
const fetchActiveMeetings = async () => {
    loadingMeetings.value = true;
    try {
        const response = await fetch('/student/active-meetings', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });
        const data = await response.json();
        if (data.success) {
            activeMeetings.value = data.meetings;
        }
    } catch (error) {
        console.error('Error fetching meetings:', error);
    } finally {
        loadingMeetings.value = false;
    }
};

// الانضمام للاجتماع
const joinMeeting = async (meeting) => {
    try {
        // إنشاء رابط انضمام للضيف
        const response = await fetch(`/student/meetings/${meeting.id}/guest-join`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        const data = await response.json();

        if (data.success) {
            // فتح Zoom كضيف في نافذة جديدة
            window.open(data.guest_join_url, '_blank');
        } else {
            alert(data.message || 'حدث خطأ أثناء الانضمام للاجتماع');
        }
    } catch (error) {
        console.error('Error joining meeting:', error);
        alert('حدث خطأ أثناء الانضمام للاجتماع');
    }
};

// جلب الاجتماعات عند تحميل الصفحة
onMounted(() => {
    fetchActiveMeetings();
});

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
            active_meetings: 'Active Meetings',
            join_meeting: 'Join as Guest',
            no_active_meetings: 'No active meetings',
            meeting_topic: 'Topic',
            meeting_time: 'Time',
            meeting_duration: 'Duration',
            meeting_password: 'Password',
            refresh_meetings: 'Refresh',
            loading_meetings: 'Loading...',
            no_active_meetings_message: 'No active meetings at the moment. Wait for your teacher to start a new meeting.',
            joining_meeting: 'Joining meeting...',
            guest_join: 'Join as Guest'
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
            active_meetings: 'الاجتماعات النشطة',
            join_meeting: 'انضم كضيف',
            no_active_meetings: 'لا توجد اجتماعات نشطة',
            meeting_topic: 'الموضوع',
            meeting_time: 'الوقت',
            meeting_duration: 'المدة',
            meeting_password: 'كلمة المرور',
            refresh_meetings: 'تحديث',
            loading_meetings: 'جاري التحميل...',
            no_active_meetings_message: 'لا توجد اجتماعات نشطة حالياً. انتظر حتى يبدأ المعلم اجتماع جديد.',
            joining_meeting: 'جاري الانضمام...',
            guest_join: 'انضم كضيف'
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
                                {{ currentLocale === 'ar' ? enrollment.title : enrollment.titleEn }}
                            </h3>
                            <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                <span class="text-sm text-gray-600">
                                    {{ currentLocale === 'en' ? 'by' : 'بواسطة' }} {{ enrollment.instructor }}
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
                    
                    <!-- Next Schedule -->
                    <div v-if="enrollment.nextSchedule" class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-blue-900">
                                    {{ currentLocale === 'ar' ? 'الموعد التالي:' : 'Next Session:' }}
                                </p>
                                <p class="text-sm text-blue-700">
                                    {{ enrollment.nextSchedule.day }} {{ currentLocale === 'ar' ? 'الساعة' : 'at' }} {{ enrollment.nextSchedule.time }}
                                </p>
                                <p class="text-xs text-blue-600">
                                    {{ enrollment.nextSchedule.nextOccurrence }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Course Schedule -->
                    <div v-if="enrollment.schedules && enrollment.schedules.length > 0" class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">{{ currentLocale === 'ar' ? 'جدول الكورس:' : 'Course Schedule:' }}</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div v-for="schedule in enrollment.schedules" :key="schedule.id" 
                                 class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                    <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ schedule.day }}</p>
                                        <p class="text-xs text-gray-600">{{ schedule.start_time }} - {{ schedule.end_time }}</p>
                                    </div>
                                </div>
                                <span :class="`px-2 py-1 text-xs rounded-full ${schedule.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`">
                                    {{ schedule.is_active ? (currentLocale === 'ar' ? 'نشط' : 'Active') : (currentLocale === 'ar' ? 'غير نشط' : 'Inactive') }}
                                </span>
                            </div>
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

        <!-- Active Meetings Section -->
        <div class="mt-12 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">{{ t('active_meetings') }}</h2>
                <button @click="fetchActiveMeetings" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm">
                    {{ t('refresh_meetings') }}
                </button>
            </div>

            <div v-if="loadingMeetings" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-blue-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <p class="text-gray-500 mt-4">{{ t('loading_meetings') }}</p>
            </div>

            <div v-else-if="activeMeetings.length > 0" class="space-y-6">
                <div v-for="meeting in activeMeetings" :key="meeting.id" 
                     class="p-6 rounded-lg border border-gray-200 hover:border-blue-300 transition-all duration-200 hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-900">{{ meeting.topic }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ meeting.course_title }}</p>
                        </div>
                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                            <!-- Status Badge -->
                            <span :class="`px-3 py-1 text-xs font-medium rounded-full ${
                                meeting.status === 'started' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'
                            }`">
                                {{ meeting.status_text }}
                            </span>
                            <!-- Join Button -->
                            <button @click="joinMeeting(meeting)" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                {{ t('join_meeting') }}
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm text-gray-700">
                        <div>
                            <span class="font-medium">{{ t('meeting_time') }}:</span>
                            <br>{{ meeting.start_time }}
                        </div>
                        <div>
                            <span class="font-medium">{{ t('meeting_duration') }}:</span>
                            <br>{{ meeting.duration }} {{ currentLocale === 'ar' ? 'دقيقة' : 'min' }}
                        </div>
                        <div>
                            <span class="font-medium">{{ t('meeting_password') }}:</span>
                            <br><code class="bg-gray-100 px-2 py-1 rounded">{{ meeting.password }}</code>
                        </div>
                        <div>
                            <span class="font-medium">{{ currentLocale === 'ar' ? 'الحالة' : 'Status' }}:</span>
                            <br>{{ meeting.status_text }}
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ t('no_active_meetings') }}</h3>
                <p class="text-gray-500 mb-4">{{ t('no_active_meetings_message') }}</p>
            </div>
        </div>
    </StudentLayout>
</template>
