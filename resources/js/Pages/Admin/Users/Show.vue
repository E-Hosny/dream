<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'ar');

const props = defineProps({
    user: Object
});

const primaryRole = computed(() => props.user.roles?.[0]?.name ?? '');
const normalizedPrimaryRole = computed(() => String(primaryRole.value).toLowerCase().trim());
const isTeacher = computed(() => normalizedPrimaryRole.value === 'teacher');
const isStudent = computed(() => normalizedPrimaryRole.value === 'student');

const roleLabel = (name) => {
    const n = String(name ?? '').toLowerCase().trim();
    if (currentLocale.value === 'ar') {
        if (n === 'admin') return 'مدير';
        if (n === 'teacher') return 'معلم';
        if (n === 'student') return 'طالب';
    }
    if (n === 'admin') return 'Admin';
    if (n === 'teacher') return 'Teacher';
    if (n === 'student') return 'Student';
    return String(name ?? '') || '—';
};

const t = (key) => {
    const translations = {
        en: {
            user_profile: 'User profile',
            back: 'Back to list',
            edit: 'Edit',
            name: 'Name',
            email: 'Email',
            phone: 'Mobile number',
            role: 'Role',
            specialty: 'Specialty',
            grade_level: 'Grade / class',
            not_set: 'Not set',
            courses_teaching: 'Courses teaching',
            courses_enrolled: 'Enrolled courses'
        },
        ar: {
            user_profile: 'ملف المستخدم',
            back: 'العودة للقائمة',
            edit: 'تعديل',
            name: 'الاسم',
            email: 'البريد الإلكتروني',
            phone: 'رقم الجوال',
            role: 'الدور',
            specialty: 'التخصص',
            grade_level: 'الصف الدراسي',
            not_set: 'غير محدد',
            courses_teaching: 'الكورسات التي يدرّسها',
            courses_enrolled: 'الكورسات المسجّل فيها'
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

const display = (value) => (value && String(value).trim() !== '' ? value : null);
</script>

<template>
    <Head :title="user.name" />

    <AdminLayout>
        <div class="mb-8 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ t('user_profile') }}</h1>
                <p class="text-gray-600 mt-1">{{ user.name }}</p>
            </div>
            <div class="flex gap-3">
                <Link
                    :href="route('admin.users.edit', user.id)"
                    class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg hover:bg-indigo-700 transition-colors"
                >
                    {{ t('edit') }}
                </Link>
                <Link
                    :href="route('admin.users.index')"
                    class="bg-gray-600 text-white px-5 py-2.5 rounded-lg hover:bg-gray-700 transition-colors"
                >
                    {{ t('back') }}
                </Link>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border-0 overflow-hidden">
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">{{ t('name') }}</p>
                        <p class="text-lg text-gray-900">{{ user.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">{{ t('email') }}</p>
                        <p class="text-lg text-gray-900">{{ user.email }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">{{ t('phone') }}</p>
                        <p class="text-lg text-gray-900">{{ display(user.phone) ?? t('not_set') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">{{ t('role') }}</p>
                        <span
                            :class="[
                                'inline-flex px-3 py-1 text-sm font-semibold rounded-full',
                                normalizedPrimaryRole === 'admin' ? 'bg-red-100 text-red-800' :
                                normalizedPrimaryRole === 'teacher' ? 'bg-green-100 text-green-800' :
                                'bg-blue-100 text-blue-800'
                            ]"
                        >
                            {{ roleLabel(normalizedPrimaryRole) }}
                        </span>
                    </div>
                    <div v-if="isTeacher">
                        <p class="text-sm font-medium text-gray-500 mb-1">{{ t('specialty') }}</p>
                        <p class="text-lg text-gray-900">{{ display(user.specialty) ?? t('not_set') }}</p>
                    </div>
                    <div v-if="isStudent">
                        <p class="text-sm font-medium text-gray-500 mb-1">{{ t('grade_level') }}</p>
                        <p class="text-lg text-gray-900">{{ display(user.grade_level) ?? t('not_set') }}</p>
                    </div>
                </div>

                <div v-if="user.teaching_courses?.length" class="border-t pt-6">
                    <p class="text-sm font-medium text-gray-500 mb-3">{{ t('courses_teaching') }}</p>
                    <ul class="list-disc list-inside text-gray-800 space-y-1">
                        <li v-for="c in user.teaching_courses" :key="c.id">{{ c.title ?? c.name }}</li>
                    </ul>
                </div>
                <div v-if="user.enrolled_courses?.length" class="border-t pt-6">
                    <p class="text-sm font-medium text-gray-500 mb-3">{{ t('courses_enrolled') }}</p>
                    <ul class="list-disc list-inside text-gray-800 space-y-1">
                        <li v-for="c in user.enrolled_courses" :key="c.id">{{ c.title ?? c.name }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
