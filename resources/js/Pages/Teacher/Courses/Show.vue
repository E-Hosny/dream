<script setup>
import TeacherLayout from '@/Layouts/TeacherLayout.vue';
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

// بيانات الاجتماع الجديد
const showStartMeetingModal = ref(false);
const meetingForm = ref({
    course_id: props.course.id,
    topic: '',
    duration: 60
});
const meetingLoading = ref(false);

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
            start_new_meeting: 'Start New Meeting',
            join_meeting: 'Join Meeting',
            end_meeting: 'End Meeting',
            meeting_active: 'Meeting Active',
            minutes: 'Minutes',
            started: 'Started',
            ended: 'Ended',
            scheduled: 'Scheduled',
            cancel: 'Cancel',
            submit: 'Submit',
            topic_placeholder: 'Enter meeting topic...',
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
            start_new_meeting: 'ابدأ اجتماع جديد',
            join_meeting: 'انضم للاجتماع',
            end_meeting: 'إنهاء الاجتماع',
            meeting_active: 'اجتماع نشط',
            minutes: 'دقائق',
            started: 'بدأ',
            ended: 'انتهى',
            scheduled: 'مجدول',
            cancel: 'إلغاء',
            submit: 'إرسال',
            topic_placeholder: 'أدخل موضوع الاجتماع...',
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

// بدء اجتماع جديد
const startInstantMeeting = () => {
    meetingForm.value.topic = `${props.course.title} - اجتماع فوري`;
    showStartMeetingModal.value = true;
};

// إرسال بيانات الاجتماع الجديد
const submitMeeting = async () => {
    if (!meetingForm.value.topic) {
        alert(t('topic_placeholder'));
        return;
    }

    meetingLoading.value = true;

    try {
        const response = await fetch('/teacher/zoom-meetings/start-instant', {
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
const endMeeting = async () => {
    if (!confirm(currentLocale.value === 'ar' ? 'هل أنت متأكد من إنهاء الاجتماع؟' : 'Are you sure you want to end the meeting?')) {
        return;
    }

    try {
        const response = await fetch(`/teacher/courses/${props.course.id}/end-meeting`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        const data = await response.json();

        if (data.success) {
            alert(currentLocale.value === 'ar' ? 'تم إنهاء الاجتماع بنجاح!' : 'Meeting ended successfully!');
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
    <Head :title="t('course_details')" />

    <TeacherLayout>
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="route('teacher.dashboard')" class="inline-flex items-center text-blue-600 hover:text-blue-700 mb-4">
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
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">{{ t('meetings_history') }}</h2>
                    <button v-if="!course.hasActiveMeeting && user.zoom_account_id" @click="startInstantMeeting"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                        {{ t('start_new_meeting') }}
                    </button>
                </div>

                <!-- Active Meeting Alert -->
                <div v-if="course.hasActiveMeeting && course.activeMeeting" class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                            <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-600 text-white">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-green-900">{{ t('meeting_active') }}</p>
                                <p class="text-xs text-green-700">{{ course.activeMeeting.topic }}</p>
                            </div>
                        </div>
                        <button @click="endMeeting" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                            {{ t('end_meeting') }}
                        </button>
                    </div>
                </div>

                <!-- Meetings List -->
                <div v-if="meetings.length > 0" class="space-y-4">
                    <div v-for="meeting in meetings" :key="meeting.id" 
                         class="p-4 rounded-lg border border-gray-200 hover:border-blue-300 transition-all duration-200 hover:shadow-md">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-medium text-gray-900">{{ meeting.topic }}</h3>
                            <span :class="`px-3 py-1 text-xs font-medium rounded-full ${meeting.status_color}`">
                                {{ meeting.status_text }}
                            </span>
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
                        
                        <div v-if="meeting.password" class="mt-3 text-sm">
                            <span class="font-medium text-gray-700">{{ t('password') }}:</span>
                            <code class="ml-2 bg-gray-100 px-2 py-1 rounded">{{ meeting.password }}</code>
                        </div>
                    </div>
                </div>
                
                <!-- No Meetings Message -->
                <div v-else class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ t('no_meetings') }}</h3>
                    <p class="text-gray-500 mb-4">{{ currentLocale === 'ar' ? 'لم يتم عقد أي اجتماعات بعد' : 'No meetings have been held yet' }}</p>
                    <button v-if="user.zoom_account_id" @click="startInstantMeeting"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        {{ t('start_new_meeting') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Start Meeting Modal -->
        <div v-if="showStartMeetingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ t('start_new_meeting') }}</h3>
                    
                    <form @submit.prevent="submitMeeting">
                        <!-- Topic -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('meeting_topic') }}</label>
                            <input v-model="meetingForm.topic" type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   :placeholder="t('topic_placeholder')" required />
                        </div>

                        <!-- Duration -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('meeting_duration') }} ({{ t('minutes') }})</label>
                            <select v-model="meetingForm.duration"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="30">30 {{ t('minutes') }}</option>
                                <option value="60">60 {{ t('minutes') }}</option>
                                <option value="90">90 {{ t('minutes') }}</option>
                                <option value="120">120 {{ t('minutes') }}</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end space-x-3 rtl:space-x-reverse">
                            <button type="button" @click="showStartMeetingModal = false"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                                {{ t('cancel') }}
                            </button>
                            <button type="submit" :disabled="meetingLoading"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50">
                                <span v-if="meetingLoading">{{ currentLocale === 'ar' ? 'جاري البدء...' : 'Starting...' }}</span>
                                <span v-else>{{ t('submit') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>