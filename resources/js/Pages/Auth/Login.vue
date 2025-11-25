<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'ar');

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            login: 'Login',
            email: 'Email',
            password: 'Password',
            remember_me: 'Remember me',
            forgot_password: 'Forgot your password?',
            log_in: 'Log in',
            login_to_account: 'Login to your account',
            welcome_back: 'Welcome back!',
            enter_credentials: 'Enter your credentials to access your account',
        },
        ar: {
            login: 'تسجيل الدخول',
            email: 'البريد الإلكتروني',
            password: 'كلمة المرور',
            remember_me: 'تذكرني',
            forgot_password: 'نسيت كلمة المرور؟',
            log_in: 'تسجيل الدخول',
            login_to_account: 'تسجيل الدخول إلى حسابك',
            welcome_back: 'مرحباً بعودتك!',
            enter_credentials: 'أدخل بياناتك للوصول إلى حسابك',
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};
</script>

<template>
    <GuestLayout>
        <Head :title="t('login')" />

        <!-- Header Section -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent mb-2">
                {{ t('welcome_back') }}
            </h1>
            <p class="text-slate-600 font-medium">{{ t('login_to_account') }}</p>
        </div>

        <!-- Status Message -->
        <div v-if="status" class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-green-800">{{ status }}</span>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-8">
            <!-- Email Field -->
            <div class="group">
                <label for="email" class="block text-sm font-semibold text-gray-800 mb-3 transition-colors group-focus-within:text-[#18b596]">
                    {{ t('email') }}
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 flex items-center pl-4 rtl:pl-0 rtl:pr-4 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-[#18b596] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        :placeholder="t('email')"
                        class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#18b596] focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md focus:shadow-lg"
                        :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.email }"
                        required
                        autofocus
                        autocomplete="username"
                    >
                </div>
                <div v-if="form.errors.email" class="mt-2 flex items-center text-sm text-red-600">
                    <svg class="w-4 h-4 mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ form.errors.email }}
                </div>
            </div>

            <!-- Password Field -->
            <div class="group">
                <label for="password" class="block text-sm font-semibold text-gray-800 mb-3 transition-colors group-focus-within:text-[#18b596]">
                    {{ t('password') }}
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 flex items-center pl-4 rtl:pl-0 rtl:pr-4 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-[#18b596] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        :placeholder="t('password')"
                        class="w-full pl-12 rtl:pl-4 rtl:pr-12 pr-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-[#18b596] focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md focus:shadow-lg"
                        :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.password }"
                        required
                        autocomplete="current-password"
                    >
                </div>
                <div v-if="form.errors.password" class="mt-2 flex items-center text-sm text-red-600">
                    <svg class="w-4 h-4 mr-1 rtl:mr-0 rtl:ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ form.errors.password }}
                </div>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <label class="flex items-center group cursor-pointer">
                    <Checkbox 
                        name="remember" 
                        v-model:checked="form.remember" 
                        class="rounded-md border-slate-300 text-[#18b596] focus:ring-[#18b596]"
                    />
                    <span class="ms-2 text-sm text-slate-600 group-hover:text-slate-800 transition-colors">
                        {{ t('remember_me') }}
                    </span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm font-medium text-[#18b596] hover:text-[#16a085] transition-colors"
                >
                    {{ t('forgot_password') }}
                </Link>
            </div>

            <!-- Submit Button -->
            <div class="space-y-4">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="group w-full flex items-center justify-center px-8 py-4 bg-[#18b596] text-white rounded-xl hover:bg-[#16a085] focus:ring-4 focus:ring-[#18b596]/30 transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-lg transform hover:scale-105 active:scale-95 font-semibold text-lg"
                >
                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="w-5 h-5 mr-2 rtl:mr-0 rtl:ml-2 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    {{ form.processing ? (currentLocale === 'ar' ? 'جاري تسجيل الدخول...' : 'Signing in...') : t('log_in') }}
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
