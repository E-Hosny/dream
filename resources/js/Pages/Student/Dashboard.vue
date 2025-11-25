<script setup>
import StudentLayout from '@/Layouts/StudentLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');
const user = computed(() => page.props.auth.user);

// استقبال البيانات من الخادم
const enrollments = computed(() => page.props.enrollments || []);

// State للتحديث وجلب البيانات
const refreshing = ref(false);
const activeMeetingStates = ref({});
let intervalId = null;

// الانضمام للاجتماع
const joinMeeting = async (courseId) => {
    try {
        console.log(`Starting join meeting process for course ID: ${courseId}`);
        
        // البحث عن اجتماع نشط للكورس
        const response = await fetch(`/student/courses/${courseId}/active-meeting`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        const data = await response.json();
        console.log('Active meeting response:', data);

        if (data.success && data.meeting) {
            console.log(`Found active meeting: ID ${data.meeting.id}, Topic: ${data.meeting.topic}`);
            
            // إنشاء رابط انضمام للضيف
            const joinResponse = await fetch(`/student/meetings/${data.meeting.id}/guest-join`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            const joinData = await joinResponse.json();
            console.log('Guest join response:', joinData);

            if (joinData.success) {
                console.log('Successfully generated guest join URL, opening Zoom...');
                // فتح Zoom كضيف في نافذة جديدة
                window.open(joinData.guest_join_url, '_blank');
            } else {
                console.error('Failed to generate guest join URL:', joinData.message);
                alert(joinData.message || 'حدث خطأ أثناء الانضمام للاجتماع');
            }
        } else {
            console.warn('No active meeting found for course:', courseId);
            alert('لا يوجد اجتماع نشط حالياً لهذا الكورس');
        }
    } catch (error) {
        console.error('Error joining meeting:', error);
        alert('حدث خطأ أثناء الانضمام للاجتماع');
    }
};

// تحديث حالة الاجتماعات النشطة
const updateActiveMeetingStates = async () => {
    try {
        console.log('Updating active meeting states for courses...');
        for (const enrollment of enrollments.value) {
            if (enrollment.course_id) {
                console.log(`Checking active meeting for course ID: ${enrollment.course_id}`);
                const response = await fetch(`/student/courses/${enrollment.course_id}/active-meeting-status`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                const data = await response.json();
                console.log(`Course ${enrollment.course_id} active meeting status:`, data);
                if (data.success) {
                    const oldState = activeMeetingStates.value[enrollment.course_id];
                    activeMeetingStates.value[enrollment.course_id] = data.hasActiveMeeting;
                    
                    if (oldState !== data.hasActiveMeeting) {
                        console.log(`Meeting state changed for course ${enrollment.course_id}: ${oldState} → ${data.hasActiveMeeting}`);
                    }
                }
            }
        }
        console.log('Active meeting states updated:', activeMeetingStates.value);
    } catch (error) {
        console.error('Error updating active meeting states:', error);
    }
};

// تحديث الصفحة
const refreshPage = async () => {
    refreshing.value = true;
    try {
        // تحديث حالة الاجتماعات النشطة بدلاً من إعادة تحميل كامل للصفحة
        await updateActiveMeetingStates();
    } catch (error) {
        console.error('Error refreshing page:', error);
    } finally {
        refreshing.value = false;
    }
};

// فحص وجود اجتماع نشط للكورس
const checkActiveMeeting = async (courseId) => {
    try {
        const response = await fetch(`/student/courses/${courseId}/active-meeting-status`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });
        const data = await response.json();
        return data.hasActiveMeeting || false;
    } catch (error) {
        console.error('Error checking active meeting:', error);
        return false;
    }
};

// تهيئة الصفحة
onMounted(async () => {
    console.log('Student Dashboard mounted, initializing...');
    
    // تهيئة البيانات الأولية من الخادم أولاً
    enrollments.value.forEach(enrollment => {
        if (enrollment.course_id && enrollment.hasActiveMeeting !== undefined) {
            activeMeetingStates.value[enrollment.course_id] = enrollment.hasActiveMeeting;
        }
    });
    
    // ثم تحديث حالة الاجتماعات النشطة من API
    await updateActiveMeetingStates();
    
    // إعداد التحديث التلقائي كل 15 ثانية (أسرع للاستجابة)
    intervalId = setInterval(updateActiveMeetingStates, 15000);
    console.log('Auto-refresh interval set for active meetings (every 15 seconds)');
});

// تنظيف الـ interval عند إلغاء تحميل الصفحة
onUnmounted(() => {
    if (intervalId) {
        clearInterval(intervalId);
        console.log('Auto-refresh interval cleared');
    }
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
            refresh_page: 'Refresh',
            join_meeting_now: 'Join Meeting',
            meeting_available: 'Meeting Available'
        },
        ar: {
            student_dashboard: 'لوحة تحكم الطالب',
            welcome_message: 'مرحباً بك في رحلة التعلم',
            my_courses: 'الدروس',
            continue_learning: 'متابعة التعلم',
            view_course: 'عرض المادة',
            completed: 'مكتمل',
            in_progress: 'قيد التقدم',
            not_started: 'لم يبدأ',
            progress: 'التقدم',
            instructor: 'المدرب',
            last_accessed: 'آخر وصول',
            next_lesson: 'الدرس التالي',
            no_courses: 'لا توجد دورات مسجلة بعد',
            browse_courses: 'تصفح الدورات المتاحة',
            active_meetings: 'الاجتماعات النشطة',
            join_meeting: 'انضم كضيف',
            no_active_meetings: 'لا توجد اجتماعات نشطة',
            meeting_topic: 'الموضوع',
            meeting_time: 'الوقت',
            meeting_duration: 'المدة',
            meeting_password: 'كلمة المرور',
            refresh_page: 'تحديث',
            join_meeting_now: 'انضم للاجتماع',
            meeting_available: 'اجتماع متاح'
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

const getStatusColor = (status) => {
    switch (status) {
        case 'completed':
            return 'bg-green-100 text-green-800';
        case 'in_progress':
            return 'bg-brand/10 text-brand-dark';
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
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ t('student_dashboard') }}</h1>
                    <p class="text-gray-600">{{ t('welcome_message') }}</p>
                    <div class="mt-4 text-sm text-gray-500">
                        {{ currentLocale === 'en' ? 'Keep learning,' : 'واصل التعلم،' }} {{ user.name }}
                    </div>
                </div>
                <button @click="refreshPage" :disabled="refreshing" 
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center sm:justify-start">
                    <svg v-if="refreshing" class="w-4 h-4 mr-2 animate-spin inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <svg v-else class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    {{ t('refresh_page') }}
                </button>
            </div>
        </div>

        <!-- My Courses Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">{{ t('my_courses') }}</h2>
            </div>
            
            <div v-if="enrollments.length > 0" class="space-y-6">
                <div v-for="enrollment in enrollments" :key="enrollment.id" 
                     class="p-6 rounded-lg border border-gray-200 hover:border-brand/30 transition-all duration-200 hover:shadow-md">
                    
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
                        <Link :href="route('student.courses.show', enrollment.course_id)" 
                              class="px-6 py-2 bg-gradient-to-r from-brand to-brand-dark text-white rounded-lg hover:from-brand-dark hover:to-brand transition-all duration-200 shadow-md hover:shadow-lg inline-flex items-center">
                            <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            {{ t('view_course') }}
                        </Link>
                    </div>
                    
                    <!-- Next Schedule -->
                    <div v-if="enrollment.nextSchedule" class="mb-4 p-4 bg-brand/5 rounded-lg border border-brand/20">
                        <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <svg class="h-5 w-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-brand-dark">
                                    {{ currentLocale === 'ar' ? 'الموعد التالي:' : 'Next Session:' }}
                                </p>
                                <p class="text-sm text-brand">
                                    {{ enrollment.nextSchedule.day }} {{ currentLocale === 'ar' ? 'الساعة' : 'at' }} {{ enrollment.nextSchedule.time }}
                                </p>
                                <p class="text-xs text-brand-dark">
                                    {{ enrollment.nextSchedule.nextOccurrence }}
                                </p>
                                </div>
                            </div>
                            <!-- Join Meeting Button - Show if there's an active meeting -->
                            <div v-if="activeMeetingStates[enrollment.course_id]" class="flex items-center space-x-2 rtl:space-x-reverse">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    <svg class="w-2 h-2 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="4" />
                                    </svg>
                                    {{ t('meeting_available') }}
                                </span>
                                <button @click="joinMeeting(enrollment.course_id)" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                                    {{ t('join_meeting_now') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Course Schedule -->
                    <div v-if="enrollment.schedules && enrollment.schedules.length > 0" class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">{{ currentLocale === 'ar' ? 'جدول المادة:' : 'Course Schedule:' }}</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div v-for="schedule in enrollment.schedules" :key="schedule.id" 
                                 class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                    <svg class="h-4 w-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            </div>
        </div>

    </StudentLayout>
</template>
