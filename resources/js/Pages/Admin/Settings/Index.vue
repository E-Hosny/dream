<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'ar');
</script>

<template>
    <Head :title="currentLocale === 'ar' ? 'الإعدادات' : 'Settings'" />

    <AdminLayout>
        <div class="max-w-5xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ currentLocale === 'ar' ? 'الإعدادات' : 'Settings' }}
                </h1>
                <p class="text-gray-600 mt-2">
                    {{ currentLocale === 'ar' ? 'إدارة إعدادات النظام العامة' : 'Manage global system settings' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <Link
                    :href="route('admin.settings.general-messages.index')"
                    class="block bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md hover:border-brand/40 transition-all"
                >
                    <div class="flex items-start gap-4">
                        <div class="h-12 w-12 rounded-lg bg-brand/10 text-brand flex items-center justify-center">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h8m-8 4h5M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">
                                {{ currentLocale === 'ar' ? 'الرسائل العامة' : 'General Messages' }}
                            </h2>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ currentLocale === 'ar' ? 'إضافة رسالة لكل كورس مع فترة ظهور وصورة للطالب' : 'Create course messages with display period and image for students' }}
                            </p>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>
