<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            register: 'Register',
            name: 'Name',
            email: 'Email',
            password: 'Password',
            confirm_password: 'Confirm Password',
            register_user: 'Register User',
            create_new_account: 'Create a new account',
            enter_details: 'Enter your details to create your account',
            already_registered: 'Already registered?',
            login_here: 'Login here',
        },
        ar: {
            register: 'التسجيل',
            name: 'الاسم',
            email: 'البريد الإلكتروني',
            password: 'كلمة المرور',
            confirm_password: 'تأكيد كلمة المرور',
            register_user: 'تسجيل مستخدم جديد',
            create_new_account: 'إنشاء حساب جديد',
            enter_details: 'أدخل بياناتك لإنشاء حسابك',
            already_registered: 'لديك حساب بالفعل؟',
            login_here: 'سجل دخول هنا',
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};
</script>

<template>
    <GuestLayout>
        <Head :title="t('register')" />

        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent mb-2">
                {{ t('create_new_account') }}
            </h1>
            <p class="text-slate-600 font-medium">{{ t('enter_details') }}</p>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Name Field -->
            <div class="space-y-2">
                <InputLabel :for="'name'" :value="t('name')" class="text-slate-700 font-semibold" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none rtl:left-auto rtl:right-0 rtl:pr-3">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <TextInput
                        id="name"
                        type="text"
                        class="pl-10 rtl:pl-4 rtl:pr-10 block w-full rounded-xl border-slate-200 bg-slate-50/50 focus:border-emerald-500 focus:ring-emerald-500 focus:bg-white transition-all duration-200"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        :placeholder="t('name')"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

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
                        class="pl-10 rtl:pl-4 rtl:pr-10 block w-full rounded-xl border-slate-200 bg-slate-50/50 focus:border-emerald-500 focus:ring-emerald-500 focus:bg-white transition-all duration-200"
                        v-model="form.email"
                        required
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
                        class="pl-10 rtl:pl-4 rtl:pr-10 block w-full rounded-xl border-slate-200 bg-slate-50/50 focus:border-emerald-500 focus:ring-emerald-500 focus:bg-white transition-all duration-200"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        :placeholder="t('password')"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Confirm Password Field -->
            <div class="space-y-2">
                <InputLabel :for="'password_confirmation'" :value="t('confirm_password')" class="text-slate-700 font-semibold" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none rtl:left-auto rtl:right-0 rtl:pr-3">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="pl-10 rtl:pl-4 rtl:pr-10 block w-full rounded-xl border-slate-200 bg-slate-50/50 focus:border-emerald-500 focus:ring-emerald-500 focus:bg-white transition-all duration-200"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        :placeholder="t('confirm_password')"
                    />
                </div>
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <!-- Terms and Conditions -->
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                <div class="flex items-start space-x-3 rtl:space-x-reverse">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-slate-600">
                            {{ currentLocale === 'en' 
                                ? 'By creating an account, you agree to our Terms of Service and Privacy Policy. We\'re committed to protecting your educational journey.' 
                                : 'بإنشاء حساب، فإنك توافق على شروط الخدمة وسياسة الخصوصية. نحن ملتزمون بحماية رحلتك التعليمية.' 
                            }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="space-y-4">
                <PrimaryButton
                    class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:ring-4 focus:ring-emerald-500/50"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ currentLocale === 'en' ? 'Creating account...' : 'جارٍ إنشاء الحساب...' }}
                    </span>
                    <span v-else class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        {{ t('register') }}
                    </span>
                </PrimaryButton>

                <!-- Divider -->
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-slate-500 font-medium">
                            {{ currentLocale === 'en' ? 'Already part of EduDream?' : 'جزء من إيدو دريم بالفعل؟' }}
                        </span>
                    </div>
                </div>

                <!-- Login Link -->
                <div class="text-center">
                    <Link
                        :href="route('login')"
                        class="inline-flex items-center text-sm font-medium text-emerald-600 hover:text-emerald-500 transition-colors group"
                    >
                        {{ currentLocale === 'en' ? 'Sign in to your account' : 'تسجيل الدخول إلى حسابك' }}
                        <svg class="ml-1 rtl:ml-0 rtl:mr-1 w-4 h-4 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </Link>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>
