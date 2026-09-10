<script setup>
import TeacherLayout from '@/Layouts/TeacherLayout.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'ar');
const user = computed(() => page.props.auth.user);
const isAdmin = computed(() => user.value?.roles?.includes('admin') || false);
const Layout = computed(() => isAdmin.value ? AdminLayout : TeacherLayout);

// البيانات من Controller
const props = defineProps({
    assignment: Object,
    submissions: Array,
    stats: Object,
});

// متغيرات التصحيح
const showCorrectionModal = ref(false);
const correctionForm = ref({
    submissionId: null,
    selectedFiles: [],
    existingFiles: [],
    removeFileIds: [],
    rating: 0,
    notes: ''
});
const correctionLoading = ref(false);

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            course_details: 'Course Details',
            back_to_assignment: 'Back to Course',
            assignment_submissions: 'Assignment Submissions',
            student_name: 'Student Name',
            submitted_at: 'Submitted At',
            corrected_at: 'Corrected At',
            status: 'Status',
            actions: 'Actions',
            not_submitted: 'Not Submitted',
            submitted: 'Submitted',
            corrected: 'Corrected',
            view_submission: 'View Submission',
            download_submission: 'Download Submission',
            view_correction: 'View Correction',
            download_correction: 'Download Correction',
            correct_assignment: 'Correct Assignment',
            update_correction: 'Update Correction',
            no_submissions: 'No submissions yet',
            stats_title: 'Statistics',
            total_students: 'Total Students',
            submitted_count: 'Submitted',
            corrected_count: 'Corrected',
            // Correction modal
            correction_title: 'Assignment Correction',
            upload_correction: 'Upload Correction File',
            correction_file: 'Correction File',
            rating: 'Rating',
            rating_placeholder: 'Rate from 1 to 5 stars',
            teacher_notes: 'Teacher Notes',
            notes_placeholder: 'Add your notes...',
            click_to_upload: 'Click to upload files',
            max_10mb: 'Max 10MB each, up to 10 files',
            remove: 'Remove',
            change_file: 'Add more files',
            files: 'files',
            existing_files: 'Current files',
            new_files: 'New files',
            open_file: 'Open',
            download_file: 'Download',
            cancel: 'Cancel',
            save: 'Save',
            optional: 'Optional',
            star: 'Star',
            stars: 'Stars',
        },
        ar: {
            course_details: 'تفاصيل الكورس',
            back_to_assignment: 'العودة للكورس',
            assignment_submissions: 'حلول الواجب',
            student_name: 'اسم الطالب',
            submitted_at: 'تاريخ الإرسال',
            corrected_at: 'تاريخ التصحيح',
            status: 'الحالة',
            actions: 'الإجراءات',
            not_submitted: 'لم يرسل',
            submitted: 'مرسل',
            corrected: 'مصحح',
            view_submission: 'عرض الحل',
            download_submission: 'تحميل الحل',
            view_correction: 'عرض التصحيح',
            download_correction: 'تحميل التصحيح',
            correct_assignment: 'تصحيح الواجب',
            update_correction: 'تحديث التصحيح',
            no_submissions: 'لا توجد حلول بعد',
            stats_title: 'الإحصائيات',
            total_students: 'إجمالي الطلاب',
            submitted_count: 'المرسل',
            corrected_count: 'المصحح',
            // Correction modal
            correction_title: 'تصحيح الواجب',
            upload_correction: 'رفع ملف التصحيح',
            correction_file: 'ملف التصحيح',
            rating: 'التقييم',
            rating_placeholder: 'قيم من 1 إلى 5 نجوم',
            teacher_notes: 'ملاحظات المعلم',
            notes_placeholder: 'أضف ملاحظاتك...',
            click_to_upload: 'اضغط لرفع الملفات',
            max_10mb: 'حد أقصى 10 ميجابايت لكل ملف، حتى 10 ملفات',
            remove: 'إزالة',
            change_file: 'إضافة ملفات أخرى',
            files: 'ملفات',
            existing_files: 'الملفات الحالية',
            new_files: 'ملفات جديدة',
            open_file: 'فتح',
            download_file: 'تحميل',
            cancel: 'إلغاء',
            save: 'حفظ',
            optional: 'اختياري',
            star: 'نجمة',
            stars: 'نجوم',
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

const submissionFiles = (submission) => {
    if (submission?.submission_files?.length) return submission.submission_files;
    if (submission?.submission_file_name) {
        return [{
            id: null,
            file_name: submission.submission_file_name,
            download_url: `/submissions/submission/${submission.id}/download`,
            view_url: `/submissions/submission/${submission.id}/view`,
        }];
    }
    return [];
};

const correctionFiles = (submission) => {
    if (submission?.correction_files?.length) return submission.correction_files;
    if (submission?.correction_file_name) {
        return [{
            id: null,
            file_name: submission.correction_file_name,
            download_url: `/submissions/correction/${submission.id}/download`,
            view_url: `/submissions/correction/${submission.id}/view`,
        }];
    }
    return [];
};

const openFile = (file, fallbackUrl) => {
    window.open(file.view_url || fallbackUrl, '_blank');
};

const downloadFile = (file, fallbackUrl) => {
    window.open(file.download_url || fallbackUrl, '_blank');
};

const viewSubmission = (submission) => {
    const files = submissionFiles(submission);
    if (files[0]) openFile(files[0], `/submissions/submission/${submission.id}/view`);
};

const downloadSubmission = (submission) => {
    const files = submissionFiles(submission);
    if (files[0]) downloadFile(files[0], `/submissions/submission/${submission.id}/download`);
};

const viewCorrection = (submission) => {
    const files = correctionFiles(submission);
    if (files[0]) openFile(files[0], `/submissions/correction/${submission.id}/view`);
};

const downloadCorrection = (submission) => {
    const files = correctionFiles(submission);
    if (files[0]) downloadFile(files[0], `/submissions/correction/${submission.id}/download`);
};

const openCorrectionModal = (submission) => {
    correctionForm.value = {
        submissionId: submission.id,
        selectedFiles: [],
        existingFiles: [...correctionFiles(submission)],
        removeFileIds: [],
        rating: submission.rating || 0,
        notes: submission.teacher_notes || ''
    };
    showCorrectionModal.value = true;
};

const closeCorrectionModal = () => {
    showCorrectionModal.value = false;
    correctionForm.value = {
        submissionId: null,
        selectedFiles: [],
        existingFiles: [],
        removeFileIds: [],
        rating: 0,
        notes: ''
    };
};

const handleCorrectionFileSelect = (event) => {
    const picked = Array.from(event.target.files || []);
    if (!picked.length) return;

    const next = [...correctionForm.value.selectedFiles];
    for (const file of picked) {
        if (file.size > 10 * 1024 * 1024) {
            alert(currentLocale.value === 'ar' ? `حجم الملف ${file.name} أكبر من 10 ميجابايت` : `File ${file.name} is larger than 10MB`);
            continue;
        }
        if (correctionForm.value.existingFiles.length + next.length >= 10) {
            alert(currentLocale.value === 'ar' ? 'الحد الأقصى 10 ملفات' : 'Maximum 10 files allowed');
            break;
        }
        next.push(file);
    }
    correctionForm.value.selectedFiles = next;
    event.target.value = '';
};

const removeCorrectionSelectedFile = (index) => {
    correctionForm.value.selectedFiles.splice(index, 1);
};

const removeCorrectionExistingFile = (file, index) => {
    if (file.id) correctionForm.value.removeFileIds.push(file.id);
    correctionForm.value.existingFiles.splice(index, 1);
};

const submitCorrection = async () => {
    correctionLoading.value = true;

    try {
        const formData = new FormData();

        correctionForm.value.selectedFiles.forEach((file) => {
            formData.append('correction_files[]', file);
        });
        correctionForm.value.removeFileIds.forEach((id) => {
            formData.append('remove_file_ids[]', id);
        });

        if (correctionForm.value.rating > 0) {
            formData.append('rating', correctionForm.value.rating);
        }

        if (correctionForm.value.notes) {
            formData.append('teacher_notes', correctionForm.value.notes);
        }

        const response = await fetch(`/submissions/${correctionForm.value.submissionId}/correct`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        const data = await response.json();

        if (data.success) {
            alert(currentLocale.value === 'ar' ? 'تم حفظ التصحيح بنجاح!' : 'Correction saved successfully!');
            closeCorrectionModal();
            window.location.reload();
        } else {
            const errorMsg = data.errors ?
                Object.values(data.errors).flat().join('\n') :
                (data.message || 'حدث خطأ أثناء حفظ التصحيح');
            alert(errorMsg);
        }
    } catch (error) {
        console.error('Error saving correction:', error);
        alert(currentLocale.value === 'ar' ? 'حدث خطأ أثناء حفظ التصحيح' : 'Error saving correction');
    } finally {
        correctionLoading.value = false;
    }
};

// دوال التقييم بالنجوم
const setRating = (rating) => {
    correctionForm.value.rating = rating;
};

const getStatusClass = (status) => {
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

const renderStars = (stars) => {
    return stars.map((filled, index) => filled ? '⭐' : '☆').join('');
};
</script>

<template>
    <Head :title="t('assignment_submissions')" />

    <component :is="Layout">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="isAdmin ? `/admin/courses/${assignment.meeting.course.id}/meetings` : `/teacher/courses/${assignment.meeting.course.id}`" 
                          class="inline-flex items-center text-blue-600 hover:text-blue-700 mb-4">
                        <svg class="w-4 h-4 mr-2 rtl:ml-2 rtl:mr-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        {{ t('back_to_assignment') }}
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ t('assignment_submissions') }}</h1>
                    <div class="text-gray-600">
                        <div class="font-medium">{{ assignment.title }}</div>
                        <div class="text-sm">{{ assignment.meeting.course.title }}</div>
                        <div class="text-sm">{{ assignment.meeting.topic }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-blue-100 text-blue-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-3.5l-7 7-1.5-1.5"></path>
                        </svg>
                    </div>
                    <div class="ml-4 rtl:mr-4 rtl:ml-0">
                        <p class="text-sm font-medium text-gray-600">{{ t('total_students') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ stats.total_students }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-yellow-100 text-yellow-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <div class="ml-4 rtl:mr-4 rtl:ml-0">
                        <p class="text-sm font-medium text-gray-600">{{ t('submitted_count') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ stats.submitted_count }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-green-100 text-green-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4 rtl:mr-4 rtl:ml-0">
                        <p class="text-sm font-medium text-gray-600">{{ t('corrected_count') }}</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ stats.corrected_count }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submissions Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">{{ t('assignment_submissions') }}</h2>
            </div>

            <div v-if="submissions.length > 0">
                <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ t('student_name') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ t('submitted_at') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ t('status') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ t('rating') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ t('actions') }}
                                </th>
                            </tr>
                        </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="submission in submissions" :key="submission.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ submission.student.name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4 rtl:mr-4 rtl:ml-0">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ submission.student.name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ submission.student.email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ submission.submitted_at ? formatDateTime(submission.submitted_at) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="`px-2 py-1 text-xs font-medium rounded-full ${getStatusClass(submission.status)}`">
                                    {{ t(submission.status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div v-if="submission.rating" class="flex items-center">
                                    <span class="text-yellow-400">{{ renderStars(submission.stars) }}</span>
                                    <span class="ml-1 text-gray-600">({{ submission.rating }}/5)</span>
                                </div>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex flex-wrap gap-1">
                                    <template v-if="submissionFiles(submission).length">
                                        <span class="text-xs text-gray-500">{{ submissionFiles(submission).length }} {{ t('files') }}</span>
                                        <span class="text-gray-300">|</span>
                                        <button @click="viewSubmission(submission)"
                                                class="text-indigo-600 hover:text-indigo-900 text-xs">
                                            {{ t('view_submission') }}
                                        </button>
                                        <span class="text-gray-300">|</span>
                                        <button @click="downloadSubmission(submission)"
                                                class="text-blue-600 hover:text-blue-900 text-xs">
                                            {{ t('download_submission') }}
                                        </button>
                                        <span class="text-gray-300">|</span>
                                    </template>
                                    
                                    <template v-if="correctionFiles(submission).length">
                                        <button @click="viewCorrection(submission)"
                                                class="text-brand hover:text-brand-dark text-xs">
                                            {{ t('view_correction') }}
                                        </button>
                                        <span class="text-gray-300">|</span>
                                        <button @click="downloadCorrection(submission)"
                                                class="text-green-600 hover:text-green-900 text-xs">
                                            {{ t('download_correction') }}
                                        </button>
                                        <span class="text-gray-300">|</span>
                                    </template>
                                    
                                    <button v-if="submission.submitted_at" 
                                            @click="openCorrectionModal(submission)"
                                            class="text-purple-600 hover:text-purple-900 text-xs">
                                        {{ submission.corrected_at ? t('update_correction') : t('correct_assignment') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
                
                <!-- Mobile Cards -->
                <div class="lg:hidden divide-y divide-gray-200">
                    <div v-for="submission in submissions" :key="submission.id" class="p-4 sm:p-6 space-y-4">
                        <!-- Student Info -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ submission.student.name.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4 rtl:mr-4 rtl:ml-0">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ submission.student.name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ submission.student.email }}
                                    </div>
                                </div>
                            </div>
                            <span :class="`px-2 py-1 text-xs font-medium rounded-full ${getStatusClass(submission.status)}`">
                                {{ t(submission.status) }}
                            </span>
                        </div>

                        <!-- Submission Details -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ t('submitted_at') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ submission.submitted_at ? formatDateTime(submission.submitted_at) : '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ t('rating') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <div v-if="submission.rating" class="flex items-center">
                                        <span class="text-yellow-400">{{ renderStars(submission.stars) }}</span>
                                        <span class="ml-1 text-gray-600 text-xs">({{ submission.rating }}/5)</span>
                                    </div>
                                    <span v-else class="text-gray-400">-</span>
                                </dd>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-200">
                            <template v-if="submissionFiles(submission).length">
                                <button @click="viewSubmission(submission)"
                                        class="px-3 py-1 bg-indigo-100 text-indigo-700 text-xs rounded-md hover:bg-indigo-200">
                                    {{ t('view_submission') }}
                                </button>
                                <button @click="downloadSubmission(submission)"
                                        class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-md hover:bg-blue-200">
                                    {{ t('download_submission') }}
                                </button>
                            </template>
                            
                            <template v-if="correctionFiles(submission).length">
                                <button @click="viewCorrection(submission)"
                                        class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-md hover:bg-green-200">
                                    {{ t('view_correction') }}
                                </button>
                                <button @click="downloadCorrection(submission)"
                                        class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-md hover:bg-green-200">
                                    {{ t('download_correction') }}
                                </button>
                            </template>
                            
                            <button v-if="submission.submitted_at" 
                                    @click="openCorrectionModal(submission)"
                                    class="px-3 py-1 bg-purple-100 text-purple-700 text-xs rounded-md hover:bg-purple-200">
                                {{ submission.corrected_at ? t('update_correction') : t('correct_assignment') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Submissions Message -->
            <div v-else class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ t('no_submissions') }}</h3>
                <p class="text-gray-500">{{ currentLocale === 'ar' ? 'لم يقم أي طالب بإرسال حل بعد' : 'No students have submitted solutions yet' }}</p>
            </div>
        </div>

        <!-- Correction Modal -->
        <div v-if="showCorrectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-4 sm:top-20 mx-4 sm:mx-auto p-4 sm:p-5 border w-full sm:w-96 shadow-lg rounded-md bg-white max-h-[95vh] sm:max-h-[90vh] overflow-y-auto">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ t('correction_title') }}</h3>
                    
                    <form @submit.prevent="submitCorrection">
                        <!-- Rating -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('rating') }} ({{ t('optional') }})
                            </label>
                            <div class="flex items-center space-x-1 rtl:space-x-reverse">
                                <button v-for="star in 5" :key="star" type="button" 
                                        @click="setRating(star)"
                                        class="text-2xl focus:outline-none hover:scale-110 transition-transform"
                                        :class="star <= correctionForm.rating ? 'text-yellow-400' : 'text-gray-300'">
                                    ⭐
                                </button>
                                <span v-if="correctionForm.rating > 0" class="ml-2 text-sm text-gray-600">
                                    ({{ correctionForm.rating }}/5 {{ correctionForm.rating === 1 ? t('star') : t('stars') }})
                                </span>
                            </div>
                        </div>

                        <!-- Teacher Notes -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('teacher_notes') }} ({{ t('optional') }})
                            </label>
                            <textarea v-model="correctionForm.notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      :placeholder="t('notes_placeholder')"></textarea>
                        </div>

                        <!-- File Upload -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('upload_correction') }} ({{ t('optional') }})
                            </label>
                            <div class="border-dashed border-2 border-gray-300 rounded-lg p-4 space-y-3">
                                <input ref="correctionFileInput" @change="handleCorrectionFileSelect" type="file" multiple
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                       class="hidden" />

                                <div v-if="correctionForm.existingFiles.length" class="space-y-2">
                                    <p class="text-xs font-medium text-gray-600">{{ t('existing_files') }}</p>
                                    <div
                                        v-for="(file, index) in correctionForm.existingFiles"
                                        :key="file.id || ('existing-corr-' + index)"
                                        class="flex items-center justify-between gap-2 text-sm bg-green-50 border border-green-100 rounded px-2 py-1.5"
                                    >
                                        <span class="truncate">{{ file.file_name }}</span>
                                        <button type="button" class="text-red-600 text-xs shrink-0" @click="removeCorrectionExistingFile(file, index)">{{ t('remove') }}</button>
                                    </div>
                                </div>

                                <div v-if="correctionForm.selectedFiles.length" class="space-y-2">
                                    <p class="text-xs font-medium text-gray-600">{{ t('new_files') }}</p>
                                    <div
                                        v-for="(file, index) in correctionForm.selectedFiles"
                                        :key="'new-corr-' + index"
                                        class="flex items-center justify-between gap-2 text-sm bg-blue-50 border border-blue-100 rounded px-2 py-1.5"
                                    >
                                        <span class="truncate">{{ file.name }}</span>
                                        <button type="button" class="text-red-600 text-xs shrink-0" @click="removeCorrectionSelectedFile(index)">{{ t('remove') }}</button>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="button" @click="$refs.correctionFileInput.click()"
                                            class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                                        {{ correctionForm.existingFiles.length || correctionForm.selectedFiles.length ? t('change_file') : t('click_to_upload') }}
                                    </button>
                                    <p class="text-xs text-gray-500 mt-1">PDF, DOC, DOCX, JPG, PNG ({{ t('max_10mb') }})</p>
                                </div>
                            </div>
                        </div>
                        <!-- Buttons -->
                        <div class="flex items-center justify-end space-x-3 rtl:space-x-reverse">
                            <button type="button" @click="closeCorrectionModal"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                                {{ t('cancel') }}
                            </button>
                            <button type="submit" :disabled="correctionLoading"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors disabled:opacity-50">
                                <span v-if="correctionLoading">{{ currentLocale === 'ar' ? 'جاري الحفظ...' : 'Saving...' }}</span>
                                <span v-else>{{ t('save') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </component>
</template>
