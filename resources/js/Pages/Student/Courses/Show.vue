<script setup>
import StudentLayout from '@/Layouts/StudentLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'ar');

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
            // Assignment translations
            assignment: 'Assignment',
            view_assignment: 'View Assignment',
            download_assignment: 'Download Assignment',
            upload_solution: 'Upload Solution',
            update_solution: 'Update Solution',
            solution_file: 'Solution File',
            click_to_upload: 'Click to upload',
            max_10mb: 'Max 10MB',
            remove: 'Remove',
            cancel: 'Cancel',
            upload: 'Upload',
            update: 'Update',
            view: 'View',
            download: 'Download',
            delete: 'Delete',
            my_solution: 'My Solution',
            teacher_correction: 'Teacher Correction',
            teacher_notes: 'Teacher Notes',
            submitted_at: 'Submitted At',
            corrected_at: 'Corrected At',
            no_assignment_available: 'No assignment available',
            // Submission status
            not_submitted: 'Not Submitted',
            submitted: 'Submitted',
            corrected: 'Corrected',
        },
        ar: {
            course_details: 'تفاصيل المادة',
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
            // Assignment translations
            assignment: 'الواجب',
            view_assignment: 'عرض الواجب',
            download_assignment: 'تحميل الواجب',
            upload_solution: 'رفع الحل',
            update_solution: 'تحديث الحل',
            solution_file: 'ملف الحل',
            click_to_upload: 'اضغط للرفع',
            max_10mb: 'حد أقصى 10 ميجابايت',
            remove: 'إزالة',
            cancel: 'إلغاء',
            upload: 'رفع',
            update: 'تحديث',
            view: 'عرض',
            download: 'تحميل',
            delete: 'حذف',
            my_solution: 'حلي',
            teacher_correction: 'تصحيح المعلم',
            teacher_notes: 'ملاحظات المعلم',
            submitted_at: 'تم الإرسال في',
            corrected_at: 'تم التصحيح في',
            no_assignment_available: 'لا يوجد واجب متاح',
            // Submission status
            not_submitted: 'لم يرسل',
            submitted: 'مرسل',
            corrected: 'مصحح',
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

// === متغيرات وآليات الواجبات ===

// متغيرات الحلول
const showSubmissionModal = ref(false);
const submissionForm = ref({
    assignmentId: null,
    hasExisting: false,
    selectedFile: null
});
const submissionLoading = ref(false);

// دوال الواجبات
const viewAssignment = (assignment) => {
    window.open(`/assignments/${assignment.id}/view`, '_blank');
};

const downloadAssignment = (assignment) => {
    window.open(`/assignments/${assignment.id}/download`, '_blank');
};

const openSubmissionModal = (assignment) => {
    submissionForm.value = {
        assignmentId: assignment.id,
        hasExisting: assignment.submission && assignment.submission.status !== 'not_submitted',
        selectedFile: null
    };
    showSubmissionModal.value = true;
};

const closeSubmissionModal = () => {
    showSubmissionModal.value = false;
    submissionForm.value = {
        assignmentId: null,
        hasExisting: false,
        selectedFile: null
    };
};

const handleSubmissionFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        // تحقق من حجم الملف (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            alert(currentLocale.value === 'ar' ? 'حجم الملف أكبر من 10 ميجابايت' : 'File size is larger than 10MB');
            event.target.value = '';
            return;
        }
        
        submissionForm.value.selectedFile = file;
    }
};

const removeSubmissionFile = () => {
    submissionForm.value.selectedFile = null;
    const fileInput = document.querySelector('#submissionFileInput, input[type="file"]');
    if (fileInput) {
        fileInput.value = '';
    }
};

const submitSolution = async () => {
    if (!submissionForm.value.selectedFile) {
        alert(currentLocale.value === 'ar' ? 'يرجى اختيار ملف الحل' : 'Please select solution file');
        return;
    }

    submissionLoading.value = true;

    try {
        const formData = new FormData();
        formData.append('submission_file', submissionForm.value.selectedFile);

        const response = await fetch(`/assignments/${submissionForm.value.assignmentId}/submit`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        const data = await response.json();

        if (data.success) {
            alert(currentLocale.value === 'ar' ? 'تم رفع الحل بنجاح!' : 'Solution uploaded successfully!');
            closeSubmissionModal();
            window.location.reload(); // إعادة تحميل الصفحة لتحديث البيانات
        } else {
            const errorMsg = data.errors ? 
                Object.values(data.errors).flat().join('\n') : 
                (data.message || 'حدث خطأ أثناء رفع الحل');
            alert(errorMsg);
        }
    } catch (error) {
        console.error('Error uploading solution:', error);
        alert(currentLocale.value === 'ar' ? 'حدث خطأ أثناء رفع الحل' : 'Error uploading solution');
    } finally {
        submissionLoading.value = false;
    }
};

const viewSubmission = (submission, type) => {
    window.open(`/submissions/${type}/${submission.id}/view`, '_blank');
};

const downloadSubmission = (submission, type) => {
    window.open(`/submissions/${type}/${submission.id}/download`, '_blank');
};

const deleteSubmission = async (submission) => {
    if (!confirm(currentLocale.value === 'ar' ? 
        'هل أنت متأكد من حذف الحل؟' : 
        'Are you sure you want to delete the solution?'
    )) {
        return;
    }

    try {
        const response = await fetch(`/submissions/${submission.id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        const data = await response.json();

        if (data.success) {
            alert(currentLocale.value === 'ar' ? 'تم حذف الحل بنجاح!' : 'Solution deleted successfully!');
            window.location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء حذف الحل');
        }
    } catch (error) {
        console.error('Error deleting solution:', error);
        alert(currentLocale.value === 'ar' ? 'حدث خطأ أثناء حذف الحل' : 'Error deleting solution');
    }
};

// دوال مساعدة لحالة الحل
const getSubmissionStatusClass = (status) => {
    switch (status) {
        case 'not_submitted':
            return 'bg-gray-100 text-gray-800';
        case 'submitted':
            return 'bg-yellow-100 text-yellow-800';
        case 'corrected':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const getSubmissionStatusText = (status) => {
    return t(status);
};
</script>

<template>
    <Head :title="t('course_details')" />

    <StudentLayout>
        <!-- Page Header -->
        <div class="bg-white border-b border-gray-200 py-6 mb-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <Link :href="route('student.dashboard')" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-4 transition-colors">
                            <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            {{ t('back_to_dashboard') }}
                        </Link>
                        <h1 class="text-3xl font-bold text-gray-900">{{ t('course_details') }}</h1>
                        <h2 class="text-xl text-gray-600 mt-2">
                            {{ currentLocale === 'ar' ? course.title : course.titleEn }}
                        </h2>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ meetings.length }}</div>
                                <div class="text-sm text-gray-600">{{ currentLocale === 'ar' ? 'اجتماع' : 'Meetings' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Active Meeting Alert -->
            <div v-if="course.hasActiveMeeting && course.activeMeeting" class="mb-8">
                <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 rtl:space-x-reverse">
                            <div class="flex items-center justify-center h-10 w-10 rounded-full bg-green-100">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-green-900">{{ t('meeting_active') }}</h3>
                                <p class="text-green-700">{{ course.activeMeeting.topic }}</p>
                            </div>
                        </div>
                        <button @click="joinMeeting(course.activeMeeting.id)" 
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold">
                            {{ t('join_now') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Meetings Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        {{ t('meetings_history') }}
                    </h2>
                </div>

                <!-- Meetings List -->
                <div v-if="meetings.length > 0" class="p-6">
                    <div class="space-y-6">
                        <div v-for="meeting in meetings" :key="meeting.id" 
                             class="group bg-white rounded-lg border border-gray-200 hover:border-gray-300 transition-all duration-200 hover:shadow-md overflow-hidden">
                        
                            <!-- Meeting Header -->
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-gray-100">
                                            <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900 text-lg">{{ meeting.topic }}</h3>
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse mt-1">
                                                <span :class="`px-2 py-1 text-xs font-medium rounded-full ${meeting.status_color}`">
                                                    {{ meeting.status_text }}
                                                </span>
                                                <span v-if="meeting.password && meeting.can_join" class="text-xs text-gray-500">
                                                    {{ t('password') }}: <code class="bg-gray-100 px-1 rounded">{{ meeting.password }}</code>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <button v-if="meeting.can_join" @click="joinMeeting(meeting.id)"
                                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                        {{ t('join_now') }}
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Meeting Details -->
                            <div class="px-6 py-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="flex items-center space-x-2 rtl:space-x-reverse mb-2">
                                            <svg class="h-3 w-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-xs font-medium text-gray-600">{{ t('start_time') }}</span>
                                        </div>
                                        <div class="text-sm text-gray-900">{{ formatDateTime(meeting.actual_start_time || meeting.start_time) }}</div>
                                    </div>
                                    
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="flex items-center space-x-2 rtl:space-x-reverse mb-2">
                                            <svg class="h-3 w-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-xs font-medium text-gray-600">{{ t('end_time') }}</span>
                                        </div>
                                        <div class="text-sm text-gray-900">{{ formatDateTime(meeting.actual_end_time || meeting.end_time) }}</div>
                                    </div>
                                    
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="flex items-center space-x-2 rtl:space-x-reverse mb-2">
                                            <svg class="h-3 w-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-xs font-medium text-gray-600">{{ t('meeting_duration') }}</span>
                                        </div>
                                        <div class="text-sm text-gray-900">{{ meeting.duration }} {{ t('minutes') }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Assignment Section -->
                            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-gray-100">
                                            <svg class="h-3 w-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <h4 class="font-semibold text-gray-800">{{ t('assignment') }}</h4>
                                    </div>
                                    <div v-if="meeting.assignment && meeting.assignment.submission" class="text-xs px-3 py-1 rounded-full font-medium"
                                         :class="getSubmissionStatusClass(meeting.assignment.submission.status)">
                                        {{ getSubmissionStatusText(meeting.assignment.submission.status) }}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Assignment Info -->
                            <div v-if="meeting.assignment" class="px-6 py-4">
                                <div class="bg-white rounded-lg border border-gray-200 p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-900 text-lg mb-2">{{ meeting.assignment.title }}</h5>
                                            <p v-if="meeting.assignment.description" class="text-sm text-gray-600 mb-3">
                                                {{ meeting.assignment.description }}
                                            </p>
                                            <div class="flex items-center space-x-4 rtl:space-x-reverse text-xs text-gray-500">
                                                <div class="flex items-center space-x-1 rtl:space-x-reverse">
                                                    <svg class="h-2.5 w-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <span>{{ meeting.assignment.file_name }}</span>
                                                </div>
                                                <div class="flex items-center space-x-1 rtl:space-x-reverse">
                                                    <svg class="h-2.5 w-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>{{ formatDateTime(meeting.assignment.created_at) }}</span>
                                                </div>
                                                <div class="flex items-center space-x-1 rtl:space-x-reverse">
                                                    <svg class="h-2.5 w-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span>{{ meeting.assignment.formatted_file_size }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-2 mt-4">
                                        <button @click="viewAssignment(meeting.assignment)"
                                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                                            <svg class="w-3 h-3 inline mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            {{ t('view_assignment') }}
                                        </button>
                                        <button @click="downloadAssignment(meeting.assignment)"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                            <svg class="w-3 h-3 inline mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            {{ t('download_assignment') }}
                                        </button>
                                        
                                        <button v-if="!meeting.assignment.submission || meeting.assignment.submission.status === 'not_submitted'"
                                                @click="openSubmissionModal(meeting.assignment)"
                                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                                            <svg class="w-3 h-3 inline mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            {{ t('upload_solution') }}
                                        </button>
                                        
                                        <button v-if="meeting.assignment.submission && meeting.assignment.submission.status !== 'not_submitted'"
                                                @click="openSubmissionModal(meeting.assignment)"
                                                class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-sm font-medium">
                                            <svg class="w-3 h-3 inline mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                            </svg>
                                            {{ t('update_solution') }}
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Student Submission Info -->
                                <div v-if="meeting.assignment.submission && meeting.assignment.submission.status !== 'not_submitted'" 
                                     class="mt-6 pt-4 border-t border-gray-200">
                                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                        <h6 class="font-semibold text-gray-800 mb-3 flex items-center">
                                            <svg class="w-3 h-3 mr-2 rtl:mr-0 rtl:ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ t('my_solution') }}
                                        </h6>
                                        
                                        <!-- Submitted File -->
                                        <div class="bg-white rounded-lg border border-gray-200 p-3 mb-3">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                                    <div class="flex items-center justify-center h-6 w-6 rounded-full bg-gray-100">
                                                        <svg class="h-3 w-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ meeting.assignment.submission.submission_file_name }}</div>
                                                        <div class="text-xs text-gray-500">{{ meeting.assignment.submission.formatted_submission_file_size }}</div>
                                                    </div>
                                                </div>
                                                <div class="flex space-x-2 rtl:space-x-reverse">
                                                    <button @click="viewSubmission(meeting.assignment.submission, 'submission')"
                                                            class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                                                        {{ t('view') }}
                                                    </button>
                                                    <button @click="downloadSubmission(meeting.assignment.submission, 'submission')"
                                                            class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                                        {{ t('download') }}
                                                    </button>
                                                    <button @click="deleteSubmission(meeting.assignment.submission)"
                                                            class="px-3 py-1.5 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition-colors font-medium">
                                                        {{ t('delete') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Correction and Rating -->
                                        <div v-if="meeting.assignment.submission.status === 'corrected'" 
                                             class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                                            <div class="flex items-center justify-between mb-3">
                                                <h6 class="font-semibold text-yellow-800 flex items-center">
                                                    <svg class="w-3 h-3 mr-2 rtl:mr-0 rtl:ml-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                    </svg>
                                                    {{ t('teacher_correction') }}
                                                </h6>
                                                <div v-if="meeting.assignment.submission.rating" class="flex items-center space-x-2 rtl:space-x-reverse">
                                                    <div class="flex items-center space-x-1">
                                                        <span v-for="(filled, index) in meeting.assignment.submission.stars" :key="index"
                                                              :class="filled ? 'text-yellow-400' : 'text-gray-300'">
                                                            ⭐
                                                        </span>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-700">({{ meeting.assignment.submission.rating }}/5)</span>
                                                </div>
                                            </div>
                                            
                                            <div v-if="meeting.assignment.submission.correction_file_name" 
                                                 class="bg-white rounded-lg border border-gray-200 p-3 mb-3">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                                        <div class="flex items-center justify-center h-6 w-6 rounded-full bg-gray-100">
                                                            <svg class="h-3 w-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900">{{ meeting.assignment.submission.correction_file_name }}</div>
                                                            <div class="text-xs text-gray-500">{{ meeting.assignment.submission.formatted_correction_file_size }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="flex space-x-2 rtl:space-x-reverse">
                                                        <button @click="viewSubmission(meeting.assignment.submission, 'correction')"
                                                                class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                                                            {{ t('view') }}
                                                        </button>
                                                        <button @click="downloadSubmission(meeting.assignment.submission, 'correction')"
                                                                class="px-3 py-1.5 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 transition-colors font-medium">
                                                            {{ t('download') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div v-if="meeting.assignment.submission.teacher_notes" class="bg-white rounded-lg border border-gray-200 p-3">
                                                <div class="flex items-start space-x-2 rtl:space-x-reverse">
                                                    <svg class="h-3 w-3 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                    </svg>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-700 mb-1">{{ t('teacher_notes') }}</div>
                                                        <div class="text-sm text-gray-600">{{ meeting.assignment.submission.teacher_notes }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 pt-3 border-t border-gray-200">
                                            <div class="flex items-center justify-center space-x-4 rtl:space-x-reverse text-xs text-gray-500">
                                                <div class="flex items-center space-x-1 rtl:space-x-reverse">
                                                    <svg class="h-2 w-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>{{ t('submitted_at') }}: {{ formatDateTime(meeting.assignment.submission.submitted_at) }}</span>
                                                </div>
                                                <span v-if="meeting.assignment.submission.corrected_at" class="flex items-center space-x-1 rtl:space-x-reverse">
                                                    <svg class="h-2 w-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>{{ t('corrected_at') }}: {{ formatDateTime(meeting.assignment.submission.corrected_at) }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- No Assignment Message -->
                            <div v-else class="px-6 py-8 text-center">
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ t('no_assignment_available') }}</h3>
                                    <p class="text-gray-500">{{ currentLocale === 'ar' ? 'لم يتم إضافة واجب لهذا الاجتماع بعد' : 'No assignment has been added to this meeting yet' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- No Meetings Message -->
                <div v-else class="p-12 text-center">
                    <div class="bg-gray-50 rounded-xl p-8">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ t('no_meetings') }}</h3>
                        <p class="text-gray-500 text-lg">{{ currentLocale === 'ar' ? 'لم يتم عقد أي اجتماعات بعد' : 'No meetings have been held yet' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submission Upload Modal -->
        <div v-if="showSubmissionModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 rounded-t-lg">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        {{ submissionForm.hasExisting ? t('update_solution') : t('upload_solution') }}
                    </h3>
                </div>
                <div class="p-6">
                    
                    <form @submit.prevent="submitSolution">
                        <!-- File Upload -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('solution_file') }}</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-gray-400 transition-colors">
                                <input ref="submissionFileInput" @change="handleSubmissionFileSelect" type="file" 
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                       class="hidden" />
                                
                                <div v-if="!submissionForm.selectedFile" class="text-center">
                                    <div class="flex items-center justify-center h-12 w-12 mx-auto mb-3 rounded-full bg-gray-100">
                                        <svg class="h-6 w-6 text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                    <div class="mb-2">
                                        <button type="button" @click="$refs.submissionFileInput.click()"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                            {{ t('click_to_upload') }}
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX, JPG, PNG ({{ t('max_10mb') }})</p>
                                </div>

                                <div v-else class="text-center">
                                    <div class="flex items-center justify-center space-x-3 rtl:space-x-reverse mb-3">
                                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-green-100">
                                            <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="text-left rtl:text-right">
                                            <div class="text-sm font-medium text-gray-900">{{ submissionForm.selectedFile.name }}</div>
                                            <div class="text-xs text-gray-500">{{ (submissionForm.selectedFile.size / 1024 / 1024).toFixed(2) }} MB</div>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeSubmissionFile()"
                                            class="px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                                        {{ t('remove') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end space-x-3 rtl:space-x-reverse pt-4 border-t border-gray-200">
                            <button type="button" @click="closeSubmissionModal"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                {{ t('cancel') }}
                            </button>
                            <button type="submit" :disabled="submissionLoading || !submissionForm.selectedFile"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                                <span v-if="submissionLoading" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-3 w-3 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ currentLocale === 'ar' ? 'جاري الرفع...' : 'Uploading...' }}
                                </span>
                                <span v-else class="flex items-center">
                                    <svg class="w-3 h-3 mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    {{ submissionForm.hasExisting ? t('update') : t('upload') }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>