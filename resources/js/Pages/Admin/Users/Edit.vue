<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'ar');

const props = defineProps({
    user: Object,
    roles: { type: Array, default: () => [] }
});

const initialRole = props.user.roles?.[0]?.name ?? 'student';

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    notification_email: props.user.notification_email ?? '',
    phone: props.user.phone ?? '',
    password: '',
    password_confirmation: '',
    role: initialRole,
    specialty: props.user.specialty ?? '',
    grade_level: props.user.grade_level ?? ''
});

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
        form.role = names[0];
    },
    { immediate: true, deep: true }
);

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

const t = (key) => {
    const translations = {
        en: {
            edit_user: 'Edit User',
            back_to_users: 'Back to Users',
            name: 'Full Name',
            email: 'Email Address',
            notification_email: 'Notification email',
            notification_email_help: 'All system emails will be sent here. Leave empty to use the login email.',
            phone: 'Mobile number',
            password: 'New password',
            confirm_password: 'Confirm new password',
            role: 'User Role',
            save: 'Save changes',
            cancel: 'Cancel',
            name_placeholder: 'Enter full name',
            email_placeholder: 'Enter email address',
            notification_email_placeholder: 'Enter notification email',
            password_placeholder: 'Leave blank to keep current',
            confirm_password_placeholder: 'Confirm new password',
            phone_placeholder: 'Optional',
            specialty: 'Specialty',
            specialty_placeholder: 'Optional',
            grade_level: 'Grade / class',
            grade_level_placeholder: 'Optional',
            optional: 'Optional',
            password_hint: 'Leave blank if you do not want to change the password'
        },
        ar: {
            edit_user: 'تعديل مستخدم',
            back_to_users: 'العودة للمستخدمين',
            name: 'الاسم الكامل',
            email: 'البريد الإلكتروني',
            notification_email: 'بريد الإشعارات',
            notification_email_help: 'تُرسل كل إشعارات النظام إلى هذا البريد. اتركه فارغاً لاستخدام بريد الدخول.',
            phone: 'رقم الجوال',
            password: 'كلمة مرور جديدة',
            confirm_password: 'تأكيد كلمة المرور الجديدة',
            role: 'دور المستخدم',
            save: 'حفظ التغييرات',
            cancel: 'إلغاء',
            name_placeholder: 'أدخل الاسم الكامل',
            email_placeholder: 'أدخل البريد الإلكتروني',
            notification_email_placeholder: 'أدخل بريد الإشعارات',
            password_placeholder: 'اتركه فارغاً للإبقاء على الحالية',
            confirm_password_placeholder: 'أكد كلمة المرور الجديدة',
            phone_placeholder: 'اختياري',
            specialty: 'التخصص',
            specialty_placeholder: 'اختياري',
            grade_level: 'الصف الدراسي',
            grade_level_placeholder: 'اختياري',
            optional: 'اختياري',
            password_hint: 'اترك الحقل فارغاً إذا لم ترد تغيير كلمة المرور'
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

const submit = () => {
    form.put(route('admin.users.update', props.user.id));
};
</script>

<template>
    <Head :title="t('edit_user')" />

    <AdminLayout>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ t('edit_user') }}</h1>
                <p class="text-gray-600 mt-2">{{ user.name }}</p>
            </div>
            <Link :href="route('admin.users.index')" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-md hover:shadow-lg">
                {{ t('back_to_users') }}
            </Link>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border-0 overflow-visible backdrop-blur-sm bg-white/95">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
                <h2 class="text-xl font-semibold text-white">{{ currentLocale === 'ar' ? 'بيانات المستخدم' : 'User information' }}</h2>
            </div>
            <form @submit.prevent="submit" class="p-8 space-y-8">
                <div class="group">
                    <label for="name" class="block text-sm font-semibold text-gray-800 mb-3">{{ t('name') }}</label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        :placeholder="t('name_placeholder')"
                        class="w-full px-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm"
                        :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.name }"
                        required
                    >
                    <div v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</div>
                </div>

                <div class="group">
                    <label for="email" class="block text-sm font-semibold text-gray-800 mb-3">{{ t('email') }}</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        :placeholder="t('email_placeholder')"
                        class="w-full px-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm"
                        :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.email }"
                        required
                    >
                    <div v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{ form.errors.email }}</div>
                </div>

                <div v-if="isTeacher || isStudent" class="group">
                    <label for="notification_email" class="block text-sm font-semibold text-gray-800 mb-3">
                        {{ t('notification_email') }}
                        <span class="text-gray-400 font-normal text-xs mr-1 rtl:mr-0 rtl:ml-1">({{ t('optional') }})</span>
                    </label>
                    <input
                        id="notification_email"
                        v-model="form.notification_email"
                        type="email"
                        :placeholder="t('notification_email_placeholder')"
                        class="w-full px-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm"
                        :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.notification_email }"
                    >
                    <p class="mt-2 text-xs text-gray-500">{{ t('notification_email_help') }}</p>
                    <div v-if="form.errors.notification_email" class="mt-2 text-sm text-red-600">{{ form.errors.notification_email }}</div>
                </div>

                <div class="group">
                    <label for="phone" class="block text-sm font-semibold text-gray-800 mb-3">
                        {{ t('phone') }}
                        <span class="text-gray-400 font-normal text-xs mr-1 rtl:mr-0 rtl:ml-1">({{ t('optional') }})</span>
                    </label>
                    <input
                        id="phone"
                        v-model="form.phone"
                        type="text"
                        autocomplete="tel"
                        :placeholder="t('phone_placeholder')"
                        class="w-full px-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm"
                        :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.phone }"
                    >
                    <div v-if="form.errors.phone" class="mt-2 text-sm text-red-600">{{ form.errors.phone }}</div>
                </div>

                <p class="text-sm text-gray-500">{{ t('password_hint') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="group">
                        <label for="password" class="block text-sm font-semibold text-gray-800 mb-3">{{ t('password') }}</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            :placeholder="t('password_placeholder')"
                            class="w-full px-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm"
                            :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.password }"
                        >
                        <div v-if="form.errors.password" class="mt-2 text-sm text-red-600">{{ form.errors.password }}</div>
                    </div>
                    <div class="group">
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-800 mb-3">{{ t('confirm_password') }}</label>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            :placeholder="t('confirm_password_placeholder')"
                            class="w-full px-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 shadow-sm"
                            :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.password_confirmation }"
                        >
                        <div v-if="form.errors.password_confirmation" class="mt-2 text-sm text-red-600">{{ form.errors.password_confirmation }}</div>
                    </div>
                </div>

                <div class="group">
                    <label for="role" class="block text-sm font-semibold text-gray-800 mb-3">{{ t('role') }}</label>
                    <select
                        id="role"
                        v-model="form.role"
                        :dir="currentLocale === 'ar' ? 'rtl' : 'ltr'"
                        required
                        class="block w-full min-h-[3.25rem] px-4 py-3 text-base leading-normal text-gray-900 bg-white border border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 [color-scheme:light]"
                        :class="{ 'ring-2 ring-red-500 border-red-300': form.errors.role }"
                    >
                        <option v-if="!roles.length" value="" disabled>
                            {{ currentLocale === 'ar' ? 'لا توجد أدوار' : 'No roles' }}
                        </option>
                        <option v-for="role in roles" :key="role.id" :value="role.name">
                            {{ roleDisplayName(role.name) }}
                        </option>
                    </select>
                    <div v-if="form.errors.role" class="mt-2 text-sm text-red-600">{{ form.errors.role }}</div>
                </div>

                <div v-if="isTeacher" class="group">
                    <label for="specialty" class="block text-sm font-semibold text-gray-800 mb-3">
                        {{ t('specialty') }}
                        <span class="text-gray-400 font-normal text-xs mr-1 rtl:mr-0 rtl:ml-1">({{ t('optional') }})</span>
                    </label>
                    <input
                        id="specialty"
                        v-model="form.specialty"
                        type="text"
                        :placeholder="t('specialty_placeholder')"
                        class="w-full px-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 shadow-sm"
                        :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.specialty }"
                    >
                    <div v-if="form.errors.specialty" class="mt-2 text-sm text-red-600">{{ form.errors.specialty }}</div>
                </div>

                <div v-if="isStudent" class="group">
                    <label for="grade_level" class="block text-sm font-semibold text-gray-800 mb-3">
                        {{ t('grade_level') }}
                        <span class="text-gray-400 font-normal text-xs mr-1 rtl:mr-0 rtl:ml-1">({{ t('optional') }})</span>
                    </label>
                    <input
                        id="grade_level"
                        v-model="form.grade_level"
                        type="text"
                        :placeholder="t('grade_level_placeholder')"
                        class="w-full px-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-indigo-500 shadow-sm"
                        :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.grade_level }"
                    >
                    <div v-if="form.errors.grade_level" class="mt-2 text-sm text-red-600">{{ form.errors.grade_level }}</div>
                </div>

                <div class="bg-gray-50 -mx-8 -mb-8 px-8 py-6 mt-8 flex items-center justify-between">
                    <Link :href="route('admin.users.index')" class="px-6 py-3 text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50">
                        {{ t('cancel') }}
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 disabled:opacity-50"
                    >
                        {{ form.processing ? (currentLocale === 'ar' ? 'جاري الحفظ...' : 'Saving...') : t('save') }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
