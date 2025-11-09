<template>
    <Head title="إدارة حسابات Zoom" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    إدارة حسابات Zoom
                </h2>
                <Link :href="route('admin.zoom-accounts.create')"
                      class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة حساب جديد
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                            <input
                                v-model="filters.search"
                                type="text"
                                placeholder="اسم الحساب، البريد الإلكتروني، أو رقم الحساب"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                @input="filterAccounts"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                            <select
                                v-model="filters.status"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                @change="filterAccounts"
                            >
                                <option value="">الكل</option>
                                <option value="active">نشط</option>
                                <option value="inactive">غير نشط</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button
                                @click="resetFilters"
                                class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                            >
                                إعادة تعيين
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Accounts List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div v-if="accounts.data.length === 0" class="p-6 text-center text-gray-500">
                        لا توجد حسابات Zoom مسجلة
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        اسم الحساب
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        البريد الإلكتروني
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        رقم الحساب
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        المعلمون
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        الاجتماعات
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        الحالة
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        الإجراءات
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="account in accounts.data" :key="account.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ account.name }}
                                        </div>
                                        <div v-if="account.description" class="text-sm text-gray-500">
                                            {{ account.description }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ account.email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ account.account_id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ account.teachers_count }} معلم
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ account.meetings_count }} اجتماع
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="account.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        >
                                            {{ account.is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex gap-2">
                                            <button
                                                @click="testConnection(account)"
                                                class="text-blue-600 hover:text-blue-900"
                                                title="اختبار الاتصال"
                                            >
                                                <i class="fas fa-plug"></i>
                                            </button>
                                            <button
                                                @click="toggleActive(account)"
                                                :class="account.is_active ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900'"
                                                :title="account.is_active ? 'تعطيل' : 'تفعيل'"
                                            >
                                                <i :class="account.is_active ? 'fas fa-pause' : 'fas fa-play'"></i>
                                            </button>
                                            <Link
                                                :href="route('admin.zoom-accounts.show', account.id)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                                title="عرض"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </Link>
                                            <Link
                                                :href="route('admin.zoom-accounts.edit', account.id)"
                                                class="text-blue-600 hover:text-blue-900"
                                                title="تعديل"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <button
                                                @click="confirmDelete(account)"
                                                class="text-red-600 hover:text-red-900"
                                                title="حذف"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="accounts.data.length > 0" class="px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                عرض
                                <span class="font-medium">{{ accounts.from }}</span>
                                إلى
                                <span class="font-medium">{{ accounts.to }}</span>
                                من
                                <span class="font-medium">{{ accounts.total }}</span>
                                نتيجة
                            </div>
                            <div class="flex gap-2">
                                <component
                                    v-for="link in accounts.links"
                                    :key="link.label"
                                    :is="link.url ? Link : 'span'"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-2 rounded border',
                                        link.active ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                                        !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                    ]"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    accounts: Object,
    filters: Object
});

const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || ''
});

const filterAccounts = () => {
    router.get(route('admin.zoom-accounts.index'), filters, {
        preserveState: true,
        preserveScroll: true
    });
};

const resetFilters = () => {
    filters.search = '';
    filters.status = '';
    filterAccounts();
};

const testConnection = async (account) => {
    if (confirm('هل تريد اختبار الاتصال بهذا الحساب؟')) {
        try {
            const response = await axios.post(route('admin.zoom-accounts.test-connection', account.id));
            if (response.data.success) {
                alert('✅ ' + response.data.message);
            }
        } catch (error) {
            alert('❌ ' + (error.response?.data?.message || 'فشل اختبار الاتصال'));
        }
    }
};

const toggleActive = (account) => {
    const action = account.is_active ? 'تعطيل' : 'تفعيل';
    if (confirm(`هل أنت متأكد من ${action} هذا الحساب؟`)) {
        router.post(route('admin.zoom-accounts.toggle-active', account.id), {}, {
            preserveState: true,
            preserveScroll: true
        });
    }
};

const confirmDelete = (account) => {
    if (confirm(`هل أنت متأكد من حذف حساب "${account.name}"؟\n\nسيتم فك ارتباط جميع المعلمين من هذا الحساب.`)) {
        router.delete(route('admin.zoom-accounts.destroy', account.id));
    }
};
</script>
