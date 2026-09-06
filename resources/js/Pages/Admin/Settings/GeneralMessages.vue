<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import InputError from '@/Components/InputError.vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'ar');

const props = defineProps({
    courses: Array,
    announcements: Array,
});

const form = useForm({
    course_id: '',
    title: '',
    message: '',
    starts_at: '',
    ends_at: '',
    image: null,
});

const submit = () => {
    form.post(route('admin.settings.general-messages.store'));
};

const handleImageChange = (event) => {
    const file = event.target.files[0];
    form.image = file || null;
};

const formatDateTime = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleString(currentLocale.value === 'ar' ? 'ar-SA' : 'en-US');
};

const deleteAnnouncement = (announcementId) => {
    if (!confirm(currentLocale.value === 'ar' ? 'هل تريد حذف هذه الرسالة؟' : 'Delete this message?')) {
        return;
    }

    form.delete(route('admin.settings.general-messages.destroy', announcementId));
};

const courseName = (course) => {
    return currentLocale.value === 'ar' ? (course.title_ar || course.title) : (course.title || course.title_ar);
};
</script>

<template>
    <Head :title="currentLocale === 'ar' ? 'الرسائل العامة' : 'General Messages'" />

    <AdminLayout>
        <div class="max-w-7xl mx-auto">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ currentLocale === 'ar' ? 'الرسائل العامة' : 'General Messages' }}
                    </h1>
                    <p class="text-gray-600 mt-1">
                        {{ currentLocale === 'ar' ? 'إدارة رسائل الكورسات المعروضة للطالب' : 'Manage course messages shown to students' }}
                    </p>
                </div>
                <Link :href="route('admin.settings.index')" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800">
                    {{ currentLocale === 'ar' ? 'العودة للإعدادات' : 'Back to Settings' }}
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        {{ currentLocale === 'ar' ? 'إضافة رسالة جديدة' : 'Add New Message' }}
                    </h2>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ currentLocale === 'ar' ? 'الكورس' : 'Course' }}
                            </label>
                            <select v-model="form.course_id" class="w-full rounded-lg border-gray-300 focus:border-brand focus:ring-brand">
                                <option value="">{{ currentLocale === 'ar' ? 'اختر الكورس' : 'Select course' }}</option>
                                <option v-for="course in courses" :key="course.id" :value="course.id">{{ courseName(course) }}</option>
                            </select>
                            <InputError :message="form.errors.course_id" class="mt-1" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ currentLocale === 'ar' ? 'عنوان الرسالة' : 'Message Title' }}
                            </label>
                            <input v-model="form.title" type="text" class="w-full rounded-lg border-gray-300 focus:border-brand focus:ring-brand" />
                            <InputError :message="form.errors.title" class="mt-1" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ currentLocale === 'ar' ? 'نص الرسالة' : 'Message' }}
                            </label>
                            <textarea v-model="form.message" rows="4" class="w-full rounded-lg border-gray-300 focus:border-brand focus:ring-brand"></textarea>
                            <InputError :message="form.errors.message" class="mt-1" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ currentLocale === 'ar' ? 'تاريخ/وقت البداية' : 'Start Date/Time' }}
                                </label>
                                <input v-model="form.starts_at" type="datetime-local" class="w-full rounded-lg border-gray-300 focus:border-brand focus:ring-brand" />
                                <InputError :message="form.errors.starts_at" class="mt-1" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ currentLocale === 'ar' ? 'تاريخ/وقت النهاية' : 'End Date/Time' }}
                                </label>
                                <input v-model="form.ends_at" type="datetime-local" class="w-full rounded-lg border-gray-300 focus:border-brand focus:ring-brand" />
                                <InputError :message="form.errors.ends_at" class="mt-1" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ currentLocale === 'ar' ? 'صورة الرسالة (اختياري)' : 'Message Image (Optional)' }}
                            </label>
                            <input type="file" accept="image/*" @change="handleImageChange" class="w-full rounded-lg border-gray-300 focus:border-brand focus:ring-brand" />
                            <InputError :message="form.errors.image" class="mt-1" />
                        </div>

                        <button :disabled="form.processing" type="submit" class="w-full px-4 py-2.5 bg-brand text-white rounded-lg hover:bg-brand-dark disabled:opacity-50">
                            {{ currentLocale === 'ar' ? 'حفظ الرسالة' : 'Save Message' }}
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        {{ currentLocale === 'ar' ? 'الرسائل الحالية' : 'Current Messages' }}
                    </h2>

                    <div v-if="announcements.length === 0" class="text-sm text-gray-500">
                        {{ currentLocale === 'ar' ? 'لا توجد رسائل حتى الآن' : 'No messages yet' }}
                    </div>

                    <div v-else class="space-y-4 max-h-[680px] overflow-auto pr-1">
                        <div v-for="item in announcements" :key="item.id" class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ item.title }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ currentLocale === 'ar' ? 'الكورس:' : 'Course:' }} {{ courseName(item.course) }}
                                    </p>
                                </div>
                                <button @click="deleteAnnouncement(item.id)" class="text-red-600 hover:text-red-800 text-sm">
                                    {{ currentLocale === 'ar' ? 'حذف' : 'Delete' }}
                                </button>
                            </div>

                            <p class="text-sm text-gray-700 mt-3 whitespace-pre-line">{{ item.message }}</p>

                            <img v-if="item.image_url" :src="item.image_url" class="mt-3 w-full max-h-44 object-cover rounded-lg border border-gray-200" />

                            <div class="mt-3 text-xs text-gray-500 space-y-1">
                                <p>{{ currentLocale === 'ar' ? 'من:' : 'From:' }} {{ formatDateTime(item.starts_at) }}</p>
                                <p>{{ currentLocale === 'ar' ? 'إلى:' : 'To:' }} {{ formatDateTime(item.ends_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
