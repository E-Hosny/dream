<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'ar');

const props = defineProps({
    roles: { type: Array, default: () => [] }
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    role: 'student',
    specialty: '',
    grade_level: ''
});

// إن لم تطابق form.role أي :value في الخيارات (اختلاف حالة الأحرف أو اسم غير موجود) يبقى الـ select فارغاً في المتصفح
watch(
    () => props.roles,
    (list) => {
        if (!list?.length) {
            return;
        }
        const names = list.map((r) => String(r.name));
        const cur = String(form.role ?? '');
        const exact = names.find((n) => n === cur);
        if (exact !== undefined) {
            return;
        }
        const ci = names.find((n) => n.toLowerCase() === cur.toLowerCase());
        if (ci !== undefined) {
            form.role = ci;
            return;
        }
        const student = names.find((n) => n.toLowerCase() === 'student');
        form.role = student ?? names[0];
    },
    { immediate: true, deep: true }
);

/** مقارنة الدور بدون حساسية لحالة الأحرف (تفادي اختلاف التخزين في DB مثل Student) */
const normalizedRole = computed(() => String(form.role ?? '').toLowerCase().trim());

const isTeacher = computed(() => normalizedRole.value === 'teacher');
const isStudent = computed(() => normalizedRole.value === 'student');

const roleDisplayName = (name) => {
    const n = String(name ?? '').toLowerCase().trim();
    const ar = currentLocale.value === 'ar';
    if (n === 'admin') return ar ? 'مدير' : 'Admin';
    if (n === 'teacher') return ar ? 'معلم' : 'Teacher';
    if (n === 'student') return ar ? 'طالب' : 'Student';
    return String(name ?? '');
};

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
            confirm_password_placeholder: 'Confirm password',
            phone: 'Mobile number',
            phone_placeholder: 'Optional',
            specialty: 'Specialty',
            specialty_placeholder: 'Optional',
            grade_level: 'Grade / class',
            grade_level_placeholder: 'Optional',
            optional: 'Optional'
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
            confirm_password_placeholder: 'أكد كلمة المرور',
            phone: 'رقم الجوال',
            phone_placeholder: 'اختياري',
            specialty: 'التخصص',
            specialty_placeholder: 'اختياري',
            grade_level: 'الصف الدراسي',
            grade_level_placeholder: 'اختياري',
            optional: 'اختياري'
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
        <div class="bg-white rounded-2xl shadow-xl border-0 overflow-visible backdrop-blur-sm bg-white/95">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
                <h2 class="text-xl font-semibold text-white">{{ currentLocale === 'ar' ? 'بيانات المستخدم الجديد' : 'New User Information' }}</h2>
                <p class="text-indigo-100 mt-1">{{ currentLocale === 'ar' ? 'أدخل جميع البيانات المطلوبة بعناية' : 'Please fill in all required information carefully' }}</p>
            </div>
            <form @submit.prevent="submit" class="p-8 space-y-8">
                <!-- Name Field -->
                <div class="group">
                    <label for="name" class="block text-sm font-semibold text-gray-800 mb-3 transition-colors group-focus-within:text-indigo-600">
                        {{ t('name') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 flex items-center pl-4 rtl:pl-0 rtl:pr-4 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            :placeholder="t('name_placeholder')"
                            class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md focus:shadow-lg"
                            :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.name }"
                            required
                        >
                    </div>
                    <div v-if="form.errors.name" class="mt-2 flex items-center text-sm text-red-600">
                        <svg class="w-4 h-4 mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ form.errors.name }}
                    </div>
                </div>

                <!-- Email Field -->
                <div class="group">
                    <label for="email" class="block text-sm font-semibold text-gray-800 mb-3 transition-colors group-focus-within:text-indigo-600">
                        {{ t('email') }}
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 flex items-center pl-4 rtl:pl-0 rtl:pr-4 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            :placeholder="t('email_placeholder')"
                            class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md focus:shadow-lg"
                            :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.email }"
                            required
                        >
                    </div>
                    <div v-if="form.errors.email" class="mt-2 flex items-center text-sm text-red-600">
                        <svg class="w-4 h-4 mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ form.errors.email }}
                    </div>
                </div>

                <!-- Phone (optional) -->
                <div class="group">
                    <label for="phone" class="block text-sm font-semibold text-gray-800 mb-3 transition-colors group-focus-within:text-indigo-600">
                        {{ t('phone') }}
                        <span class="text-gray-400 font-normal text-xs mr-1 rtl:mr-0 rtl:ml-1">({{ t('optional') }})</span>
                    </label>
                    <input
                        id="phone"
                        v-model="form.phone"
                        type="text"
                        autocomplete="tel"
                        :placeholder="t('phone_placeholder')"
                        class="w-full px-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md focus:shadow-lg"
                        :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.phone }"
                    >
                    <div v-if="form.errors.phone" class="mt-2 text-sm text-red-600">{{ form.errors.phone }}</div>
                </div>

                <!-- Password Fields Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Password Field -->
                    <div class="group">
                        <label for="password" class="block text-sm font-semibold text-gray-800 mb-3 transition-colors group-focus-within:text-indigo-600">
                            {{ t('password') }}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 flex items-center pl-4 rtl:pl-0 rtl:pr-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                :placeholder="t('password_placeholder')"
                                class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md focus:shadow-lg"
                                :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.password }"
                                required
                            >
                        </div>
                        <div v-if="form.errors.password" class="mt-2 flex items-center text-sm text-red-600">
                            <svg class="w-4 h-4 mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="group">
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-800 mb-3 transition-colors group-focus-within:text-indigo-600">
                            {{ t('confirm_password') }}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 flex items-center pl-4 rtl:pl-0 rtl:pr-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                :placeholder="t('confirm_password_placeholder')"
                                class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md focus:shadow-lg"
                                :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.password_confirmation }"
                                required
                            >
                        </div>
                        <div v-if="form.errors.password_confirmation" class="mt-2 flex items-center text-sm text-red-600">
                            <svg class="w-4 h-4 mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ form.errors.password_confirmation }}
                        </div>
                    </div>
                </div>

                <!-- Role Field: بدون أيقونات فوق الـ select (تسبب إخفاء النص في RTL/Chrome) -->
                <div class="group">
                    <label for="role" class="block text-sm font-semibold text-gray-800 mb-3 transition-colors group-focus-within:text-indigo-600">
                        {{ t('role') }}
                    </label>
                    <select
                        id="role"
                        v-model="form.role"
                        :dir="currentLocale === 'ar' ? 'rtl' : 'ltr'"
                        required
                        class="block w-full min-h-[3.25rem] px-4 py-3 text-base leading-normal text-gray-900 bg-white border border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 [color-scheme:light]"
                        :class="{ 'ring-2 ring-red-500 border-red-300': form.errors.role }"
                    >
                        <option v-if="!roles.length" value="" disabled>
                            {{ currentLocale === 'ar' ? 'لا توجد أدوار في النظام' : 'No roles available' }}
                        </option>
                        <option v-for="role in roles" :key="role.id" :value="role.name">
                            {{ roleDisplayName(role.name) }}
                        </option>
                    </select>
                    <div v-if="form.errors.role" class="mt-2 flex items-center text-sm text-red-600">
                        <svg class="w-4 h-4 mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ form.errors.role }}
                    </div>
                </div>

                <!-- Teacher: specialty (optional) -->
                <div v-if="isTeacher" class="group">
                    <label for="specialty" class="block text-sm font-semibold text-gray-800 mb-3 transition-colors group-focus-within:text-indigo-600">
                        {{ t('specialty') }}
                        <span class="text-gray-400 font-normal text-xs mr-1 rtl:mr-0 rtl:ml-1">({{ t('optional') }})</span>
                    </label>
                    <input
                        id="specialty"
                        v-model="form.specialty"
                        type="text"
                        :placeholder="t('specialty_placeholder')"
                        class="w-full px-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md focus:shadow-lg"
                        :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.specialty }"
                    >
                    <div v-if="form.errors.specialty" class="mt-2 text-sm text-red-600">{{ form.errors.specialty }}</div>
                </div>

                <!-- Student: grade level (optional) -->
                <div v-if="isStudent" class="group">
                    <label for="grade_level" class="block text-sm font-semibold text-gray-800 mb-3 transition-colors group-focus-within:text-indigo-600">
                        {{ t('grade_level') }}
                        <span class="text-gray-400 font-normal text-xs mr-1 rtl:mr-0 rtl:ml-1">({{ t('optional') }})</span>
                    </label>
                    <input
                        id="grade_level"
                        v-model="form.grade_level"
                        type="text"
                        :placeholder="t('grade_level_placeholder')"
                        class="w-full px-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md focus:shadow-lg"
                        :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.grade_level }"
                    >
                    <div v-if="form.errors.grade_level" class="mt-2 text-sm text-red-600">{{ form.errors.grade_level }}</div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-gray-50 -mx-8 -mb-8 px-8 py-6 mt-8">
                    <div class="flex items-center justify-between">
                        <Link :href="route('admin.users.index')" class="group flex items-center px-6 py-3 text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md">
                            <svg class="w-5 h-5 mr-2 rtl:mr-0 rtl:ml-2 group-hover:-translate-x-1 rtl:group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            {{ t('cancel') }}
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="group flex items-center px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 focus:ring-4 focus:ring-indigo-300 transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-lg transform hover:scale-105 active:scale-95"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg v-else class="w-5 h-5 mr-2 rtl:mr-0 rtl:ml-2 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ form.processing ? (currentLocale === 'ar' ? 'جاري الإنشاء...' : 'Creating...') : t('create') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
