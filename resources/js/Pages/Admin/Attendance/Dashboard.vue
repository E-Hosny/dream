<template>
    <AdminLayout>
        <Head title="لوحة إحصائيات الحضور" />

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="md:flex md:items-center md:justify-between">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                                لوحة إحصائيات الحضور
                            </h2>
                            <p class="mt-1 text-sm text-gray-500">
                                نظرة شاملة على إحصائيات الحضور والانصراف
                            </p>
                        </div>
                        <div class="mt-4 flex md:mt-0 md:ml-4">
                            <Link :href="route('admin.attendance.index')" 
                                  class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <ListBulletIcon class="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                                السجل التفصيلي
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Date Filter -->
                <div class="bg-white shadow rounded-lg mb-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">فلتر التاريخ</h3>
                        <form @submit.prevent="filterStats" class="flex items-end space-x-4 rtl:space-x-reverse">
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
                                تحديث الإحصائيات
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                    <!-- Total Meetings -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <VideoCameraIcon class="h-6 w-6 text-gray-400" aria-hidden="true" />
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

                    <!-- Total Attendances -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <UsersIcon class="h-6 w-6 text-blue-400" aria-hidden="true" />
                                </div>
                                <div class="ml-5 w-0 flex-1 rtl:ml-0 rtl:mr-5">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">إجمالي الحضور</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.total_attendances }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Teacher Attendances -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <AcademicCapIcon class="h-6 w-6 text-green-400" aria-hidden="true" />
                                </div>
                                <div class="ml-5 w-0 flex-1 rtl:ml-0 rtl:mr-5">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">حضور المعلمين</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.teacher_attendances }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Student Attendances -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <UserGroupIcon class="h-6 w-6 text-purple-400" aria-hidden="true" />
                                </div>
                                <div class="ml-5 w-0 flex-1 rtl:ml-0 rtl:mr-5">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">حضور الطلاب</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ stats.student_attendances }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Average Duration Card -->
                <div class="bg-white shadow rounded-lg mb-8">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <ClockIcon class="h-8 w-8 text-indigo-500" aria-hidden="true" />
                            </div>
                            <div class="ml-5 rtl:ml-0 rtl:mr-5">
                                <h3 class="text-lg font-medium text-gray-900">متوسط مدة الحضور</h3>
                                <p class="text-3xl font-bold text-indigo-600">{{ formatDuration(stats.average_duration) }}</p>
                                <p class="text-sm text-gray-500 mt-1">متوسط الوقت المُستغرق في الاجتماعات</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Courses -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-6">أكثر الكورسات نشاطاً</h3>
                        <div v-if="topCourses.length > 0" class="space-y-4">
                            <div v-for="(course, index) in topCourses" :key="course.id" 
                                 class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div :class="[
                                            'w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-sm',
                                            getRankColor(index)
                                        ]">
                                            {{ index + 1 }}
                                        </div>
                                    </div>
                                    <div class="ml-3 rtl:ml-0 rtl:mr-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ course.title_ar || course.title }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ course.attendance_count }} مشارك
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                    <Link :href="route('admin.attendance.course-report', course.id)"
                                          class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        عرض التفاصيل
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <ChartBarIcon class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد بيانات</h3>
                            <p class="mt-1 text-sm text-gray-500">لا توجد كورسات نشطة في هذه الفترة</p>
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
    VideoCameraIcon,
    UsersIcon,
    AcademicCapIcon,
    UserGroupIcon,
    ClockIcon,
    ChartBarIcon,
    ListBulletIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    stats: Object,
    topCourses: Array,
    filters: Object
})

const filters = reactive({
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || ''
})

const filterStats = () => {
    router.get(route('admin.attendance.dashboard'), filters, {
        preserveState: true,
        replace: true
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

const getRankColor = (index) => {
    const colors = [
        'bg-yellow-500',   // 1st - Gold
        'bg-gray-400',     // 2nd - Silver
        'bg-orange-600',   // 3rd - Bronze
        'bg-blue-500',     // 4th - Blue
        'bg-green-500'     // 5th - Green
    ]
    return colors[index] || 'bg-gray-500'
}
</script>
