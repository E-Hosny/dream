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
const currentLocale = computed(() => page.props.locale || 'en');

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
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
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

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Email Field -->
            <div class="space-y-2">
                <InputLabel :for="'email'" :value="t('email')" class="text-slate-700 font-semibold" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none rtl:left-auto rtl:right-0 rtl:pr-3">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                    </div>
                    <TextInput
                        id="email"
                        type="email"
                        class="pl-10 rtl:pl-4 rtl:pr-10 block w-full rounded-xl border-slate-200 bg-slate-50/50 focus:border-blue-500 focus:ring-blue-500 focus:bg-white transition-all duration-200"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        :placeholder="t('email')"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Password Field -->
            <div class="space-y-2">
                <InputLabel :for="'password'" :value="t('password')" class="text-slate-700 font-semibold" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none rtl:left-auto rtl:right-0 rtl:pr-3">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <TextInput
                        id="password"
                        type="password"
                        class="pl-10 rtl:pl-4 rtl:pr-10 block w-full rounded-xl border-slate-200 bg-slate-50/50 focus:border-blue-500 focus:ring-blue-500 focus:bg-white transition-all duration-200"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        :placeholder="t('password')"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
                <label class="flex items-center group cursor-pointer">
                    <Checkbox 
                        name="remember" 
                        v-model:checked="form.remember" 
                        class="rounded-md border-slate-300 text-blue-600 focus:ring-blue-500"
                    />
                    <span class="ms-2 text-sm text-slate-600 group-hover:text-slate-800 transition-colors">
                        {{ t('remember_me') }}
                    </span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors"
                >
                    {{ t('forgot_password') }}
                </Link>
            </div>

            <!-- Submit Button -->
            <div class="space-y-4">
                <PrimaryButton
                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:ring-4 focus:ring-blue-500/50"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ currentLocale === 'en' ? 'Signing in...' : 'جارٍ تسجيل الدخول...' }}
                    </span>
                    <span v-else>{{ t('log_in') }}</span>
                </PrimaryButton>

                <!-- Divider -->
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-slate-500 font-medium">
                            {{ currentLocale === 'en' ? 'New to EduDream?' : 'جديد على إيدو دريم؟' }}
                        </span>
                    </div>
                </div>

                <!-- Register Link -->
                <div class="text-center">
                    <Link
                        :href="route('register')"
                        class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors group"
                    >
                        {{ currentLocale === 'en' ? 'Create an account' : 'إنشاء حساب جديد' }}
                        <svg class="ml-1 rtl:ml-0 rtl:mr-1 w-4 h-4 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>
