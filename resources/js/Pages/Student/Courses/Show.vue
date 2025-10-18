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
                <div v-if="meetings.length > 0" class="space-y-6">
                    <div v-for="meeting in meetings" :key="meeting.id" 
                         class="p-6 rounded-lg border border-gray-200 hover:border-blue-300 transition-all duration-200 hover:shadow-md">
                        
                        <!-- Meeting Info -->
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-medium text-gray-900 text-lg">{{ meeting.topic }}</h3>
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
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-gray-700 mb-4">
                            <div>
                                <span class="font-medium">{{ t('start_time') }}:</span>
                                <br>{{ formatDateTime(meeting.actual_start_time || meeting.start_time) }}
                            </div>
                            <div>
                                <span class="font-medium">{{ t('end_time') }}:</span>
                                <br>{{ formatDateTime(meeting.actual_end_time || meeting.end_time) }}
                            </div>
                            <div>
                                <span class="font-medium">{{ t('meeting_duration') }}:</span>
                                <br>{{ meeting.duration }} {{ t('minutes') }}
                            </div>
                        </div>
                        
                        <div v-if="meeting.password && meeting.can_join" class="mb-4 text-sm">
                            <span class="font-medium text-gray-700">{{ t('password') }}:</span>
                            <code class="ml-2 bg-gray-100 px-2 py-1 rounded">{{ meeting.password }}</code>
                        </div>

                        <!-- Assignment Section -->
                        <div class="border-t pt-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-medium text-gray-800">{{ t('assignment') }}</h4>
                                <div v-if="meeting.assignment && meeting.assignment.submission" class="text-xs px-2 py-1 rounded-full"
                                     :class="getSubmissionStatusClass(meeting.assignment.submission.status)">
                                    {{ getSubmissionStatusText(meeting.assignment.submission.status) }}
                                </div>
                            </div>
                            
                            <!-- Assignment Info -->
                            <div v-if="meeting.assignment" class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center justify-between mb-3">
                                    <h5 class="font-medium text-gray-900">{{ meeting.assignment.title }}</h5>
                                    <span class="text-xs text-gray-500">{{ meeting.assignment.formatted_file_size }}</span>
                                </div>
                                
                                <p v-if="meeting.assignment.description" class="text-sm text-gray-600 mb-3">
                                    {{ meeting.assignment.description }}
                                </p>
                                
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div class="text-xs text-gray-500">
                                        <div>{{ meeting.assignment.file_name }}</div>
                                        <div>{{ formatDateTime(meeting.assignment.created_at) }}</div>
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-2">
                                        <button @click="viewAssignment(meeting.assignment)"
                                                class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-xs font-medium">
                                            {{ t('view_assignment') }}
                                        </button>
                                        <button @click="downloadAssignment(meeting.assignment)"
                                                class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-xs font-medium">
                                            {{ t('download_assignment') }}
                                        </button>
                                        
                                        <button v-if="!meeting.assignment.submission || meeting.assignment.submission.status === 'not_submitted'"
                                                @click="openSubmissionModal(meeting.assignment)"
                                                class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-xs font-medium">
                                            {{ t('upload_solution') }}
                                        </button>
                                        
                                        <button v-if="meeting.assignment.submission && meeting.assignment.submission.status !== 'not_submitted'"
                                                @click="openSubmissionModal(meeting.assignment)"
                                                class="px-3 py-1.5 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-xs font-medium">
                                            {{ t('update_solution') }}
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Student Submission Info -->
                                <div v-if="meeting.assignment.submission && meeting.assignment.submission.status !== 'not_submitted'" 
                                     class="mt-4 pt-3 border-t">
                                    <h6 class="font-medium text-gray-800 mb-2">{{ t('my_solution') }}</h6>
                                    
                                    <div class="space-y-2">
                                        <!-- Submitted File -->
                                        <div class="flex items-center justify-between bg-white p-2 rounded border">
                                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span class="text-sm text-gray-700">{{ meeting.assignment.submission.submission_file_name }}</span>
                                                <span class="text-xs text-gray-500">({{ meeting.assignment.submission.formatted_submission_file_size }})</span>
                                            </div>
                                            <div class="flex space-x-1 rtl:space-x-reverse">
                                                <button @click="viewSubmission(meeting.assignment.submission, 'submission')"
                                                        class="px-2 py-1 bg-indigo-500 text-white text-xs rounded hover:bg-indigo-600">
                                                    {{ t('view') }}
                                                </button>
                                                <button @click="downloadSubmission(meeting.assignment.submission, 'submission')"
                                                        class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                                    {{ t('download') }}
                                                </button>
                                                <button @click="deleteSubmission(meeting.assignment.submission)"
                                                        class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600">
                                                    {{ t('delete') }}
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Correction and Rating -->
                                        <div v-if="meeting.assignment.submission.status === 'corrected'" 
                                             class="bg-green-50 p-3 rounded border">
                                            <div class="flex items-center justify-between mb-2">
                                                <h6 class="font-medium text-green-800">{{ t('teacher_correction') }}</h6>
                                                <div v-if="meeting.assignment.submission.rating" class="flex items-center space-x-1">
                                                    <span v-for="(filled, index) in meeting.assignment.submission.stars" :key="index"
                                                          :class="filled ? 'text-yellow-400' : 'text-gray-300'">
                                                        ⭐
                                                    </span>
                                                    <span class="text-sm text-gray-600 ml-1">({{ meeting.assignment.submission.rating }}/5)</span>
                                                </div>
                                            </div>
                                            
                                            <div v-if="meeting.assignment.submission.correction_file_name" 
                                                 class="flex items-center justify-between bg-white p-2 rounded border mb-2">
                                                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                    <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <span class="text-sm text-gray-700">{{ meeting.assignment.submission.correction_file_name }}</span>
                                                    <span class="text-xs text-gray-500">({{ meeting.assignment.submission.formatted_correction_file_size }})</span>
                                                </div>
                                                <button @click="viewSubmission(meeting.assignment.submission, 'correction')"
                                                        class="px-2 py-1 bg-indigo-500 text-white text-xs rounded hover:bg-indigo-600">
                                                    {{ t('view') }}
                                                </button>
                                                <button @click="downloadSubmission(meeting.assignment.submission, 'correction')"
                                                        class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">
                                                    {{ t('download') }}
                                                </button>
                                            </div>
                                            
                                            <div v-if="meeting.assignment.submission.teacher_notes" class="text-sm text-gray-700">
                                                <strong>{{ t('teacher_notes') }}:</strong> {{ meeting.assignment.submission.teacher_notes }}
                                            </div>
                                        </div>
                                        
                                        <div class="text-xs text-gray-500 text-center">
                                            {{ t('submitted_at') }}: {{ formatDateTime(meeting.assignment.submission.submitted_at) }}
                                            <span v-if="meeting.assignment.submission.corrected_at">
                                                | {{ t('corrected_at') }}: {{ formatDateTime(meeting.assignment.submission.corrected_at) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- No Assignment Message -->
                            <div v-else class="text-center py-4 text-gray-500 text-sm">
                                {{ t('no_assignment_available') }}
                            </div>
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

        <!-- Submission Upload Modal -->
        <div v-if="showSubmissionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-4 sm:top-20 mx-4 sm:mx-auto p-4 sm:p-5 border w-full sm:w-96 shadow-lg rounded-md bg-white max-h-[95vh] sm:max-h-[90vh] overflow-y-auto">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ submissionForm.hasExisting ? t('update_solution') : t('upload_solution') }}
                    </h3>
                    
                    <form @submit.prevent="submitSolution">
                        <!-- File Upload -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('solution_file') }}</label>
                            <div class="border-dashed border-2 border-gray-300 rounded-lg p-4">
                                <input ref="submissionFileInput" @change="handleSubmissionFileSelect" type="file" 
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                       class="hidden" />
                                
                                <div v-if="!submissionForm.selectedFile" class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="mt-2">
                                        <button type="button" @click="$refs.submissionFileInput.click()"
                                                class="text-blue-600 hover:text-blue-500">
                                            {{ t('click_to_upload') }}
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX, JPG, PNG ({{ t('max_10mb') }})</p>
                                </div>

                                <div v-else class="text-center">
                                    <div class="flex items-center justify-center space-x-2 rtl:space-x-reverse">
                                        <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ submissionForm.selectedFile.name }}</span>
                                    </div>
                                    <button type="button" @click="removeSubmissionFile()"
                                            class="mt-2 text-red-600 hover:text-red-500 text-sm">
                                        {{ t('remove') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end space-x-3 rtl:space-x-reverse">
                            <button type="button" @click="closeSubmissionModal"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                                {{ t('cancel') }}
                            </button>
                            <button type="submit" :disabled="submissionLoading || !submissionForm.selectedFile"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors disabled:opacity-50">
                                <span v-if="submissionLoading">{{ currentLocale === 'ar' ? 'جاري الرفع...' : 'Uploading...' }}</span>
                                <span v-else>{{ submissionForm.hasExisting ? t('update') : t('upload') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>