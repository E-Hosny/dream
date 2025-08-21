<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');
const isRTL = computed(() => currentLocale.value === 'ar');

const switchLanguage = (locale) => {
    router.visit(`/language/${locale}`, {
        method: 'get',
        preserveState: false,
        preserveScroll: false,
        replace: true,
        onFinish: () => {
            // Force page reload to ensure all components update
            window.location.reload();
        }
    });
};
</script>

<template>
    <div
        class="relative min-h-screen bg-gradient-to-br from-indigo-50 via-white to-cyan-50"
        :dir="isRTL ? 'rtl' : 'ltr'"
    >
        <!-- Background Decorative Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400/20 to-indigo-600/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-cyan-400/20 to-blue-600/20 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-br from-indigo-400/10 to-purple-600/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Language Switcher -->
        <div class="absolute top-6 right-6 z-10 flex items-center space-x-3 rtl:space-x-reverse language-switcher">
            <span class="text-sm text-slate-600 font-medium">
                {{ currentLocale === 'en' ? 'Language' : 'اللغة' }}
            </span>
            <div class="flex items-center bg-white/80 backdrop-blur-sm rounded-lg p-1 shadow-lg border border-white/20">
                <button
                    @click="switchLanguage('en')"
                    :class="[
                        'px-4 py-2 text-sm rounded-md transition-all duration-200 font-medium',
                        currentLocale === 'en' 
                            ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md transform scale-105' 
                            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/50'
                    ]"
                >
                    EN
                </button>
                <button
                    @click="switchLanguage('ar')"
                    :class="[
                        'px-4 py-2 text-sm rounded-md transition-all duration-200 font-medium',
                        currentLocale === 'ar' 
                            ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md transform scale-105' 
                            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/50'
                    ]"
                >
                    عربي
                </button>
            </div>
        </div>

        <!-- Main Content Container -->
        <div class="relative flex min-h-screen items-center justify-center px-6 py-12">
            <div class="w-full max-w-md">
                <!-- Logo Section -->
                <div class="text-center mb-8">
                    <Link href="/" class="inline-block">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full blur opacity-30 scale-110"></div>
                            <ApplicationLogo class="relative h-16 w-16 mx-auto text-blue-600" />
                        </div>
                    </Link>
                    <h2 class="mt-4 text-2xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                        {{ currentLocale === 'en' ? 'EduDream Platform' : 'منصة إيدو دريم' }}
                    </h2>
                    <p class="mt-2 text-sm text-slate-600">
                        {{ currentLocale === 'en' ? 'Your gateway to knowledge and success' : 'بوابتك إلى المعرفة والنجاح' }}
                    </p>
                </div>

                <!-- Auth Card -->
                <div class="relative bg-white/70 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-8">
                    <!-- Decorative gradient border -->
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 via-indigo-600/20 to-cyan-600/20 rounded-2xl blur-sm opacity-50"></div>
                    
                    <!-- Content -->
                    <div class="relative">
                        <slot />
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <p class="text-xs text-slate-500">
                        {{ currentLocale === 'en' ? '© 2024 EduDream. Empowering minds, shaping futures.' : '© 2024 إيدو دريم. نمكن العقول، نشكل المستقبل.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
