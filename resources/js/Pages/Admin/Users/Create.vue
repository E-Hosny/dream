<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');

defineProps({
    roles: Array
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'student'
});

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            create_user: 'Create New User',
            back_to_users: 'Back to Users',
            name: 'Full Name',
            email: 'Email Address',
            password: 'Password',
            confirm_password: 'Confirm Password',
            role: 'User Role',
            create: 'Create User',
            cancel: 'Cancel',
            name_placeholder: 'Enter full name',
            email_placeholder: 'Enter email address',
            password_placeholder: 'Enter password',
            confirm_password_placeholder: 'Confirm password'
        },
        ar: {
            create_user: 'إنشاء مستخدم جديد',
            back_to_users: 'العودة للمستخدمين',
            name: 'الاسم الكامل',
            email: 'البريد الإلكتروني',
            password: 'كلمة المرور',
            confirm_password: 'تأكيد كلمة المرور',
            role: 'دور المستخدم',
            create: 'إنشاء مستخدم',
            cancel: 'إلغاء',
            name_placeholder: 'أدخل الاسم الكامل',
            email_placeholder: 'أدخل البريد الإلكتروني',
            password_placeholder: 'أدخل كلمة المرور',
            confirm_password_placeholder: 'أكد كلمة المرور'
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

const submit = () => {
    form.post(route('admin.users.store'));
};
</script>

<template>
    <Head :title="t('create_user')" />

    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ t('create_user') }}</h1>
                <p class="text-gray-600 mt-2">{{ currentLocale === 'ar' ? 'إضافة مستخدم جديد للنظام' : 'Add a new user to the system' }}</p>
            </div>
            <Link :href="route('admin.users.index')" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-md hover:shadow-lg">
                <svg class="w-5 h-5 inline mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                {{ t('back_to_users') }}
            </Link>
        </div>

        <!-- Create Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ t('name') }}</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        :placeholder="t('name_placeholder')"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        :class="{ 'border-red-500': form.errors.name }"
                        required
                    >
                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                        {{ form.errors.name }}
                    </div>
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ t('email') }}</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        :placeholder="t('email_placeholder')"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        :class="{ 'border-red-500': form.errors.email }"
                        required
                    >
                    <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                        {{ form.errors.email }}
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">{{ t('password') }}</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        :placeholder="t('password_placeholder')"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        :class="{ 'border-red-500': form.errors.password }"
                        required
                    >
                    <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                        {{ form.errors.password }}
                    </div>
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">{{ t('confirm_password') }}</label>
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        :placeholder="t('confirm_password_placeholder')"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        :class="{ 'border-red-500': form.errors.password_confirmation }"
                        required
                    >
                    <div v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-600">
                        {{ form.errors.password_confirmation }}
                    </div>
                </div>

                <!-- Role Field -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">{{ t('role') }}</label>
                    <select
                        id="role"
                        v-model="form.role"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        :class="{ 'border-red-500': form.errors.role }"
                        required
                    >
                        <option v-for="role in roles" :key="role.id" :value="role.name">
                            {{ role.name === 'admin' ? (currentLocale === 'ar' ? 'مدير' : 'Admin') :
                               role.name === 'teacher' ? (currentLocale === 'ar' ? 'معلم' : 'Teacher') :
                               (currentLocale === 'ar' ? 'طالب' : 'Student') }}
                        </option>
                    </select>
                    <div v-if="form.errors.role" class="mt-1 text-sm text-red-600">
                        {{ form.errors.role }}
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4 rtl:space-x-reverse pt-6 border-t border-gray-200">
                    <Link :href="route('admin.users.index')" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-all duration-200">
                        {{ t('cancel') }}
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ form.processing ? (currentLocale === 'ar' ? 'جاري الإنشاء...' : 'Creating...') : t('create') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
