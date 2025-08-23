<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            {{ currentLocale === 'ar' ? 'إدارة حسابات Zoom' : 'Zoom Accounts Management' }}
                        </h1>
                        <p class="text-gray-600">
                            {{ currentLocale === 'ar' ? 'إدارة حسابات Zoom الحقيقية للمنصة' : 'Manage real Zoom accounts for the platform' }}
                        </p>
                    </div>
                    <div class="flex space-x-3 rtl:space-x-reverse">
                        <button
                            @click="showCreateModal = true"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                        >
                            {{ currentLocale === 'ar' ? 'إضافة حساب Zoom جديد' : 'Add New Zoom Account' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Zoom Accounts List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ currentLocale === 'ar' ? 'اسم الحساب' : 'Account Name' }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ currentLocale === 'ar' ? 'البريد الإلكتروني' : 'Email' }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ currentLocale === 'ar' ? 'Account ID' : 'Account ID' }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ currentLocale === 'ar' ? 'الحالة' : 'Status' }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ currentLocale === 'ar' ? 'المعلم المرتبط' : 'Linked Teacher' }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ currentLocale === 'ar' ? 'الإجراءات' : 'Actions' }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="account in zoomAccounts" :key="account.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ account.name }}</div>
                                    <div class="text-sm text-gray-500">{{ account.description }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ account.email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-mono">{{ account.account_id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            account.is_active
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800',
                                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                                        ]"
                                    >
                                        {{ account.is_active ? (currentLocale === 'ar' ? 'نشط' : 'Active') : (currentLocale === 'ar' ? 'غير نشط' : 'Inactive') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="account.teachers && account.teachers.length > 0" class="text-sm text-gray-900">
                                        {{ account.teachers[0].name }}
                                    </div>
                                    <div v-else class="text-sm text-gray-500">
                                        {{ currentLocale === 'ar' ? 'غير مرتبط' : 'Not linked' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2 rtl:space-x-reverse">
                                        <button
                                            @click="editZoomAccount(account)"
                                            class="text-blue-600 hover:text-blue-900"
                                        >
                                            {{ currentLocale === 'ar' ? 'تعديل' : 'Edit' }}
                                        </button>
                                        <button
                                            @click="toggleZoomAccountStatus(account)"
                                            :class="[
                                                account.is_active
                                                    ? 'text-red-600 hover:text-red-900'
                                                    : 'text-green-600 hover:text-green-900'
                                            ]"
                                        >
                                            {{ account.is_active ? (currentLocale === 'ar' ? 'إلغاء تفعيل' : 'Deactivate') : (currentLocale === 'ar' ? 'تفعيل' : 'Activate') }}
                                        </button>
                                        <button
                                            @click="deleteZoomAccount(account)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            {{ currentLocale === 'ar' ? 'حذف' : 'Delete' }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ showEditModal ? (currentLocale === 'ar' ? 'تعديل حساب Zoom' : 'Edit Zoom Account') : (currentLocale === 'ar' ? 'إضافة حساب Zoom جديد' : 'Add New Zoom Account') }}
                    </h3>
                    
                    <form @submit.prevent="saveZoomAccount">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ currentLocale === 'ar' ? 'اسم الحساب' : 'Account Name' }}
                                </label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ currentLocale === 'ar' ? 'البريد الإلكتروني' : 'Email' }}
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ currentLocale === 'ar' ? 'Account ID' : 'Account ID' }}
                                </label>
                                <input
                                    v-model="form.account_id"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ currentLocale === 'ar' ? 'Client ID' : 'Client ID' }}
                                </label>
                                <input
                                    v-model="form.client_id"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ currentLocale === 'ar' ? 'Client Secret' : 'Client Secret' }}
                                </label>
                                <input
                                    v-model="form.client_secret"
                                    type="password"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ currentLocale === 'ar' ? 'الوصف' : 'Description' }}
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                ></textarea>
                            </div>
                            
                            <div>
                                <label class="flex items-center">
                                    <input
                                        v-model="form.is_active"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    />
                                    <span class="mr-2 text-sm text-gray-700">
                                        {{ currentLocale === 'ar' ? 'نشط' : 'Active' }}
                                    </span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3 rtl:space-x-reverse mt-6">
                            <button
                                type="button"
                                @click="closeModal"
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
                            >
                                {{ currentLocale === 'ar' ? 'إلغاء' : 'Cancel' }}
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                {{ showEditModal ? (currentLocale === 'ar' ? 'تحديث' : 'Update') : (currentLocale === 'ar' ? 'إنشاء' : 'Create') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');

// Props
const props = defineProps({
    zoomAccounts: Array
});

// Local state
const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingAccount = ref(null);

// Form data
const form = ref({
    name: '',
    email: '',
    account_id: '',
    client_id: '',
    client_secret: '',
    description: '',
    is_active: true
});

// Methods
const openCreateModal = () => {
    resetForm();
    showCreateModal.value = true;
};

const editZoomAccount = (account) => {
    editingAccount.value = account;
    form.value = {
        name: account.name,
        email: account.email,
        account_id: account.account_id,
        client_id: account.client_id,
        client_secret: account.client_secret,
        description: account.description || '',
        is_active: account.is_active
    };
    showEditModal.value = true;
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    editingAccount.value = null;
    resetForm();
};

const resetForm = () => {
    form.value = {
        name: '',
        email: '',
        account_id: '',
        client_id: '',
        client_secret: '',
        description: '',
        is_active: true
    };
};

const saveZoomAccount = async () => {
    try {
        const url = showEditModal.value 
            ? `/admin/zoom-accounts/${editingAccount.value.id}`
            : '/admin/zoom-accounts';
        
        const method = showEditModal.value ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(form.value)
        });

        const data = await response.json();

        if (data.success) {
            closeModal();
            router.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء حفظ الحساب');
        }
    } catch (error) {
        console.error('Error saving zoom account:', error);
        alert('حدث خطأ أثناء حفظ الحساب');
    }
};

const toggleZoomAccountStatus = async (account) => {
    try {
        const response = await fetch(`/admin/zoom-accounts/${account.id}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success) {
            router.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء تغيير حالة الحساب');
        }
    } catch (error) {
        console.error('Error toggling zoom account status:', error);
        alert('حدث خطأ أثناء تغيير حالة الحساب');
    }
};

const deleteZoomAccount = async (account) => {
    if (!confirm(currentLocale.value === 'ar' ? 'هل أنت متأكد من حذف هذا الحساب؟' : 'Are you sure you want to delete this account?')) {
        return;
    }

    try {
        const response = await fetch(`/admin/zoom-accounts/${account.id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success) {
            router.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء حذف الحساب');
        }
    } catch (error) {
        console.error('Error deleting zoom account:', error);
        alert('حدث خطأ أثناء حذف الحساب');
    }
};
</script>
