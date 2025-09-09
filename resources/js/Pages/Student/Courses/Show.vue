<script setup>
import StudentLayout from '@/Layouts/StudentLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');

// البيانات من Controller
const props = defineProps({
    course: Object,
    meetings: Array,
    user: Object,
});

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            course_details: 'Course Details',
            back_to_dashboard: 'Back to Dashboard',
            meetings_history: 'Meetings History',
            meeting_topic: 'Meeting Topic',
            start_time: 'Start Time',
            end_time: 'End Time',
            meeting_duration: 'Duration',
            meeting_status: 'Status',
            password: 'Password',
            no_meetings: 'No meetings yet',
            join_meeting: 'Join Meeting',
            meeting_active: 'Meeting Active',
            minutes: 'Minutes',
            started: 'Started',
            ended: 'Ended',
            scheduled: 'Scheduled',
            join_now: 'Join Now',
        },
        ar: {
            course_details: 'تفاصيل الكورس',
            back_to_dashboard: 'العودة للوحة التحكم',
            meetings_history: 'تاريخ الاجتماعات',
            meeting_topic: 'موضوع الاجتماع',
            start_time: 'وقت البداية',
            end_time: 'وقت النهاية',
            meeting_duration: 'المدة',
            meeting_status: 'الحالة',
            password: 'كلمة المرور',
            no_meetings: 'لا توجد اجتماعات بعد',
            join_meeting: 'انضم للاجتماع',
            meeting_active: 'اجتماع نشط',
            minutes: 'دقائق',
            started: 'بدأ',
            ended: 'انتهى',
            scheduled: 'مجدول',
            join_now: 'انضم الآن',
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

// دالة تنسيق التاريخ والوقت
const formatDateTime = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const options = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        timeZone: 'Asia/Riyadh'
    };
    return date.toLocaleString(currentLocale.value === 'ar' ? 'ar-SA' : 'en-US', options);
};

// الانضمام للاجتماع
const joinMeeting = async (meetingId) => {
    try {
        console.log(`Attempting to join meeting: ${meetingId}`);
        
        const response = await fetch(`/student/meetings/${meetingId}/guest-join`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        const data = await response.json();

        if (data.success) {
            console.log('Successfully generated guest join URL, opening Zoom...');
            window.open(data.guest_join_url, '_blank');
        } else {
            console.error('Failed to generate guest join URL:', data.message);
            alert(data.message || 'حدث خطأ أثناء الانضمام للاجتماع');
        }
    } catch (error) {
        console.error('Error joining meeting:', error);
        alert('حدث خطأ أثناء الانضمام للاجتماع');
    }
};
</script>

<template>
    <Head :title="t('course_details')" />

    <StudentLayout>
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="route('student.dashboard')" class="inline-flex items-center text-blue-600 hover:text-blue-700 mb-4">
                        <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ t('back_to_dashboard') }}
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900">{{ t('course_details') }}</h1>
                </div>
            </div>
        </div>

        <!-- Course Title -->
        <div class="max-w-6xl mx-auto mb-6">
            <h2 class="text-2xl font-bold text-gray-900">
                {{ currentLocale === 'ar' ? course.title : course.titleEn }}
            </h2>
        </div>

        <!-- Meetings History -->
        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ t('meetings_history') }}</h2>

                <!-- Active Meeting Alert -->
                <div v-if="course.hasActiveMeeting && course.activeMeeting" class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                            <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-600 text-white">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-green-900">{{ t('meeting_active') }}</p>
                                <p class="text-xs text-green-700">{{ course.activeMeeting.topic }}</p>
                            </div>
                        </div>
                        <button @click="joinMeeting(course.activeMeeting.id)" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                            {{ t('join_now') }}
                        </button>
                    </div>
                </div>

                <!-- Meetings List -->
                <div v-if="meetings.length > 0" class="space-y-4">
                    <div v-for="meeting in meetings" :key="meeting.id" 
                         class="p-4 rounded-lg border border-gray-200 hover:border-blue-300 transition-all duration-200 hover:shadow-md">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-medium text-gray-900">{{ meeting.topic }}</h3>
                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                <span :class="`px-3 py-1 text-xs font-medium rounded-full ${meeting.status_color}`">
                                    {{ meeting.status_text }}
                                </span>
                                <button v-if="meeting.can_join" @click="joinMeeting(meeting.id)"
                                        class="px-3 py-1 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 transition-colors">
                                    {{ t('join_now') }}
                                </button>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-gray-700">
                            <div>
                                <span class="font-medium">{{ t('start_time') }}:</span>
                                <br>{{ formatDateTime(meeting.start_time) }}
                            </div>
                            <div>
                                <span class="font-medium">{{ t('end_time') }}:</span>
                                <br>{{ formatDateTime(meeting.end_time) }}
                            </div>
                            <div>
                                <span class="font-medium">{{ t('meeting_duration') }}:</span>
                                <br>{{ meeting.duration }} {{ t('minutes') }}
                            </div>
                        </div>
                        
                        <div v-if="meeting.password && meeting.can_join" class="mt-3 text-sm">
                            <span class="font-medium text-gray-700">{{ t('password') }}:</span>
                            <code class="ml-2 bg-gray-100 px-2 py-1 rounded">{{ meeting.password }}</code>
                        </div>
                    </div>
                </div>
                
                <!-- No Meetings Message -->
                <div v-else class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ t('no_meetings') }}</h3>
                    <p class="text-gray-500">{{ currentLocale === 'ar' ? 'لم يتم عقد أي اجتماعات بعد' : 'No meetings have been held yet' }}</p>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>