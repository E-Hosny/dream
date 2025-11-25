<script setup>
import StudentLayout from '@/Layouts/StudentLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');

const props = defineProps({
    assignments: Object,
    courses: Array,
    filters: Object,
    stats: Object,
});

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            all_assignments: 'All Assignments',
            my_assignments: 'My Assignments',
            filter_by_course: 'Filter by Course',
            filter_by_status: 'Filter by Status',
            all_courses: 'All Courses',
            all_statuses: 'All Statuses',
            submitted: 'Submitted',
            not_submitted: 'Not Submitted',
            graded: 'Graded',
            clear_filters: 'Clear Filters',
            total_assignments: 'Total Assignments',
            submitted_assignments: 'Submitted',
            graded_assignments: 'Graded',
            pending_assignments: 'Pending',
            assignment_title: 'Assignment Title',
            course: 'Course',
            teacher: 'Teacher',
            created_at: 'Created',
            status: 'Status',
            actions: 'Actions',
            view_assignment: 'View Assignment',
            download_assignment: 'Download',
            upload_solution: 'Upload Solution',
            view_solution: 'View Solution',
            view_correction: 'View Correction',
            rating: 'Rating',
            no_assignments: 'No Assignments',
            no_assignments_message: 'No assignments found matching your criteria',
            go_to_course: 'Go to Course',
            teacher_notes: 'Teacher Notes',
            corrected_at: 'Corrected',
            submitted_at: 'Submitted',
            stars: 'stars',
        },
        ar: {
            all_assignments: 'جميع الواجبات',
            my_assignments: 'واجباتي',
            filter_by_course: 'فلترة حسب المادة',
            filter_by_status: 'فلترة حسب الحالة',
            all_courses: 'جميع المواد',
            all_statuses: 'جميع الحالات',
            submitted: 'تم التسليم',
            not_submitted: 'لم يتم التسليم',
            graded: 'تم التصحيح',
            clear_filters: 'مسح الفلاتر',
            total_assignments: 'إجمالي الواجبات',
            submitted_assignments: 'تم التسليم',
            graded_assignments: 'تم التصحيح',
            pending_assignments: 'قيد الانتظار',
            assignment_title: 'عنوان الواجب',
            course: 'المادة',
            teacher: 'المعلم',
            created_at: 'تاريخ الإضافة',
            status: 'الحالة',
            actions: 'الإجراءات',
            view_assignment: 'عرض الواجب',
            download_assignment: 'تحميل',
            upload_solution: 'رفع الحل',
            view_solution: 'عرض الحل',
            view_correction: 'عرض التصحيح',
            rating: 'التقييم',
            no_assignments: 'لا توجد واجبات',
            no_assignments_message: 'لا توجد واجبات تطابق معايير البحث',
            go_to_course: 'الذهاب للمادة',
            teacher_notes: 'ملاحظات المعلم',
            corrected_at: 'تاريخ التصحيح',
            submitted_at: 'تاريخ التسليم',
            stars: 'نجوم',
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

// Filters
const selectedCourse = ref(props.filters?.course_id || 'all');
const selectedStatus = ref(props.filters?.status || 'all');

const applyFilters = () => {
    router.get(route('student.assignments.index'), {
        course_id: selectedCourse.value !== 'all' ? selectedCourse.value : null,
        status: selectedStatus.value !== 'all' ? selectedStatus.value : null,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    selectedCourse.value = 'all';
    selectedStatus.value = 'all';
    router.get(route('student.assignments.index'));
};

// Assignment actions
const viewAssignment = (assignment) => {
    window.open(`/assignments/${assignment.id}/view`, '_blank');
};

const downloadAssignment = (assignment) => {
    window.open(`/assignments/${assignment.id}/download`, '_blank');
};

const goToCourse = (courseId) => {
    router.visit(route('student.courses.show', courseId));
};

const getStatusBadgeClass = (assignment) => {
    if (!assignment.submission) {
        return 'bg-yellow-100 text-yellow-800';
    }
    if (assignment.submission.rating !== null) {
        return 'bg-green-100 text-green-800';
    }
    return 'bg-brand/10 text-brand-dark';
};

const getStatusText = (assignment) => {
    if (!assignment.submission) {
        return t('not_submitted');
    }
    if (assignment.submission.rating !== null) {
        return t('graded');
    }
    return t('submitted');
};
</script>

<template>
    <Head :title="t('my_assignments')" />

    <StudentLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">{{ t('my_assignments') }}</h1>
                    <p class="mt-2 text-sm text-gray-600">{{ t('all_assignments') }}</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-brand rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">{{ t('total_assignments') }}</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.total }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">{{ t('pending_assignments') }}</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.pending }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">{{ t('submitted_assignments') }}</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.submitted }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">{{ t('graded_assignments') }}</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ stats.graded }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('filter_by_course') }}
                            </label>
                            <select 
                                v-model="selectedCourse"
                                @change="applyFilters"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand"
                            >
                                <option value="all">{{ t('all_courses') }}</option>
                                <option v-for="course in courses" :key="course.id" :value="course.id">
                                    {{ currentLocale === 'ar' ? course.title_ar : course.title }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('filter_by_status') }}
                            </label>
                            <select 
                                v-model="selectedStatus"
                                @change="applyFilters"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand"
                            >
                                <option value="all">{{ t('all_statuses') }}</option>
                                <option value="not_submitted">{{ t('not_submitted') }}</option>
                                <option value="submitted">{{ t('submitted') }}</option>
                                <option value="graded">{{ t('graded') }}</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button
                                @click="clearFilters"
                                class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors"
                            >
                                {{ t('clear_filters') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Assignments Grid -->
                <div v-if="assignments.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div 
                        v-for="assignment in assignments.data" 
                        :key="assignment.id"
                        class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200"
                    >
                        <div class="p-6">
                            <!-- Course Badge -->
                            <div class="flex items-center justify-between mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-brand/10 text-brand-dark">
                                    {{ currentLocale === 'ar' ? assignment.course.title_ar : assignment.course.title }}
                                </span>
                                <span 
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="getStatusBadgeClass(assignment)"
                                >
                                    {{ getStatusText(assignment) }}
                                </span>
                            </div>

                            <!-- Assignment Title -->
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                {{ assignment.title }}
                            </h3>

                            <!-- Description -->
                            <p v-if="assignment.description" class="text-sm text-gray-600 mb-4 line-clamp-2">
                                {{ assignment.description }}
                            </p>

                            <!-- Meta Info -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ assignment.teacher.name }}
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ assignment.created_at_human }}
                                </div>
                            </div>

                            <!-- Submission Info -->
                            <div v-if="assignment.submission" class="mb-4 p-3 bg-gray-50 rounded-lg">
                                <div class="text-xs text-gray-600 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span>{{ t('submitted_at') }}:</span>
                                        <span class="font-medium">{{ assignment.submission.submitted_at_human }}</span>
                                    </div>
                                    <div v-if="assignment.submission.rating" class="flex items-center justify-between">
                                        <span>{{ t('rating') }}:</span>
                                        <span class="font-medium text-yellow-600">
                                            {{ '⭐'.repeat(assignment.submission.rating) }} ({{ assignment.submission.rating }}/5)
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-wrap gap-2">
                                <button
                                    @click="viewAssignment(assignment)"
                                    class="flex-1 px-3 py-2 bg-brand text-white text-sm rounded-md hover:bg-brand-dark transition-colors"
                                >
                                    {{ t('view_assignment') }}
                                </button>
                                <button
                                    @click="goToCourse(assignment.course.id)"
                                    class="px-3 py-2 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300 transition-colors"
                                >
                                    {{ t('go_to_course') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ t('no_assignments') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ t('no_assignments_message') }}</p>
                </div>

                <!-- Pagination -->
                <div v-if="assignments.data.length > 0" class="mt-6 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        {{ currentLocale === 'ar' ? 
                            `عرض ${assignments.from} إلى ${assignments.to} من ${assignments.total} واجب` :
                            `Showing ${assignments.from} to ${assignments.to} of ${assignments.total} assignments`
                        }}
                    </div>
                    <div class="flex space-x-2" :class="currentLocale === 'ar' ? 'space-x-reverse' : ''">
                        <component
                            v-for="(link, index) in assignments.links"
                            :key="index"
                            :is="link.url ? Link : 'span'"
                            :href="link.url"
                            :class="[
                                'px-3 py-2 text-sm rounded-md',
                                link.active 
                                    ? 'bg-brand text-white' 
                                    : 'bg-white text-gray-700 hover:bg-gray-50',
                                !link.url ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

