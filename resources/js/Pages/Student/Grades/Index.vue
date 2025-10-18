<script setup>
import StudentLayout from '@/Layouts/StudentLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');

const props = defineProps({
    submissions: Object,
    courses: Array,
    filters: Object,
    stats: Object,
});

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            my_grades: 'My Grades',
            all_grades: 'All Graded Assignments',
            filter_by_course: 'Filter by Course',
            filter_by_rating: 'Filter by Rating',
            all_courses: 'All Courses',
            all_ratings: 'All Ratings',
            clear_filters: 'Clear Filters',
            total_graded: 'Total Graded',
            average_rating: 'Average Rating',
            five_stars: '5 Stars',
            four_stars: '4 Stars',
            three_stars: '3 Stars',
            two_stars: '2 Stars',
            one_star: '1 Star',
            assignment_title: 'Assignment',
            course: 'Course',
            teacher: 'Teacher',
            rating: 'Rating',
            teacher_notes: 'Teacher Notes',
            submitted_at: 'Submitted',
            corrected_at: 'Corrected',
            view_submission: 'View Submission',
            view_correction: 'View Correction',
            download_correction: 'Download Correction',
            no_grades: 'No Grades Yet',
            no_grades_message: 'No graded assignments found',
            go_to_course: 'Go to Course',
            stars: 'stars',
            excellent: 'Excellent',
            very_good: 'Very Good',
            good: 'Good',
            fair: 'Fair',
            needs_improvement: 'Needs Improvement',
        },
        ar: {
            my_grades: 'درجاتي',
            all_grades: 'جميع الواجبات المصححة',
            filter_by_course: 'فلترة حسب المادة',
            filter_by_rating: 'فلترة حسب التقييم',
            all_courses: 'جميع المواد',
            all_ratings: 'جميع التقييمات',
            clear_filters: 'مسح الفلاتر',
            total_graded: 'إجمالي المصحح',
            average_rating: 'متوسط التقييم',
            five_stars: '5 نجوم',
            four_stars: '4 نجوم',
            three_stars: '3 نجوم',
            two_stars: 'نجمتان',
            one_star: 'نجمة واحدة',
            assignment_title: 'الواجب',
            course: 'المادة',
            teacher: 'المعلم',
            rating: 'التقييم',
            teacher_notes: 'ملاحظات المعلم',
            submitted_at: 'تاريخ التسليم',
            corrected_at: 'تاريخ التصحيح',
            view_submission: 'عرض الحل',
            view_correction: 'عرض التصحيح',
            download_correction: 'تحميل التصحيح',
            no_grades: 'لا توجد درجات',
            no_grades_message: 'لا توجد واجبات مصححة حتى الآن',
            go_to_course: 'الذهاب للمادة',
            stars: 'نجوم',
            excellent: 'ممتاز',
            very_good: 'جيد جداً',
            good: 'جيد',
            fair: 'مقبول',
            needs_improvement: 'يحتاج تحسين',
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

// Filters
const selectedCourse = ref(props.filters?.course_id || 'all');
const selectedRating = ref(props.filters?.rating || 'all');

const applyFilters = () => {
    router.get(route('student.grades.index'), {
        course_id: selectedCourse.value !== 'all' ? selectedCourse.value : null,
        rating: selectedRating.value !== 'all' ? selectedRating.value : null,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    selectedCourse.value = 'all';
    selectedRating.value = 'all';
    router.get(route('student.grades.index'));
};

// Helper functions
const getRatingColor = (rating) => {
    if (rating >= 5) return 'text-green-600';
    if (rating >= 4) return 'text-blue-600';
    if (rating >= 3) return 'text-yellow-600';
    if (rating >= 2) return 'text-orange-600';
    return 'text-red-600';
};

const getRatingBadgeClass = (rating) => {
    if (rating >= 5) return 'bg-green-100 text-green-800';
    if (rating >= 4) return 'bg-blue-100 text-blue-800';
    if (rating >= 3) return 'bg-yellow-100 text-yellow-800';
    if (rating >= 2) return 'bg-orange-100 text-orange-800';
    return 'bg-red-100 text-red-800';
};

const getRatingText = (rating) => {
    if (rating >= 5) return t('excellent');
    if (rating >= 4) return t('very_good');
    if (rating >= 3) return t('good');
    if (rating >= 2) return t('fair');
    return t('needs_improvement');
};

const viewSubmission = (submission) => {
    window.open(`/submissions/submission/${submission.id}/view`, '_blank');
};

const viewCorrection = (submission) => {
    window.open(`/submissions/correction/${submission.id}/view`, '_blank');
};

const goToCourse = (courseId) => {
    router.visit(route('student.courses.show', courseId));
};
</script>

<template>
    <Head :title="t('my_grades')" />

    <StudentLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">{{ t('my_grades') }}</h1>
                    <p class="mt-2 text-sm text-gray-600">{{ t('all_grades') }}</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
                    <!-- Total & Average -->
                    <div class="md:col-span-2 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium opacity-90">{{ t('total_graded') }}</p>
                                <p class="text-4xl font-bold mt-2">{{ stats.total_graded }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium opacity-90">{{ t('average_rating') }}</p>
                                <p class="text-3xl font-bold mt-2">{{ stats.average_rating }}</p>
                                <p class="text-xs opacity-75">{{ '⭐'.repeat(Math.round(stats.average_rating)) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Distribution -->
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-2xl">⭐⭐⭐⭐⭐</span>
                            <span class="text-xl font-bold text-green-600">{{ stats.five_stars }}</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-2xl">⭐⭐⭐⭐</span>
                            <span class="text-xl font-bold text-blue-600">{{ stats.four_stars }}</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-2xl">⭐⭐⭐</span>
                            <span class="text-xl font-bold text-yellow-600">{{ stats.three_stars }}</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-2xl">⭐⭐ / ⭐</span>
                            <span class="text-xl font-bold text-orange-600">{{ stats.two_stars + stats.one_star }}</span>
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
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="all">{{ t('all_courses') }}</option>
                                <option v-for="course in courses" :key="course.id" :value="course.id">
                                    {{ currentLocale === 'ar' ? course.title_ar : course.title }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ t('filter_by_rating') }}
                            </label>
                            <select 
                                v-model="selectedRating"
                                @change="applyFilters"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="all">{{ t('all_ratings') }}</option>
                                <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                                <option value="4">⭐⭐⭐⭐ (4)</option>
                                <option value="3">⭐⭐⭐ (3)</option>
                                <option value="2">⭐⭐ (2)</option>
                                <option value="1">⭐ (1)</option>
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

                <!-- Grades Grid -->
                <div v-if="submissions.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div 
                        v-for="submission in submissions.data" 
                        :key="submission.id"
                        class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-200 overflow-hidden"
                    >
                        <!-- Header with Rating -->
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-4 border-b">
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ currentLocale === 'ar' ? submission.course.title_ar : submission.course.title }}
                                </span>
                                <span 
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold"
                                    :class="getRatingBadgeClass(submission.rating)"
                                >
                                    {{ '⭐'.repeat(submission.rating) }}
                                </span>
                            </div>
                            <div class="text-center">
                                <p class="text-3xl font-bold" :class="getRatingColor(submission.rating)">
                                    {{ submission.rating }}/5
                                </p>
                                <p class="text-xs text-gray-600 mt-1">{{ getRatingText(submission.rating) }}</p>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <!-- Assignment Title -->
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                {{ submission.assignment.title }}
                            </h3>

                            <!-- Teacher Info -->
                            <div class="flex items-center text-sm text-gray-600 mb-3">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ submission.teacher.name }}
                            </div>

                            <!-- Dates -->
                            <div class="space-y-2 mb-4 text-xs text-gray-500">
                                <div class="flex items-center justify-between">
                                    <span>{{ t('submitted_at') }}:</span>
                                    <span class="font-medium">{{ submission.submitted_at_human }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>{{ t('corrected_at') }}:</span>
                                    <span class="font-medium text-green-600">{{ submission.corrected_at_human }}</span>
                                </div>
                            </div>

                            <!-- Teacher Notes -->
                            <div v-if="submission.teacher_notes" class="mb-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                <p class="text-xs font-semibold text-yellow-800 mb-1">{{ t('teacher_notes') }}:</p>
                                <p class="text-sm text-gray-700">{{ submission.teacher_notes }}</p>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-if="submission.correction_file_name"
                                    @click="viewCorrection(submission)"
                                    class="flex-1 px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 transition-colors"
                                >
                                    {{ t('view_correction') }}
                                </button>
                                <button
                                    @click="goToCourse(submission.course.id)"
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ t('no_grades') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ t('no_grades_message') }}</p>
                </div>

                <!-- Pagination -->
                <div v-if="submissions.data.length > 0" class="mt-6 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        {{ currentLocale === 'ar' ? 
                            `عرض ${submissions.from} إلى ${submissions.to} من ${submissions.total} درجة` :
                            `Showing ${submissions.from} to ${submissions.to} of ${submissions.total} grades`
                        }}
                    </div>
                    <div class="flex space-x-2" :class="currentLocale === 'ar' ? 'space-x-reverse' : ''">
                        <component
                            v-for="(link, index) in submissions.links"
                            :key="index"
                            :is="link.url ? Link : 'span'"
                            :href="link.url"
                            :class="[
                                'px-3 py-2 text-sm rounded-md',
                                link.active 
                                    ? 'bg-blue-600 text-white' 
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

