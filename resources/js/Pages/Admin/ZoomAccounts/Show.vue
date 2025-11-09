<template>
    <Head :title="`حساب Zoom: ${account.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    تفاصيل حساب Zoom: {{ account.name }}
                </h2>
                <div class="flex gap-2">
                    <Link :href="route('admin.zoom-accounts.edit', account.id)"
                          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل
                    </Link>
                    <Link :href="route('admin.zoom-accounts.index')"
                          class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-arrow-right ml-2"></i>
                        رجوع
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- معلومات أساسية -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                            <i class="fas fa-info-circle ml-2 text-blue-500"></i>
                            المعلومات الأساسية
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">اسم الحساب</label>
                                <p class="text-base text-gray-900">{{ account.name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">البريد الإلكتروني</label>
                                <p class="text-base text-gray-900">{{ account.email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Account ID</label>
                                <p class="text-base text-gray-900 font-mono">{{ account.account_id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">الحالة</label>
                                <span
                                    :class="account.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full"
                                >
                                    {{ account.is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500 mb-1">الوصف</label>
                                <p class="text-base text-gray-900">{{ account.description || 'لا يوجد وصف' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الإحصائيات -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">عدد المعلمين</p>
                                <p class="text-3xl font-bold text-blue-600">{{ account.teachers?.length || 0 }}</p>
                            </div>
                            <i class="fas fa-users text-4xl text-blue-200"></i>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">عدد الاجتماعات</p>
                                <p class="text-3xl font-bold text-purple-600">{{ account.meetings?.length || 0 }}</p>
                            </div>
                            <i class="fas fa-video text-4xl text-purple-200"></i>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">حد الاجتماعات اليومي</p>
                                <p class="text-3xl font-bold text-green-600">{{ account.max_meetings_per_day }}</p>
                            </div>
                            <i class="fas fa-calendar-check text-4xl text-green-200"></i>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">حد المشاركين</p>
                                <p class="text-3xl font-bold text-orange-600">{{ account.max_participants }}</p>
                            </div>
                            <i class="fas fa-user-friends text-4xl text-orange-200"></i>
                        </div>
                    </div>
                </div>

                <!-- الميزات -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                            <i class="fas fa-star ml-2 text-yellow-500"></i>
                            الميزات المتاحة
                        </h3>
                        <div v-if="account.features && account.features.length > 0" class="flex flex-wrap gap-2">
                            <span
                                v-for="feature in account.features"
                                :key="feature"
                                class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium"
                            >
                                <i class="fas fa-check-circle ml-1"></i>
                                {{ getFeatureLabel(feature) }}
                            </span>
                        </div>
                        <p v-else class="text-gray-500">لا توجد ميزات خاصة</p>
                    </div>
                </div>

                <!-- المعلمون المرتبطون -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                            <i class="fas fa-chalkboard-teacher ml-2 text-green-500"></i>
                            المعلمون المرتبطون بهذا الحساب
                        </h3>
                        <div v-if="account.teachers && account.teachers.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            الاسم
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            البريد الإلكتروني
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            الإجراءات
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="teacher in account.teachers" :key="teacher.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ teacher.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ teacher.email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <Link
                                                :href="route('admin.teachers.index', { search: teacher.email })"
                                                class="text-blue-600 hover:text-blue-900"
                                            >
                                                <i class="fas fa-eye ml-1"></i>
                                                عرض
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p v-else class="text-gray-500">لا يوجد معلمون مرتبطون بهذا الحساب</p>
                    </div>
                </div>

                <!-- آخر الاجتماعات -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                            <i class="fas fa-history ml-2 text-purple-500"></i>
                            آخر الاجتماعات
                        </h3>
                        <div v-if="account.meetings && account.meetings.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            الموضوع
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            وقت البدء
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            المدة
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            الحالة
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="meeting in account.meetings" :key="meeting.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ meeting.topic }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(meeting.start_time) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ meeting.duration }} دقيقة
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="getStatusClass(meeting.status)"
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            >
                                                {{ getStatusLabel(meeting.status) }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p v-else class="text-gray-500">لا توجد اجتماعات مسجلة لهذا الحساب</p>
                    </div>
                </div>

                <!-- معلومات النظام -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                            <i class="fas fa-database ml-2 text-gray-500"></i>
                            معلومات النظام
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">تم الإنشاء بواسطة</label>
                                <p class="text-base text-gray-900">{{ account.creator?.name || 'غير محدد' }}</p>
                                <p class="text-sm text-gray-500">{{ formatDate(account.created_at) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">آخر تحديث بواسطة</label>
                                <p class="text-base text-gray-900">{{ account.updater?.name || 'غير محدد' }}</p>
                                <p class="text-sm text-gray-500">{{ formatDate(account.updated_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    account: Object
});

const getFeatureLabel = (feature) => {
    const labels = {
        'recording': 'تسجيل',
        'transcription': 'نسخ نصي',
        'breakout_rooms': 'غرف منفصلة',
        'polling': 'استطلاعات'
    };
    return labels[feature] || feature;
};

const getStatusClass = (status) => {
    const classes = {
        'scheduled': 'bg-blue-100 text-blue-800',
        'started': 'bg-green-100 text-green-800',
        'ended': 'bg-gray-100 text-gray-800',
        'cancelled': 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        'scheduled': 'مجدول',
        'started': 'نشط',
        'ended': 'انتهى',
        'cancelled': 'ملغي'
    };
    return labels[status] || status;
};

const formatDate = (date) => {
    if (!date) return 'غير محدد';
    return new Date(date).toLocaleString('ar-SA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

