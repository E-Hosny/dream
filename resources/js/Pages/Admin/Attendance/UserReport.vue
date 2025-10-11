<template>
    <AdminLayout>
        <Head :title="`تقرير حضور المستخدم - ${user.name}`" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="md:flex md:items-center md:justify-between">
                        <div class="flex-1 min-w-0">
                            <nav class="flex mb-4" aria-label="Breadcrumb">
                                <ol class="inline-flex items-center space-x-1 md:space-x-3 rtl:space-x-reverse">
                                    <li class="inline-flex items-center">
                                        <Link :href="route('admin.attendance.index')" class="text-gray-700 hover:text-gray-900 text-sm font-medium">
                                            تقارير الحضور
                                        </Link>
                                    </li>
                                    <li>
                                        <div class="flex items-center">
                                            <ChevronRightIcon class="flex-shrink-0 h-5 w-5 text-gray-400 rtl:rotate-180" />
                                            <span class="ml-1 text-gray-500 text-sm font-medium rtl:ml-0 rtl:mr-1">تقرير المستخدم</span>
                                        </div>
                                    </li>
                                </ol>
                            </nav>
                            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                                تقرير حضور المستخدم
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">{{ user.name }} - {{ user.email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">فلاتر التقرير</h3>
                        <form @submit.prevent="filterReport" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700">من تاريخ</label>
                                <input type="date" id="date_from" v-model="filters.date_from"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700">إلى تاريخ</label>
                                <input type="date" id="date_to" v-model="filters.date_to"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div class="flex items-end">
                                <button type="submit" 
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    تطبيق الفلتر
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- User Statistics -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <VideoCameraIcon class="h-6 w-6 text-blue-400" aria-hidden="true" />
                                </div>
                                <div class="ml-5 w-0 flex-1 rtl:ml-0 rtl:mr-5">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">إجمالي الاجتماعات</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.total_meetings }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <ClockIcon class="h-6 w-6 text-green-400" aria-hidden="true" />
                                </div>
                                <div class="ml-5 w-0 flex-1 rtl:ml-0 rtl:mr-5">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">إجمالي وقت الحضور</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ formatDuration(stats.total_duration) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <ChartBarIcon class="h-6 w-6 text-indigo-400" aria-hidden="true" />
                                </div>
                                <div class="ml-5 w-0 flex-1 rtl:ml-0 rtl:mr-5">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">متوسط الحضور</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ formatDuration(stats.average_duration) }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Info -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات المستخدم</h3>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">الاسم</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ user.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">البريد الإلكتروني</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ user.email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">نوع المستخدم</dt>
                                <dd class="mt-1">
                                    <span :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        getUserTypeColor()
                                    ]">
                                        {{ getUserTypeText() }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Attendance History -->
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                سجل الحضور
                            </h3>
                            <span class="text-sm text-gray-500">
                                {{ attendances.total }} سجل
                            </span>
                        </div>
                    </div>

                    <ul role="list" class="divide-y divide-gray-200">
                        <li v-for="attendance in attendances.data" :key="attendance.id" class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center flex-1">
                                    <div class="flex-shrink-0">
                                        <span :class="[
                                            'inline-flex items-center justify-center h-8 w-8 rounded-full text-sm font-medium',
                                            getActionColor(attendance.action_type)
                                        ]">
                                            {{ getActionIcon(attendance.action_type) }}
                                        </span>
                                    </div>
                                    <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ attendance.meeting.course.title_ar || attendance.meeting.course.title }}
                                                </p>
                                                <p class="text-sm text-gray-500">{{ attendance.meeting.topic }}</p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ getActionText(attendance.action_type) }}
                                                </p>
                                            </div>
                                            <div class="text-right rtl:text-left">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ formatDateTime(attendance.action_time) }}
                                                </p>
                                                <p v-if="attendance.duration_seconds" class="text-xs text-indigo-600 mt-1">
                                                    مدة الحضور: {{ formatDuration(attendance.duration_seconds) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <Link :href="route('admin.attendance.meeting-report', attendance.meeting.id)"
                                          class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        عرض التفاصيل
                                    </Link>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <!-- Empty State -->
                    <div v-if="attendances.data.length === 0" class="text-center py-12">
                        <UserIcon class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد سجلات حضور</h3>
                        <p class="mt-1 text-sm text-gray-500">لا توجد سجلات حضور لهذا المستخدم في الفترة المحددة</p>
                    </div>

                    <!-- Pagination -->
                    <div v-if="attendances.links" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link v-if="attendances.prev_page_url" :href="attendances.prev_page_url" 
                                      class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    السابق
                                </Link>
                                <Link v-if="attendances.next_page_url" :href="attendances.next_page_url"
                                      class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    التالي
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        عرض
                                        <span class="font-medium">{{ attendances.from }}</span>
                                        إلى
                                        <span class="font-medium">{{ attendances.to }}</span>
                                        من
                                        <span class="font-medium">{{ attendances.total }}</span>
                                        سجل
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
                                                  v-html="link.label">
                                            </Link>
                                            <span v-else 
                                                  :class="[
                                                      'relative inline-flex items-center px-2 py-2 border text-sm font-medium cursor-not-allowed',
                                                      'bg-gray-50 border-gray-300 text-gray-300'
                                                  ]"
                                                  v-html="link.label">
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
import { reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { 
    ChevronRightIcon,
    VideoCameraIcon,
    ClockIcon,
    ChartBarIcon,
    UserIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    user: Object,
    attendances: Object,
    stats: Object,
    filters: Object
})

const filters = reactive({
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
    course_id: props.filters.course_id || ''
})

const filterReport = () => {
    router.get(route('admin.attendance.user-report', props.user.id), filters, {
        preserveState: true,
        replace: true
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

const getActionColor = (actionType) => {
    const colors = {
        'join': 'bg-green-100 text-green-800',
        'leave': 'bg-red-100 text-red-800',
        'meeting_start': 'bg-blue-100 text-blue-800',
        'meeting_end': 'bg-gray-100 text-gray-800'
    }
    return colors[actionType] || 'bg-gray-100 text-gray-800'
}

const getActionIcon = (actionType) => {
    const icons = {
        'join': '→',
        'leave': '←',
        'meeting_start': '▶',
        'meeting_end': '⏹'
    }
    return icons[actionType] || '•'
}

const getActionText = (actionType) => {
    const texts = {
        'join': 'انضمام للاجتماع',
        'leave': 'مغادرة الاجتماع',
        'meeting_start': 'بداية الاجتماع',
        'meeting_end': 'نهاية الاجتماع'
    }
    return texts[actionType] || actionType
}

const getUserTypeColor = () => {
    // This would need to be determined based on user roles
    // For now, assuming we can determine from the attendance records
    const hasTeacherActions = props.attendances.data.some(a => 
        a.action_type === 'meeting_start' || a.action_type === 'meeting_end'
    )
    
    return hasTeacherActions ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'
}

const getUserTypeText = () => {
    const hasTeacherActions = props.attendances.data.some(a => 
        a.action_type === 'meeting_start' || a.action_type === 'meeting_end'
    )
    
    return hasTeacherActions ? 'معلم' : 'طالب'
}
</script>
