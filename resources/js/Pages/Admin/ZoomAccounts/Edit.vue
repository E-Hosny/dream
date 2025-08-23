<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            {{ currentLocale === 'ar' ? 'تعديل حساب Zoom' : 'Edit Zoom Account' }}
                        </h1>
                        <p class="text-gray-600">
                            {{ currentLocale === 'ar' ? 'تعديل بيانات حساب Zoom' : 'Update Zoom account information' }}
                        </p>
                    </div>
                    <button
                        @click="goBack"
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
                    >
                        {{ currentLocale === 'ar' ? 'العودة' : 'Back' }}
                    </button>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form @submit.prevent="updateAccount">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Account Name -->
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

                        <!-- Email -->
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

                        <!-- Account ID -->
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

                        <!-- Client ID -->
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

                        <!-- Client Secret -->
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

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ currentLocale === 'ar' ? 'الحالة' : 'Status' }}
                            </label>
                            <select
                                v-model="form.is_active"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option :value="true">{{ currentLocale === 'ar' ? 'نشط' : 'Active' }}</option>
                                <option :value="false">{{ currentLocale === 'ar' ? 'غير نشط' : 'Inactive' }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ currentLocale === 'ar' ? 'الوصف' : 'Description' }}
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        ></textarea>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 rtl:space-x-reverse mt-8">
                        <button
                            type="button"
                            @click="goBack"
                            class="px-6 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
                        >
                            {{ currentLocale === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button
                            type="submit"
                            :disabled="loading"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="loading">
                                {{ currentLocale === 'ar' ? 'جاري الحفظ...' : 'Saving...' }}
                            </span>
                            <span v-else>
                                {{ currentLocale === 'ar' ? 'حفظ التغييرات' : 'Save Changes' }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');

// Props
const props = defineProps({
    zoomAccount: Object
});

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

const loading = ref(false);

// Initialize form with account data
onMounted(() => {
    if (props.zoomAccount) {
        form.value = {
            name: props.zoomAccount.name || '',
            email: props.zoomAccount.email || '',
            account_id: props.zoomAccount.account_id || '',
            client_id: props.zoomAccount.client_id || '',
            client_secret: props.zoomAccount.client_secret || '',
            description: props.zoomAccount.description || '',
            is_active: props.zoomAccount.is_active ?? true
        };
    }
});

// Methods
const updateAccount = async () => {
    loading.value = true;
    
    try {
        const response = await fetch(`/admin/zoom-accounts/${props.zoomAccount.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(form.value)
        });

        const data = await response.json();

        if (data.success) {
            alert(currentLocale.value === 'ar' ? 'تم تحديث الحساب بنجاح' : 'Account updated successfully');
            router.visit('/admin/teachers');
        } else {
            alert(data.message || 'حدث خطأ أثناء تحديث الحساب');
        }
    } catch (error) {
        console.error('Error updating account:', error);
        alert('حدث خطأ أثناء تحديث الحساب');
    } finally {
        loading.value = false;
    }
};

const goBack = () => {
    router.visit('/admin/teachers');
};
</script>
