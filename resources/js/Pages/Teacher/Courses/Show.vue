<script setup>
import TeacherLayout from '@/Layouts/TeacherLayout.vue';
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

// بيانات الاجتماع الجديد
const showStartMeetingModal = ref(false);
const meetingForm = ref({
    course_id: props.course.id,
    topic: '',
    duration: 60
});
const meetingLoading = ref(false);

// بيانات الواجبات
const showAssignmentModal = ref(false);
const assignmentForm = ref({
    id: null,
    meeting_id: null,
    title: '',
    description: '',
    selectedFiles: [],
    existingFiles: [],
    removeFileIds: [],
});
const assignmentLoading = ref(false);

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
            // Assignment translations
            assignment: 'Assignment',
            upload_assignment: 'Upload Assignment',
            edit_assignment: 'Edit Assignment',
            assignment_title: 'Assignment Title',
            assignment_title_placeholder: 'Enter assignment title...',
            description: 'Description',
            optional: 'Optional',
            assignment_description_placeholder: 'Enter assignment description...',
            assignment_file: 'Assignment Files',
            update_file: 'Add / Update Files',
            click_to_upload: 'Click to upload files',
            max_10mb: 'Max 10MB each, up to 10 files',
            remove: 'Remove',
            change_file: 'Add more files',
            files_count: 'files',
            existing_files: 'Current files',
            new_files: 'New files',
            update: 'Update',
            upload: 'Upload',
            view: 'View',
            download: 'Download',
            edit: 'Edit',
            delete: 'Delete',
            student_solutions: 'Student Solutions',
            no_assignment_uploaded: 'No assignment uploaded yet',
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
            // Assignment translations
            assignment: 'الواجب',
            upload_assignment: 'رفع واجب',
            edit_assignment: 'تعديل الواجب',
            assignment_title: 'عنوان الواجب',
            assignment_title_placeholder: 'أدخل عنوان الواجب...',
            description: 'الوصف',
            optional: 'اختياري',
            assignment_description_placeholder: 'أدخل وصف الواجب...',
            assignment_file: 'ملفات الواجب',
            update_file: 'إضافة / تحديث الملفات',
            click_to_upload: 'اضغط لرفع الملفات',
            max_10mb: 'حد أقصى 10 ميجابايت لكل ملف، حتى 10 ملفات',
            remove: 'إزالة',
            change_file: 'إضافة ملفات أخرى',
            files_count: 'ملفات',
            existing_files: 'الملفات الحالية',
            new_files: 'ملفات جديدة',
            update: 'تحديث',
            upload: 'رفع',
            view: 'عرض',
            download: 'تحميل',
            edit: 'تعديل',
            delete: 'حذف',
            student_solutions: 'حلول الطلاب',
            no_assignment_uploaded: 'لم يتم رفع واجب بعد',
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
    return date.toLocaleString(currentLocale.value === 'ar' ? 'ar-SA-u-ca-gregory' : 'en-US', options);
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

// === دوال الواجبات ===

// فتح نافذة رفع/تعديل الواجب
const openAssignmentModal = (meeting) => {
    assignmentForm.value = {
        id: meeting.assignment?.id || null,
        meeting_id: meeting.id,
        title: meeting.assignment?.title || '',
        description: meeting.assignment?.description || '',
        selectedFiles: [],
        existingFiles: [...(meeting.assignment?.files || (meeting.assignment?.file_name ? [{
            id: null,
            file_name: meeting.assignment.file_name,
            formatted_file_size: meeting.assignment.formatted_file_size,
            download_url: `/assignments/${meeting.assignment.id}/download`,
            view_url: `/assignments/${meeting.assignment.id}/view`,
        }] : []))],
        removeFileIds: [],
    };
    showAssignmentModal.value = true;
};

// إغلاق نافذة الواجب
const closeAssignmentModal = () => {
    showAssignmentModal.value = false;
    assignmentForm.value = {
        id: null,
        meeting_id: null,
        title: '',
        description: '',
        selectedFiles: [],
        existingFiles: [],
        removeFileIds: [],
    };
};

const handleFileSelect = (event) => {
    const picked = Array.from(event.target.files || []);
    if (!picked.length) return;

    const next = [...assignmentForm.value.selectedFiles];
    for (const file of picked) {
        if (file.size > 10 * 1024 * 1024) {
            alert(currentLocale.value === 'ar' ? `حجم الملف ${file.name} أكبر من 10 ميجابايت` : `File ${file.name} is larger than 10MB`);
            continue;
        }
        if (assignmentForm.value.existingFiles.length + next.length >= 10) {
            alert(currentLocale.value === 'ar' ? 'الحد الأقصى 10 ملفات' : 'Maximum 10 files allowed');
            break;
        }
        next.push(file);
    }
    assignmentForm.value.selectedFiles = next;
    event.target.value = '';
};

const removeSelectedFile = (index) => {
    assignmentForm.value.selectedFiles.splice(index, 1);
};

const removeExistingFile = (file, index) => {
    if (file.id) {
        assignmentForm.value.removeFileIds.push(file.id);
    }
    assignmentForm.value.existingFiles.splice(index, 1);
};

const submitAssignment = async () => {
    if (!assignmentForm.value.title.trim()) {
        alert(currentLocale.value === 'ar' ? 'يرجى إدخال عنوان الواجب' : 'Please enter assignment title');
        return;
    }

    const totalFiles = assignmentForm.value.existingFiles.length + assignmentForm.value.selectedFiles.length;
    if (!assignmentForm.value.id && assignmentForm.value.selectedFiles.length < 1) {
        alert(currentLocale.value === 'ar' ? 'يرجى اختيار ملف واحد على الأقل' : 'Please select at least one file');
        return;
    }
    if (assignmentForm.value.id && totalFiles < 1) {
        alert(currentLocale.value === 'ar' ? 'يجب الإبقاء على ملف واحد على الأقل' : 'Keep at least one file');
        return;
    }

    assignmentLoading.value = true;

    try {
        const formData = new FormData();
        formData.append('title', assignmentForm.value.title);
        formData.append('description', assignmentForm.value.description || '');

        assignmentForm.value.selectedFiles.forEach((file) => {
            formData.append('assignment_files[]', file);
        });

        assignmentForm.value.removeFileIds.forEach((id) => {
            formData.append('remove_file_ids[]', id);
        });

        let url, method;
        if (assignmentForm.value.id) {
            url = `/assignments/${assignmentForm.value.id}`;
            method = 'POST';
            formData.append('_method', 'PUT');
        } else {
            url = '/assignments';
            method = 'POST';
            formData.append('meeting_id', assignmentForm.value.meeting_id);
        }

        const response = await fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Response is not JSON:', text);
            throw new Error('Server returned non-JSON response');
        }

        const data = await response.json();

        if (data.success) {
            alert(currentLocale.value === 'ar' ?
                (assignmentForm.value.id ? 'تم تحديث الواجب بنجاح!' : 'تم رفع الواجب بنجاح!') :
                (assignmentForm.value.id ? 'Assignment updated successfully!' : 'Assignment uploaded successfully!')
            );
            closeAssignmentModal();
            window.location.reload();
        } else {
            const errorMsg = data.errors ?
                Object.values(data.errors).flat().join('\n') :
                (data.message || 'حدث خطأ أثناء حفظ الواجب');
            alert(errorMsg);
        }
    } catch (error) {
        console.error('Error saving assignment:', error);
        alert(currentLocale.value === 'ar' ?
            `حدث خطأ أثناء حفظ الواجب: ${error.message}` :
            `Error saving assignment: ${error.message}`
        );
    } finally {
        assignmentLoading.value = false;
    }
};

const assignmentFiles = (assignment) => {
    if (assignment?.files?.length) return assignment.files;
    if (assignment?.file_name) {
        return [{
            id: null,
            file_name: assignment.file_name,
            formatted_file_size: assignment.formatted_file_size,
            download_url: `/assignments/${assignment.id}/download`,
            view_url: `/assignments/${assignment.id}/view`,
        }];
    }
    return [];
};

const viewAssignmentFile = (file, assignment) => {
    window.open(file.view_url || `/assignments/${assignment.id}/view`, '_blank');
};

const downloadAssignmentFile = (file, assignment) => {
    window.open(file.download_url || `/assignments/${assignment.id}/download`, '_blank');
};

// عرض ملف الواجب (للتوافق)
const viewAssignment = (assignment) => {
    const files = assignmentFiles(assignment);
    if (files[0]) viewAssignmentFile(files[0], assignment);
};

const downloadAssignment = (assignment) => {
    const files = assignmentFiles(assignment);
    if (files[0]) downloadAssignmentFile(files[0], assignment);
};

// تعديل الواجب
const editAssignment = (meeting) => {
    openAssignmentModal(meeting);
};

// حذف الواجب
const deleteAssignment = async (assignment) => {
    if (!confirm(currentLocale.value === 'ar' ? 
        'هل أنت متأكد من حذف هذا الواجب؟ سيتم حذف جميع الحلول المرتبطة به أيضاً.' : 
        'Are you sure you want to delete this assignment? All related submissions will be deleted too.'
    )) {
        return;
    }

    try {
        const response = await fetch(`/assignments/${assignment.id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        const data = await response.json();

        if (data.success) {
            alert(currentLocale.value === 'ar' ? 'تم حذف الواجب بنجاح!' : 'Assignment deleted successfully!');
            window.location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء حذف الواجب');
        }
    } catch (error) {
        console.error('Error deleting assignment:', error);
        alert(currentLocale.value === 'ar' ? 'حدث خطأ أثناء حذف الواجب' : 'Error deleting assignment');
    }
};

// عرض حلول الطلاب
const viewSubmissions = (assignment) => {
    window.open(`/assignments/${assignment.id}/submissions`, '_blank');
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
                <div v-if="meetings.length > 0" class="space-y-6">
                    <div v-for="meeting in meetings" :key="meeting.id" 
                         class="p-6 rounded-lg border border-gray-200 hover:border-blue-300 transition-all duration-200 hover:shadow-md">
                        
                        <!-- Meeting Info -->
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-medium text-gray-900 text-lg">{{ meeting.topic }}</h3>
                            <span :class="`px-3 py-1 text-xs font-medium rounded-full ${meeting.status_color}`">
                                {{ meeting.status_text }}
                            </span>
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
                        
                        <div v-if="meeting.password" class="mb-4 text-sm">
                            <span class="font-medium text-gray-700">{{ t('password') }}:</span>
                            <code class="ml-2 bg-gray-100 px-2 py-1 rounded">{{ meeting.password }}</code>
                        </div>

                        <!-- Assignment Section -->
                        <div class="border-t pt-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-medium text-gray-800">{{ t('assignment') }}</h4>
                                <div class="flex space-x-2 rtl:space-x-reverse">
                                    <!-- Upload Assignment Button -->
                                    <button v-if="!meeting.assignment" @click="openAssignmentModal(meeting)"
                                            class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-xs font-medium">
                                        {{ t('upload_assignment') }}
                                    </button>
                                    
                                    <!-- Assignment Actions -->
                                    <div v-if="meeting.assignment" class="flex flex-wrap gap-2">
                                        <button @click="viewAssignment(meeting.assignment)"
                                                class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-xs font-medium">
                                            {{ t('view') }}
                                        </button>
                                        <button @click="downloadAssignment(meeting.assignment)"
                                                class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-xs font-medium">
                                            {{ t('download') }}
                                        </button>
                                        <button @click="editAssignment(meeting)"
                                                class="px-3 py-1.5 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-xs font-medium">
                                            {{ t('edit') }}
                                        </button>
                                        <button @click="deleteAssignment(meeting.assignment)"
                                                class="px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-xs font-medium">
                                            {{ t('delete') }}
                                        </button>
                                        <button @click="viewSubmissions(meeting.assignment)"
                                                class="px-3 py-1.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-xs font-medium">
                                            {{ t('student_solutions') }} ({{ meeting.assignment.submissions_count || 0 }})
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Assignment Info -->
                            <div v-if="meeting.assignment" class="bg-gray-50 p-3 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="font-medium text-gray-900">{{ meeting.assignment.title }}</h5>
                                    <span class="text-xs text-gray-500">{{ assignmentFiles(meeting.assignment).length }} {{ t('files_count') }}</span>
                                </div>
                                <p v-if="meeting.assignment.description" class="text-sm text-gray-600 mb-2">
                                    {{ meeting.assignment.description }}
                                </p>
                                <div class="space-y-2">
                                    <div
                                        v-for="(file, idx) in assignmentFiles(meeting.assignment)"
                                        :key="file.id || idx"
                                        class="flex items-center justify-between gap-2 text-xs bg-white border border-gray-200 rounded-lg px-2 py-1.5"
                                    >
                                        <span class="truncate text-gray-700">{{ file.file_name }}</span>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <span class="text-gray-400">{{ file.formatted_file_size }}</span>
                                            <button type="button" class="text-indigo-600 hover:text-indigo-800" @click="viewAssignmentFile(file, meeting.assignment)">{{ t('view') }}</button>
                                            <button type="button" class="text-blue-600 hover:text-blue-800" @click="downloadAssignmentFile(file, meeting.assignment)">{{ t('download') }}</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-2">{{ formatDateTime(meeting.assignment.created_at) }}</div>
                            </div>
                            
                            <!-- No Assignment Message -->
                            <div v-else class="text-center py-4 text-gray-500 text-sm">
                                {{ t('no_assignment_uploaded') }}
                            </div>
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

        <!-- Assignment Upload Modal -->
        <div v-if="showAssignmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ assignmentForm.id ? t('edit_assignment') : t('upload_assignment') }}
                    </h3>
                    
                    <form @submit.prevent="submitAssignment">
                        <!-- Title -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('assignment_title') }}</label>
                            <input v-model="assignmentForm.title" type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   :placeholder="t('assignment_title_placeholder')" required />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('description') }} ({{ t('optional') }})</label>
                            <textarea v-model="assignmentForm.description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      :placeholder="t('assignment_description_placeholder')"></textarea>
                        </div>

                        <!-- File Upload -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ assignmentForm.id ? t('update_file') + ' (' + t('optional') + ')' : t('assignment_file') }}
                            </label>
                            <div class="border-dashed border-2 border-gray-300 rounded-lg p-4 space-y-3">
                                <input ref="assignmentFileInput" @change="handleFileSelect" type="file" multiple
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                       class="hidden" />

                                <div v-if="assignmentForm.existingFiles.length" class="space-y-2">
                                    <p class="text-xs font-medium text-gray-600">{{ t('existing_files') }}</p>
                                    <div
                                        v-for="(file, index) in assignmentForm.existingFiles"
                                        :key="file.id || ('existing-' + index)"
                                        class="flex items-center justify-between gap-2 text-sm bg-green-50 border border-green-100 rounded px-2 py-1.5"
                                    >
                                        <span class="truncate">{{ file.file_name }}</span>
                                        <button type="button" class="text-red-600 text-xs shrink-0" @click="removeExistingFile(file, index)">{{ t('remove') }}</button>
                                    </div>
                                </div>

                                <div v-if="assignmentForm.selectedFiles.length" class="space-y-2">
                                    <p class="text-xs font-medium text-gray-600">{{ t('new_files') }}</p>
                                    <div
                                        v-for="(file, index) in assignmentForm.selectedFiles"
                                        :key="'new-' + index + '-' + file.name"
                                        class="flex items-center justify-between gap-2 text-sm bg-blue-50 border border-blue-100 rounded px-2 py-1.5"
                                    >
                                        <span class="truncate">{{ file.name }}</span>
                                        <button type="button" class="text-red-600 text-xs shrink-0" @click="removeSelectedFile(index)">{{ t('remove') }}</button>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="button" @click="$refs.assignmentFileInput.click()"
                                            class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                                        {{ assignmentForm.existingFiles.length || assignmentForm.selectedFiles.length ? t('change_file') : t('click_to_upload') }}
                                    </button>
                                    <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX, JPG, PNG ({{ t('max_10mb') }})</p>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end space-x-3 rtl:space-x-reverse">
                            <button type="button" @click="closeAssignmentModal"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                                {{ t('cancel') }}
                            </button>
                            <button type="submit" :disabled="assignmentLoading"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50">
                                <span v-if="assignmentLoading">{{ currentLocale === 'ar' ? 'جاري الحفظ...' : 'Saving...' }}</span>
                                <span v-else>{{ assignmentForm.id ? t('update') : t('upload') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </TeacherLayout>
</template>