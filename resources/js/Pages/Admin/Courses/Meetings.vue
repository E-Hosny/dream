<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'ar');

// البيانات من Controller
const props = defineProps({
    course: Object,
    meetings: Array,
});

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            course_details: 'Course Details',
            back_to_courses: 'Back to Courses',
            meetings_history: 'Meetings History',
            start_time: 'Start Time',
            end_time: 'End Time',
            meeting_duration: 'Duration',
            password: 'Password',
            no_meetings: 'No meetings yet',
            minutes: 'Minutes',
            // Assignment translations
            assignment: 'Assignment',
            view: 'View',
            download: 'Download',
            student_solutions: 'Student Solutions',
            no_assignment_uploaded: 'No assignment uploaded yet',
            delete: 'Delete',
            delete_meeting: 'Delete Meeting',
        },
        ar: {
            course_details: 'تفاصيل الكورس',
            back_to_courses: 'العودة للكورسات',
            meetings_history: 'تاريخ الاجتماعات',
            start_time: 'وقت البداية',
            end_time: 'وقت النهاية',
            meeting_duration: 'المدة',
            password: 'كلمة المرور',
            no_meetings: 'لا توجد اجتماعات بعد',
            minutes: 'دقائق',
            // Assignment translations
            assignment: 'الواجب',
            view: 'عرض',
            download: 'تحميل',
            student_solutions: 'حلول الطلاب',
            no_assignment_uploaded: 'لم يتم رفع واجب بعد',
            delete: 'حذف',
            delete_meeting: 'حذف الاجتماع',
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

// عرض ملف الواجب
const viewAssignment = (assignment) => {
    window.open(`/assignments/${assignment.id}/view`, '_blank');
};

// تحميل ملف الواجب
const downloadAssignment = (assignment) => {
    window.open(`/assignments/${assignment.id}/download`, '_blank');
};

// عرض حلول الطلاب
const viewSubmissions = (assignment) => {
    window.open(`/assignments/${assignment.id}/submissions`, '_blank');
};

// حذف الاجتماع
const deleteMeeting = async (meeting) => {
    if (!confirm(currentLocale.value === 'ar' ? 
        'هل أنت متأكد من حذف هذا الاجتماع؟ سيتم حذف جميع البيانات المرتبطة به أيضاً.' : 
        'Are you sure you want to delete this meeting? All related data will be deleted too.'
    )) {
        return;
    }

    try {
        const response = await fetch(`/admin/courses/${props.course.id}/meetings/${meeting.id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        // التحقق من نوع المحتوى قبل parse
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            if (data.success) {
                alert(currentLocale.value === 'ar' ? 'تم حذف الاجتماع بنجاح!' : 'Meeting deleted successfully!');
                window.location.reload();
            } else {
                throw new Error(data.message || 'حدث خطأ أثناء حذف الاجتماع');
            }
        } else if (response.ok) {
            // إذا كانت الاستجابة redirect (HTML) لكن status code 200
            alert(currentLocale.value === 'ar' ? 'تم حذف الاجتماع بنجاح!' : 'Meeting deleted successfully!');
            window.location.reload();
        } else {
            // محاولة قراءة JSON حتى لو كان status code خطأ
            try {
                const data = await response.json();
                throw new Error(data.message || 'حدث خطأ أثناء حذف الاجتماع');
            } catch (jsonError) {
                throw new Error(currentLocale.value === 'ar' ? 'حدث خطأ أثناء حذف الاجتماع' : 'Error deleting meeting');
            }
        }
    } catch (error) {
        console.error('Error deleting meeting:', error);
        alert(currentLocale.value === 'ar' ? 
            `حدث خطأ أثناء حذف الاجتماع: ${error.message}` : 
            `Error deleting meeting: ${error.message}`
        );
    }
};
</script>

<template>
    <Head :title="t('course_details')" />

    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="route('admin.courses.index')" class="inline-flex items-center text-blue-600 hover:text-blue-700 mb-4">
                        <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ t('back_to_courses') }}
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
                                <button @click="deleteMeeting(meeting)"
                                        class="px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-xs font-medium"
                                        :title="t('delete_meeting')">
                                    {{ t('delete') }}
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
                        
                        <div v-if="meeting.password" class="mb-4 text-sm">
                            <span class="font-medium text-gray-700">{{ t('password') }}:</span>
                            <code class="ml-2 bg-gray-100 px-2 py-1 rounded">{{ meeting.password }}</code>
                        </div>

                        <!-- Assignment Section -->
                        <div class="border-t pt-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-medium text-gray-800">{{ t('assignment') }}</h4>
                                <div class="flex space-x-2 rtl:space-x-reverse" v-if="meeting.assignment">
                                    <button @click="viewAssignment(meeting.assignment)"
                                            class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-xs font-medium">
                                        {{ t('view') }}
                                    </button>
                                    <button @click="downloadAssignment(meeting.assignment)"
                                            class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-xs font-medium">
                                        {{ t('download') }}
                                    </button>
                                    <button @click="viewSubmissions(meeting.assignment)"
                                            class="px-3 py-1.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-xs font-medium">
                                        {{ t('student_solutions') }} ({{ meeting.assignment.submissions_count || 0 }})
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Assignment Info -->
                            <div v-if="meeting.assignment" class="bg-gray-50 p-3 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="font-medium text-gray-900">{{ meeting.assignment.title }}</h5>
                                    <span class="text-xs text-gray-500">{{ meeting.assignment.formatted_file_size }}</span>
                                </div>
                                <p v-if="meeting.assignment.description" class="text-sm text-gray-600 mb-2">
                                    {{ meeting.assignment.description }}
                                </p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ meeting.assignment.file_name }}</span>
                                    <span>{{ formatDateTime(meeting.assignment.created_at) }}</span>
                                </div>
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
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

