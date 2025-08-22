<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                إدارة التسجيلات
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                                <!-- Header with Actions -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">قائمة التسجيلات</h3>
                    <p class="text-gray-600 mt-2">إدارة تسجيلات الطلاب في الكورسات المختلفة</p>
                </div>
                <div class="flex space-x-3 rtl:space-x-reverse">
                    <Link
                        :href="route('admin.enrollments.create')"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl"
                    >
                        <svg class="w-5 h-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        إضافة تسجيل جديد
                    </Link>
                    <button
                        @click="showBulkEnrollModal = true"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl"
                    >
                        <svg class="w-5 h-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        تسجيل جماعي
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-400 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4 rtl:ml-0 rtl:mr-4">
                            <p class="text-blue-100 text-sm">إجمالي التسجيلات</p>
                            <p class="text-2xl font-bold">{{ stats?.total || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-400 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4 rtl:ml-0 rtl:mr-4">
                            <p class="text-green-100 text-sm">مسجلين</p>
                            <p class="text-2xl font-bold">{{ stats?.enrolled || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-400 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4 rtl:ml-0 rtl:mr-4">
                            <p class="text-purple-100 text-sm">مكتملين</p>
                            <p class="text-2xl font-bold">{{ stats?.completed || 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-6 text-white shadow-lg">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-400 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div class="ml-4 rtl:ml-0 rtl:mr-4">
                            <div class="text-red-100 text-sm">منسحبين</div>
                            <p class="text-2xl font-bold">{{ stats?.dropped || 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

                        <!-- Filters -->
                        <div class="mb-6 bg-white rounded-xl shadow-lg border-0 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-900">فلترة وبحث</h4>
                                <button 
                                    @click="clearFilters" 
                                    class="text-sm text-gray-500 hover:text-gray-700 flex items-center"
                                >
                                    <svg class="w-4 h-4 mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    مسح الفلاتر
                                </button>
                            </div>
                            
                            <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                                    <div class="relative">
                                        <input 
                                            v-model="filters.search" 
                                            type="text" 
                                            placeholder="البحث في الطلاب أو الكورسات..."
                                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                        >
                                        <svg class="absolute left-3 rtl:left-auto rtl:right-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">الكورس</label>
                                    <select v-model="filters.course_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                        <option value="">جميع الكورسات</option>
                                        <option v-for="course in courses" :key="course.id" :value="course.id">
                                            {{ course.title_ar || course.title }}
                                        </option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">الطالب</label>
                                    <select v-model="filters.student_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                        <option value="">جميع الطلاب</option>
                                        <option v-for="student in students" :key="student.id" :value="student.id">
                                            {{ student.name }}
                                        </option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                                    <select v-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                        <option value="">جميع الحالات</option>
                                        <option value="enrolled">مسجل</option>
                                        <option value="completed">مكتمل</option>
                                        <option value="dropped">منسحب</option>
                                    </select>
                                </div>
                                
                                <div class="flex items-end">
                                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg">
                                        <svg class="w-5 h-5 inline mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        تطبيق
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Enrollments Table -->
                        <div class="bg-white rounded-xl shadow-lg border-0 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                        <tr>
                                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    الطالب
                                                </div>
                                            </th>
                                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.966 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                                                    </svg>
                                                    الكورس
                                                </div>
                                            </th>
                                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    الحالة
                                                </div>
                                            </th>
                                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                    </svg>
                                                    التقدم
                                                </div>
                                            </th>
                                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    تاريخ التسجيل
                                                </div>
                                            </th>
                                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    الإجراءات
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        <tr v-for="enrollment in enrollments.data" :key="enrollment.id" class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                                                            {{ enrollment.student.name.charAt(0) }}
                                                        </div>
                                                    </div>
                                                    <div class="mr-4 rtl:mr-0 rtl:ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ enrollment.student.name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ enrollment.student.email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ enrollment.course.title_ar || enrollment.course.title }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ getLevelText(enrollment.course.level) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span :class="getStatusClass(enrollment.status)" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium">
                                                    <span :class="getStatusDot(enrollment.status)" class="w-2 h-2 rounded-full mr-2 rtl:mr-0 rtl:ml-2"></span>
                                                    {{ getStatusText(enrollment.status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-3 rtl:mr-0 rtl:ml-3">
                                                        <div 
                                                            class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-300"
                                                            :style="{ width: (enrollment.progress || 0) + '%' }"
                                                        ></div>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-900">
                                                        {{ enrollment.progress || 0 }}%
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ formatDate(enrollment.enrolled_at) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2 rtl:space-x-reverse">
                                                    <Link
                                                        :href="route('admin.enrollments.edit', enrollment.id)"
                                                        class="inline-flex items-center px-3 py-1.5 border border-blue-300 text-blue-700 bg-blue-50 rounded-md text-xs font-medium hover:bg-blue-100 hover:border-blue-400 transition-colors duration-200"
                                                    >
                                                        <svg class="w-4 h-4 mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        تعديل
                                                    </Link>
                                                    <button
                                                        @click="deleteEnrollment(enrollment.id)"
                                                        class="inline-flex items-center px-3 py-1.5 border border-red-300 text-red-700 bg-red-50 rounded-md text-xs font-medium hover:bg-red-100 hover:border-red-400 transition-colors duration-200"
                                                    >
                                                        <svg class="w-4 h-4 mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        حذف
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6" v-if="enrollments.links">
                            <nav class="flex justify-center">
                                <div class="flex space-x-1 rtl:space-x-reverse">
                                                                            <Link
                                            v-for="(link, key) in enrollments.links"
                                            :key="key"
                                            :href="link.url"
                                            :class="[
                                                'px-3 py-2 text-sm font-medium rounded-md',
                                                link.url === null
                                                    ? 'text-gray-400 cursor-not-allowed'
                                                    : link.active
                                                    ? 'bg-blue-600 text-white'
                                                    : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'
                                            ]"
                                        >
                                            <span v-html="link.label"></span>
                                        </Link>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Enroll Modal -->
        <Modal :show="showBulkEnrollModal" @close="showBulkEnrollModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">تسجيل طلاب متعددين</h3>
                
                <form @submit.prevent="submitBulkEnroll">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">اختر الكورس</label>
                        <select v-model="bulkForm.course_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">اختر الكورس</option>
                            <option v-for="course in courses" :key="course.id" :value="course.id">
                                {{ course.title_ar || course.title }}
                            </option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">اختر الطلاب</label>
                        <select v-model="bulkForm.student_ids" multiple required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" size="6">
                            <option v-for="student in students" :key="student.id" :value="student.id">
                                {{ student.name }} ({{ student.email }})
                            </option>
                        </select>
                        <p class="text-sm text-gray-500 mt-1">اضغط Ctrl (أو Cmd) لاختيار طلاب متعددين</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                        <select v-model="bulkForm.status" required class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="enrolled">مسجل</option>
                            <option value="completed">مكتمل</option>
                            <option value="dropped">منسحب</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3 rtl:space-x-reverse">
                        <button
                            type="button"
                            @click="showBulkEnrollModal = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                        >
                            إلغاء
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700"
                        >
                            تسجيل الطلاب
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    enrollments: Object,
    courses: Array,
    students: Array,
    stats: Object,
    filters: Object
})

const showBulkEnrollModal = ref(false)
const bulkForm = reactive({
    course_id: '',
    student_ids: [],
    status: 'enrolled'
})

const applyFilters = () => {
    router.get(route('admin.enrollments.index'), filters.value, {
        preserveState: true,
        preserveScroll: true
    })
}

const clearFilters = () => {
    filters.value = {
        course_id: '',
        student_id: '',
        status: '',
        search: ''
    }
    applyFilters()
}

const submitBulkEnroll = () => {
    router.post(route('admin.enrollments.bulk'), bulkForm, {
        onSuccess: () => {
            showBulkEnrollModal.value = false
            bulkForm.course_id = ''
            bulkForm.student_ids = []
            bulkForm.status = 'enrolled'
        }
    })
}

const deleteEnrollment = (id) => {
    if (confirm('هل أنت متأكد من حذف هذا التسجيل؟')) {
        router.delete(route('admin.enrollments.destroy', id))
    }
}

const getStatusClass = (status) => {
    switch (status) {
        case 'enrolled':
            return 'bg-blue-100 text-blue-800 border border-blue-200'
        case 'completed':
            return 'bg-green-100 text-green-800 border border-green-200'
        case 'dropped':
            return 'bg-red-100 text-red-800 border border-red-200'
        default:
            return 'bg-gray-100 text-gray-800 border border-gray-200'
    }
}

const getStatusDot = (status) => {
    switch (status) {
        case 'enrolled':
            return 'bg-blue-500'
        case 'completed':
            return 'bg-green-500'
        case 'dropped':
            return 'bg-red-500'
        default:
            return 'bg-gray-500'
    }
}

const getStatusText = (status) => {
    switch (status) {
        case 'enrolled':
            return 'مسجل'
        case 'completed':
            return 'مكتمل'
        case 'dropped':
            return 'منسحب'
        default:
            return status
    }
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('ar-SA')
}

const getLevelText = (level) => {
    switch (level) {
        case 'beginner':
            return 'مبتدئ'
        case 'intermediate':
            return 'متوسط'
        case 'advanced':
            return 'متقدم'
        default:
            return level
    }
}
</script>
