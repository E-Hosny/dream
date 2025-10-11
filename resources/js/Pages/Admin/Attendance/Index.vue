<template>
    <AdminLayout>
        <Head :title="t('attendance_reports')" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="md:flex md:items-center md:justify-between">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                                {{ t('attendance_reports') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ t('track_attendance_description') }}
                            </p>
                        </div>
                        <div class="mt-4 flex md:mt-0 md:ml-4">
                            <Link :href="route('admin.attendance.dashboard')" 
                                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <ChartBarIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                {{ t('statistics_dashboard') }}
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Search Filters -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ t('search_and_filter') }}</h3>
                        <form @submit.prevent="searchAttendances" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <!-- Course Filter -->
                            <div>
                                <label for="course_id" class="block text-sm font-medium text-gray-700">{{ t('course') }}</label>
                                <select id="course_id" v-model="filters.course_id" 
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">{{ t('all_courses') }}</option>
                                    <option v-for="course in courses" :key="course.id" :value="course.id">
                                        {{ course.title_ar || course.title }}
                                    </option>
                                </select>
                            </div>

                            <!-- User Type Filter -->
                            <div>
                                <label for="user_type" class="block text-sm font-medium text-gray-700">{{ t('user_type') }}</label>
                                <select id="user_type" v-model="filters.user_type"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">{{ t('all') }}</option>
                                    <option value="teacher">{{ t('teacher') }}</option>
                                    <option value="student">{{ t('student') }}</option>
                                </select>
                            </div>

                            <!-- Date From -->
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700">{{ t('from_date') }}</label>
                                <input type="date" id="date_from" v-model="filters.date_from"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <!-- Date To -->
                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700">{{ t('to_date') }}</label>
                                <input type="date" id="date_to" v-model="filters.date_to"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>

                            <!-- Search Button -->
                            <div class="flex items-end">
                                <button type="submit" 
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    🔍
                                    <span class="mr-2 rtl:mr-0 rtl:ml-2">{{ t('search') }}</span>
                                </button>
                            </div>

                            <!-- Clear Filters -->
                            <div class="flex items-end">
                                <button type="button" @click="clearFilters"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <XMarkIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                    {{ t('clear_filters') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                {{ t('attendance_log') }}
                            </h3>
                            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                <span class="text-sm text-gray-500">
                                    {{ attendances.total }} {{ t('participant') }}
                                </span>
                                <button @click="exportData" 
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <ArrowDownTrayIcon class="-ml-1 mr-2 h-4 w-4" />
                                    {{ t('export_data') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table Content -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ t('participant') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ t('meeting') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ t('join_time') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ t('leave_time') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ t('attendance_duration') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ t('status') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="attendance in attendances.data" :key="attendance.id" class="hover:bg-gray-50">
                                    <!-- User Info -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <UserIcon class="h-6 w-6 text-gray-500" />
                                                </div>
                                            </div>
                                            <div class="mr-4 rtl:mr-0 rtl:ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ attendance.user.name }}
                                                </div>
                                                <div class="flex items-center justify-center mt-1">
                                                    <span :class="[
                                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                        attendance.user_type === 'teacher' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'
                                                    ]">
                                                        {{ attendance.user_type === 'teacher' ? t('teacher') : t('student') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Meeting Info -->
                                    <td class="px-6 py-4 text-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ attendance.meeting.course.title_ar || attendance.meeting.course.title }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ attendance.meeting.topic }}
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Join Time -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div v-if="attendance.join_time" class="text-sm text-gray-900">
                                            {{ formatDateTime(attendance.join_time) }}
                                        </div>
                                        <div v-else class="text-sm text-gray-400">
                                            {{ t('not_specified') }}
                                        </div>
                                    </td>

                                    <!-- Leave Time -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div v-if="attendance.leave_time" class="text-sm text-gray-900">
                                            {{ formatDateTime(attendance.leave_time) }}
                                        </div>
                                        <div v-else class="text-sm text-gray-400">
                                            {{ t('still_connected') }}
                                        </div>
                                    </td>

                                    <!-- Duration -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div v-if="attendance.duration_seconds" class="text-sm font-medium text-indigo-600">
                                            {{ formatDuration(attendance.duration_seconds) }}
                                        </div>
                                        <div v-else class="text-sm text-gray-400">
                                            {{ attendance.is_active ? t('in_progress') : t('not_specified') }}
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            attendance.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                        ]">
                                            {{ attendance.is_active ? t('connected') : t('ended') }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div v-if="attendances.data.length === 0" class="text-center py-12">
                        <UserGroupIcon class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ t('no_attendance_records') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ t('no_records_match_criteria') }}</p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="attendances.links" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link v-if="attendances.prev_page_url" :href="attendances.prev_page_url" 
                                      class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    {{ t('previous') }}
                                </Link>
                                <Link v-if="attendances.next_page_url" :href="attendances.next_page_url"
                                      class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    {{ t('next') }}
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        {{ t('showing_results', {
                                            from: attendances.from || 0,
                                            to: attendances.to || 0,
                                            total: attendances.total
                                        }) }}
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <template v-for="link in attendances.links" :key="link.label">
                                            <Link v-if="link.url" :href="link.url" 
                                                  :class="[
                                                      'relative inline-flex items-center px-2 py-2 border text-sm font-medium',
                                                      link.active 
                                                          ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                                                          : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                                  ]"
                                                  v-text="link.label">
                                            </Link>
                                            <span v-else 
                                                  :class="[
                                                      'relative inline-flex items-center px-2 py-2 border text-sm font-medium cursor-not-allowed',
                                                      'bg-gray-50 border-gray-300 text-gray-300'
                                                  ]"
                                                  v-text="link.label">
                                            </span>
                                        </template>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { 
    ChartBarIcon, 
    XMarkIcon, 
    ArrowDownTrayIcon, 
    UserIcon,
    UserGroupIcon
} from '@heroicons/vue/24/outline'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()
const page = usePage()

// Sync i18n locale with Laravel locale
watch(() => page.props.locale, (newLocale) => {
    if (newLocale) {
        locale.value = newLocale
    }
}, { immediate: true })

const props = defineProps({
    attendances: Object,
    filters: Object,
    courses: Array,
    teachers: Array,
    students: Array
})

const filters = reactive({
    course_id: props.filters.course_id || '',
    user_type: props.filters.user_type || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
    user_id: props.filters.user_id || ''
})

const searchAttendances = () => {
    router.get(route('admin.attendance.index'), filters, {
        preserveState: true,
        replace: true
    })
}

const clearFilters = () => {
    Object.keys(filters).forEach(key => {
        filters[key] = ''
    })
    searchAttendances()
}

const exportData = () => {
    router.post(route('admin.attendance.export'), filters, {
        preserveState: true
    })
}

const formatDateTime = (dateTime) => {
    return new Date(dateTime).toLocaleString('ar-EG', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatDuration = (seconds) => {
    if (!seconds) return '0د'
    
    const hours = Math.floor(seconds / 3600)
    const minutes = Math.floor((seconds % 3600) / 60)
    
    if (hours > 0) {
        return `${hours}س ${minutes}د`
    }
    return `${minutes}د`
}
</script>