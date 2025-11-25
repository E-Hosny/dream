<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'ar');

defineProps({
    courses: Object,
    filters: Object
});

const searchForm = ref({
    search: '',
    status: '',
    level: ''
});

const search = () => {
    router.get(route('admin.courses.index'), searchForm.value, {
        preserveState: true,
        replace: true
    });
};

// Translation helper
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
            beginner: 'Beginner',
            intermediate: 'Intermediate',
            advanced: 'Advanced'
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
            beginner: 'مبتدئ',
            intermediate: 'متوسط',
            advanced: 'متقدم'
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
        <!-- Page Header -->
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

        <!-- Filters -->
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
                    <button @click="searchForm = { search: '', status: '', level: '' }; search()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                        {{ t('clear') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Courses Table -->
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
                                <div class="text-sm font-medium text-gray-900">{{ course.title }}</div>
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

            <!-- No Results -->
            <div v-if="courses.data.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">{{ t('no_courses') }}</h3>
            </div>

            <!-- Pagination -->
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
