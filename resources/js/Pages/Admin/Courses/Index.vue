<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'ar');

const props = defineProps({
    courses: Object,
    filters: Object,
    sessionStats: Object,
});

const searchForm = ref({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    level: props.filters?.level || '',
    stats_month: props.sessionStats?.month || props.filters?.stats_month || '',
});

const search = () => {
    router.get(route('admin.courses.index'), {
        search: searchForm.value.search || undefined,
        status: searchForm.value.status || undefined,
        level: searchForm.value.level || undefined,
        stats_month: searchForm.value.stats_month || undefined,
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true,
    });
};

const changeStatsMonth = () => {
    search();
};

const formatMonthOption = (monthKey) => {
    if (!monthKey) return '';
    const [year, month] = monthKey.split('-');
    const date = new Date(Number(year), Number(month) - 1, 1);
    return date.toLocaleDateString(currentLocale.value === 'ar' ? 'ar-SA-u-ca-gregory' : 'en-US', {
        year: 'numeric',
        month: 'long',
    });
};

const t = (key) => {
    const translations = {
        en: {
            courses_management: 'Courses Management',
            add_new_course: 'Add New Course',
            search_courses: 'Search courses...',
            filter_by_status: 'Filter by status',
            filter_by_level: 'Filter by level',
            all_statuses: 'All Statuses',
            all_levels: 'All Levels',
            title: 'Title',
            instructor: 'Instructor',
            status: 'Status',
            level: 'Level',
            students: 'Students',
            created_at: 'Created At',
            actions: 'Actions',
            edit: 'Edit',
            delete: 'Delete',
            view: 'View',
            no_courses: 'No courses found',
            search: 'Search',
            clear: 'Clear',
            draft: 'Draft',
            published: 'Published',
            archived: 'Archived',
            completed: 'Completed',
            beginner: 'Beginner',
            intermediate: 'Intermediate',
            advanced: 'Advanced',
            month_stats: 'Monthly sessions summary',
            filter_by_month: 'Month',
            total_sessions: 'Sessions taken',
            paid_sessions: 'Paid sessions',
            unpaid_sessions: 'Unpaid sessions',
            paid_value: 'Paid value',
            unpaid_value: 'Unpaid value',
            total_value: 'Total value',
            due_notice_sessions: 'Notified due sessions',
            prepaid_in_paid: 'prepaid',
        },
        ar: {
            courses_management: 'إدارة الكورسات',
            add_new_course: 'إضافة كورس جديد',
            search_courses: 'البحث عن الكورسات...',
            filter_by_status: 'فلترة حسب الحالة',
            filter_by_level: 'فلترة حسب المستوى',
            all_statuses: 'جميع الحالات',
            all_levels: 'جميع المستويات',
            title: 'العنوان',
            instructor: 'المدرس',
            status: 'الحالة',
            level: 'المستوى',
            students: 'الطلاب',
            created_at: 'تاريخ الإنشاء',
            actions: 'الإجراءات',
            edit: 'تعديل',
            delete: 'حذف',
            view: 'عرض',
            no_courses: 'لا يوجد كورسات',
            search: 'بحث',
            clear: 'مسح',
            draft: 'مسودة',
            published: 'منشور',
            archived: 'مؤرشف',
            completed: 'مكتمل',
            beginner: 'مبتدئ',
            intermediate: 'متوسط',
            advanced: 'متقدم',
            month_stats: 'ملخص حصص الشهر',
            filter_by_month: 'الشهر',
            total_sessions: 'حصص تم أخذها',
            paid_sessions: 'حصص مدفوعة',
            unpaid_sessions: 'حصص غير مدفوعة',
            paid_value: 'قيمة المدفوع',
            unpaid_value: 'قيمة غير المدفوع',
            total_value: 'الإجمالي',
            due_notice_sessions: 'حصص بإشعار مستحق',
            prepaid_in_paid: 'مدفوعة مقدماً',
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

const deleteCourse = (course) => {
    if (confirm(`Are you sure you want to delete ${course.title}?`)) {
        router.delete(route('admin.courses.destroy', course.id));
    }
};

const getStatusColor = (status) => {
    switch (status) {
        case 'published': return 'bg-green-100 text-green-800';
        case 'draft': return 'bg-yellow-100 text-yellow-800';
        case 'archived': return 'bg-gray-100 text-gray-800';
        case 'completed': return 'bg-blue-100 text-blue-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const getLevelColor = (level) => {
    switch (level) {
        case 'beginner': return 'bg-blue-100 text-blue-800';
        case 'intermediate': return 'bg-purple-100 text-purple-800';
        case 'advanced': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <Head :title="t('courses_management')" />

    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ t('courses_management') }}</h1>
                <p class="text-gray-600 mt-2">{{ currentLocale === 'ar' ? 'إدارة الكورسات والمحتوى التعليمي' : 'Manage courses and educational content' }}</p>
            </div>
            <Link :href="route('admin.courses.create')" class="bg-gradient-to-r from-brand to-brand-dark text-white px-6 py-3 rounded-lg hover:from-brand-dark hover:to-brand transition-all duration-200 shadow-md hover:shadow-lg">
                <svg class="w-5 h-5 inline mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ t('add_new_course') }}
            </Link>
        </div>

        <div v-if="sessionStats" class="mb-6 bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ t('month_stats') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ currentLocale === 'ar' ? sessionStats.month_label_ar : sessionStats.month_label_en }}
                    </p>
                </div>
                <div class="w-full sm:w-56">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('filter_by_month') }}</label>
                    <select
                        v-model="searchForm.stats_month"
                        class="w-full rounded-lg border-gray-300 focus:border-brand focus:ring-brand"
                        @change="changeStatsMonth"
                    >
                        <option
                            v-for="monthKey in sessionStats.month_options"
                            :key="monthKey"
                            :value="monthKey"
                        >
                            {{ formatMonthOption(monthKey) }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                    <p class="text-xs text-slate-600 mb-1">{{ t('total_sessions') }}</p>
                    <p class="text-2xl font-bold text-slate-900">{{ sessionStats.total_sessions }}</p>
                    <p class="text-sm font-semibold text-slate-700 mt-1">{{ sessionStats.total_amount_format }}</p>
                </div>
                <div class="rounded-xl bg-green-50 border border-green-200 p-4">
                    <p class="text-xs text-green-700 mb-1">{{ t('paid_sessions') }}</p>
                    <p class="text-2xl font-bold text-green-800">{{ sessionStats.paid_sessions }}</p>
                    <p class="text-sm font-semibold text-green-700 mt-1">{{ sessionStats.paid_amount_format }}</p>
                    <div
                        v-if="sessionStats.prepaid_sessions"
                        class="mt-2 inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800"
                    >
                        {{ t('prepaid_in_paid') }}: {{ sessionStats.prepaid_sessions }} ({{ sessionStats.prepaid_amount_format }})
                    </div>
                </div>
                <div class="rounded-xl bg-amber-50 border border-amber-200 p-4">
                    <p class="text-xs text-amber-700 mb-1">{{ t('unpaid_sessions') }}</p>
                    <p class="text-2xl font-bold text-amber-800">{{ sessionStats.unpaid_sessions }}</p>
                    <p class="text-sm font-semibold text-amber-700 mt-1">{{ sessionStats.unpaid_amount_format }}</p>
                    <p v-if="sessionStats.due_notice_sessions" class="text-xs text-amber-600 mt-2">
                        {{ t('due_notice_sessions') }}: {{ sessionStats.due_notice_sessions }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border-0 p-6 mb-6 backdrop-blur-sm bg-white/95">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('search') }}</label>
                    <input
                        v-model="searchForm.search"
                        type="text"
                        :placeholder="t('search_courses')"
                        class="w-full rounded-lg border-gray-300 focus:border-brand focus:ring-brand"
                        @keyup.enter="search"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('filter_by_status') }}</label>
                    <select v-model="searchForm.status" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">{{ t('all_statuses') }}</option>
                        <option value="draft">{{ t('draft') }}</option>
                        <option value="published">{{ t('published') }}</option>
                        <option value="archived">{{ t('archived') }}</option>
                        <option value="completed">{{ t('completed') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('filter_by_level') }}</label>
                    <select v-model="searchForm.level" class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">{{ t('all_levels') }}</option>
                        <option value="beginner">{{ t('beginner') }}</option>
                        <option value="intermediate">{{ t('intermediate') }}</option>
                        <option value="advanced">{{ t('advanced') }}</option>
                    </select>
                </div>
                <div class="flex items-end space-x-2 rtl:space-x-reverse">
                    <button @click="search" class="bg-brand text-white px-4 py-2 rounded-lg hover:bg-brand-dark transition-colors">
                        {{ t('search') }}
                    </button>
                    <button @click="searchForm = { search: '', status: '', level: '', stats_month: sessionStats?.month || '' }; search()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                        {{ t('clear') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border-0 overflow-hidden backdrop-blur-sm bg-white/95">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left rtl:text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ t('title') }}
                            </th>
                            <th class="px-6 py-3 text-left rtl:text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ t('instructor') }}
                            </th>
                            <th class="px-6 py-3 text-left rtl:text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ t('status') }}
                            </th>
                            <th class="px-6 py-3 text-left rtl:text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ t('level') }}
                            </th>
                            <th class="px-6 py-3 text-left rtl:text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ t('students') }}
                            </th>
                            <th class="px-6 py-3 text-left rtl:text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ t('created_at') }}
                            </th>
                            <th class="px-6 py-3 text-left rtl:text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ t('actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="course in courses.data" :key="course.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <Link :href="route('admin.courses.meetings', course.id)" class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                    {{ course.title }}
                                </Link>
                                <div class="text-sm text-gray-500 truncate max-w-xs">{{ course.description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ course.instructor?.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="['inline-flex px-2 py-1 text-xs font-semibold rounded-full', getStatusColor(course.status)]">
                                    {{ t(course.status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="['inline-flex px-2 py-1 text-xs font-semibold rounded-full', getLevelColor(course.level)]">
                                    {{ t(course.level) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ course.enrollments?.length || 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ new Date(course.created_at).toLocaleDateString(currentLocale === 'ar' ? 'ar-SA-u-ca-gregory' : 'en-US') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right rtl:text-left text-sm font-medium">
                                <div class="flex space-x-2 rtl:space-x-reverse">
                                    <Link :href="route('admin.courses.show', course.id)" class="text-brand hover:text-brand-dark">
                                        {{ t('view') }}
                                    </Link>
                                    <Link :href="route('admin.courses.edit', course.id)" class="text-blue-600 hover:text-blue-900">
                                        {{ t('edit') }}
                                    </Link>
                                    <Link :href="route('admin.enrollments.index', { course_id: course.id })" class="text-purple-600 hover:text-purple-900">
                                        {{ currentLocale === 'ar' ? 'التسجيلات' : 'Enrollments' }}
                                    </Link>
                                    <button @click="deleteCourse(course)" class="text-red-600 hover:text-red-900">
                                        {{ t('delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="courses.data.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">{{ t('no_courses') }}</h3>
            </div>

            <div v-if="courses.links && courses.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        {{ currentLocale === 'ar' ? 'عرض' : 'Showing' }} {{ courses.from }} {{ currentLocale === 'ar' ? 'إلى' : 'to' }} {{ courses.to }} {{ currentLocale === 'ar' ? 'من' : 'of' }} {{ courses.total }} {{ currentLocale === 'ar' ? 'نتيجة' : 'results' }}
                    </div>
                    <div class="flex space-x-1 rtl:space-x-reverse">
                        <Link
                            v-for="link in courses.links"
                            :key="link.label"
                            :href="link.url"
                            :class="[
                                'px-3 py-2 text-sm rounded-md',
                                link.active
                                    ? 'bg-brand text-white'
                                    : link.url
                                        ? 'text-gray-700 hover:text-gray-900 hover:bg-gray-100'
                                        : 'text-gray-400 cursor-not-allowed'
                            ]"
                        >
                            <span v-html="link.label"></span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
