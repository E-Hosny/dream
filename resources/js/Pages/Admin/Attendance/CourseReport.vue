<template>
    <AdminLayout>
        <Head :title="`تقرير حضور الكورس - ${course.title_ar || course.title}`" />

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
                                            <span class="ml-1 text-gray-500 text-sm font-medium rtl:ml-0 rtl:mr-1">تقرير الكورس</span>
                                        </div>
                                    </li>
                                </ol>
                            </nav>
                            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                                تقرير حضور الكورس
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">{{ course.title_ar || course.title }}</p>
                        </div>
                    </div>
                </div>

                <!-- Date Filter -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">فلتر التاريخ</h3>
                        <form @submit.prevent="filterReport" class="flex items-end space-x-4 rtl:space-x-reverse">
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
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                تطبيق الفلتر
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Course Info -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات الكورس</h3>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">اسم الكورس</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ course.title_ar || course.title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">الاسم الإنجليزي</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ course.title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">إجمالي الاجتماعات</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ meetings.length }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Meetings List -->
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            اجتماعات الكورس
                        </h3>
                    </div>
                    <ul role="list" class="divide-y divide-gray-200">
                        <li v-for="meeting in meetings" :key="meeting.id" class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center flex-1">
                                    <div class="flex-shrink-0">
                                        <VideoCameraIcon class="h-6 w-6 text-gray-400" />
                                    </div>
                                    <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ meeting.topic }}</p>
                                                <p class="text-sm text-gray-500">
                                                    {{ formatDateTime(meeting.start_time) }}
                                                    • المدة: {{ meeting.duration }} دقيقة
                                                </p>
                                            </div>
                                            <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                                <span :class="[
                                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                    getStatusColor(meeting.status)
                                                ]">
                                                    {{ getStatusText(meeting.status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                    <!-- Meeting Statistics -->
                                    <div v-if="attendanceStats[meeting.id]" class="text-right rtl:text-left">
                                        <div class="flex items-center space-x-2 rtl:space-x-reverse text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <UsersIcon class="h-4 w-4 mr-1 rtl:mr-0 rtl:ml-1" />
                                                <span>{{ attendanceStats[meeting.id].total_joins }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <AcademicCapIcon class="h-4 w-4 mr-1 rtl:mr-0 rtl:ml-1 text-green-500" />
                                                <span>{{ attendanceStats[meeting.id].teacher_joins }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <UserGroupIcon class="h-4 w-4 mr-1 rtl:mr-0 rtl:ml-1 text-blue-500" />
                                                <span>{{ attendanceStats[meeting.id].student_joins }}</span>
                                            </div>
                                        </div>
                                        <p v-if="attendanceStats[meeting.id].average_duration" class="text-xs text-gray-400 mt-1">
                                            متوسط الحضور: {{ formatDuration(attendanceStats[meeting.id].average_duration) }}
                                        </p>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <Link :href="route('admin.attendance.meeting-report', meeting.id)"
                                          class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <ChartBarIcon class="-ml-1 mr-2 h-4 w-4" aria-hidden="true" />
                                        التفاصيل
                                    </Link>
                                </div>
                            </div>
                        </li>
                    </ul>
                    
                    <!-- Empty State -->
                    <div v-if="meetings.length === 0" class="text-center py-12">
                        <VideoCameraIcon class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد اجتماعات</h3>
                        <p class="mt-1 text-sm text-gray-500">لا توجد اجتماعات لهذا الكورس في الفترة المحددة</p>
                    </div>
                </div>

                <!-- Summary Statistics -->
                <div v-if="meetings.length > 0" class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <VideoCameraIcon class="h-6 w-6 text-gray-400" aria-hidden="true" />
                                </div>
                                <div class="ml-5 w-0 flex-1 rtl:ml-0 rtl:mr-5">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">إجمالي الاجتماعات</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ meetings.length }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <UsersIcon class="h-6 w-6 text-blue-400" aria-hidden="true" />
                                </div>
                                <div class="ml-5 w-0 flex-1 rtl:ml-0 rtl:mr-5">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">إجمالي المشاركات</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ getTotalParticipations() }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <AcademicCapIcon class="h-6 w-6 text-green-400" aria-hidden="true" />
                                </div>
                                <div class="ml-5 w-0 flex-1 rtl:ml-0 rtl:mr-5">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">حضور المعلمين</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ getTotalTeacherAttendance() }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <UserGroupIcon class="h-6 w-6 text-purple-400" aria-hidden="true" />
                                </div>
                                <div class="ml-5 w-0 flex-1 rtl:ml-0 rtl:mr-5">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">حضور الطلاب</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ getTotalStudentAttendance() }}</dd>
                                    </dl>
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
    UsersIcon,
    AcademicCapIcon,
    UserGroupIcon,
    ChartBarIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    course: Object,
    meetings: Array,
    attendanceStats: Object,
    filters: Object
})

const filters = reactive({
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || ''
})

const filterReport = () => {
    router.get(route('admin.attendance.course-report', props.course.id), filters, {
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

const getStatusColor = (status) => {
    const colors = {
        'scheduled': 'bg-yellow-100 text-yellow-800',
        'started': 'bg-green-100 text-green-800',
        'ended': 'bg-gray-100 text-gray-800',
        'cancelled': 'bg-red-100 text-red-800'
    }
    return colors[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
    const texts = {
        'scheduled': 'مجدول',
        'started': 'نشط',
        'ended': 'انتهى',
        'cancelled': 'ملغي'
    }
    return texts[status] || status
}

const getTotalParticipations = () => {
    return Object.values(props.attendanceStats).reduce((total, stats) => {
        return total + (stats.total_joins || 0)
    }, 0)
}

const getTotalTeacherAttendance = () => {
    return Object.values(props.attendanceStats).reduce((total, stats) => {
        return total + (stats.teacher_joins || 0)
    }, 0)
}

const getTotalStudentAttendance = () => {
    return Object.values(props.attendanceStats).reduce((total, stats) => {
        return total + (stats.student_joins || 0)
    }, 0)
}
</script>
