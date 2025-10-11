<template>
    <AdminLayout>
        <Head :title="`تقرير حضور الاجتماع - ${meeting.topic}`" />

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
                                            <span class="ml-1 text-gray-500 text-sm font-medium rtl:ml-0 rtl:mr-1">تقرير الاجتماع</span>
                                        </div>
                                    </li>
                                </ol>
                            </nav>
                            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                                تقرير حضور الاجتماع
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">{{ meeting.topic }}</p>
                        </div>
                        <div class="mt-4 flex md:mt-0 md:ml-4">
                            <Link :href="route('admin.attendance.course-report', meeting.course.id)" 
                                  class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <AcademicCapIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                تقرير الكورس
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Meeting Info -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات الاجتماع</h3>
                        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">موضوع الاجتماع</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ meeting.topic }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">الكورس</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ meeting.course.title_ar || meeting.course.title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">المعلم</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ meeting.creator.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">تاريخ الاجتماع</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ formatDate(meeting.start_time) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">المدة المخطط لها</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ meeting.duration }} دقيقة</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">الحالة</dt>
                                <dd class="mt-1">
                                    <span :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        getStatusColor(meeting.status)
                                    ]">
                                        {{ getStatusText(meeting.status) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Attendance Summary -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <UsersIcon class="h-6 w-6 text-blue-400" aria-hidden="true" />
                                </div>
                                <div class="ml-5 w-0 flex-1 rtl:ml-0 rtl:mr-5">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">إجمالي المشاركين</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ userSummary.length }}</dd>
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
                                        <dt class="text-sm font-medium text-gray-500 truncate">المعلمون</dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ userSummary.filter(u => u.user_type === 'teacher').length }}
                                        </dd>
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
                                        <dt class="text-sm font-medium text-gray-500 truncate">الطلاب</dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ userSummary.filter(u => u.user_type === 'student').length }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <ClockIcon class="h-6 w-6 text-indigo-400" aria-hidden="true" />
                                </div>
                                <div class="ml-5 w-0 flex-1 rtl:ml-0 rtl:mr-5">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">متوسط الحضور</dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ getAverageDuration() }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Summary -->
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            ملخص حضور المشاركين
                        </h3>
                    </div>
                    <ul role="list" class="divide-y divide-gray-200">
                        <li v-for="userAttendance in userSummary" :key="userAttendance.user.id" class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <UserIcon class="h-6 w-6 text-gray-500" />
                                        </div>
                                    </div>
                                    <div class="ml-4 rtl:ml-0 rtl:mr-4">
                                        <div class="flex items-center">
                                            <p class="text-sm font-medium text-gray-900">{{ userAttendance.user.name }}</p>
                                            <span :class="[
                                                'ml-2 rtl:ml-0 rtl:mr-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                userAttendance.user_type === 'teacher' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'
                                            ]">
                                                {{ userAttendance.user_type === 'teacher' ? 'معلم' : 'طالب' }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500">{{ userAttendance.user.email }}</p>
                                    </div>
                                </div>
                                <div class="text-right rtl:text-left">
                                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                                        <div v-if="userAttendance.join_time">
                                            <p class="text-xs text-gray-500">وقت الدخول</p>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ formatTime(userAttendance.join_time) }}
                                            </p>
                                        </div>
                                        <div v-if="userAttendance.leave_time">
                                            <p class="text-xs text-gray-500">وقت المغادرة</p>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ formatTime(userAttendance.leave_time) }}
                                            </p>
                                        </div>
                                        <div v-if="userAttendance.duration_seconds > 0">
                                            <p class="text-xs text-gray-500">مدة الحضور</p>
                                            <p class="text-sm font-medium text-indigo-600">
                                                {{ formatDuration(userAttendance.duration_seconds) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Detailed Timeline -->
                <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            الجدول الزمني التفصيلي
                        </h3>
                    </div>
                    <ul role="list" class="divide-y divide-gray-200">
                        <li v-for="attendance in attendances" :key="attendance.id" class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <span :class="[
                                            'inline-flex items-center justify-center h-8 w-8 rounded-full text-sm font-medium',
                                            getActionColor(attendance.action_type)
                                        ]">
                                            {{ getActionIcon(attendance.action_type) }}
                                        </span>
                                    </div>
                                    <div class="ml-4 rtl:ml-0 rtl:mr-4">
                                        <p class="text-sm font-medium text-gray-900">{{ attendance.user.name }}</p>
                                        <p class="text-sm text-gray-500">{{ getActionText(attendance.action_type) }}</p>
                                    </div>
                                </div>
                                <div class="text-right rtl:text-left">
                                    <p class="text-sm text-gray-900">{{ formatDateTime(attendance.action_time) }}</p>
                                    <p v-if="attendance.duration_seconds" class="text-xs text-gray-500">
                                        مدة الحضور: {{ formatDuration(attendance.duration_seconds) }}
                                    </p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { 
    ChevronRightIcon,
    AcademicCapIcon,
    UsersIcon,
    UserGroupIcon,
    ClockIcon,
    UserIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    meeting: Object,
    attendances: Array,
    userSummary: Array
})

const formatDate = (dateTime) => {
    return new Date(dateTime).toLocaleDateString('ar-EG', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const formatTime = (dateTime) => {
    return new Date(dateTime).toLocaleTimeString('ar-EG', {
        hour: '2-digit',
        minute: '2-digit'
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

const getAverageDuration = () => {
    const durationsWithData = props.userSummary.filter(u => u.duration_seconds > 0)
    if (durationsWithData.length === 0) return '0د'
    
    const totalDuration = durationsWithData.reduce((sum, u) => sum + u.duration_seconds, 0)
    const average = totalDuration / durationsWithData.length
    
    return formatDuration(Math.round(average))
}
</script>
