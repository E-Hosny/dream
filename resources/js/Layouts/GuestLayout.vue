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
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
        :dir="isRTL ? 'rtl' : 'ltr'"
    >
        <!-- Language Switcher -->
        <div class="absolute top-4 right-4 flex items-center space-x-2 rtl:space-x-reverse language-switcher">
            <span class="text-sm text-gray-600 font-medium">
                {{ currentLocale === 'en' ? 'Language' : 'اللغة' }}
            </span>
            <button
                @click="switchLanguage('en')"
                :class="[
                    'px-3 py-1 text-sm rounded-md transition-colors font-medium',
                    currentLocale === 'en' 
                        ? 'bg-blue-500 text-white shadow-md' 
                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300 hover:shadow-sm'
                ]"
            >
                EN
            </button>
            <button
                @click="switchLanguage('ar')"
                :class="[
                    'px-3 py-1 text-sm rounded-md transition-colors font-medium',
                    currentLocale === 'ar' 
                        ? 'bg-blue-500 text-white shadow-md' 
                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300 hover:shadow-sm'
                ]"
            >
                عربي
            </button>
        </div>

        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
