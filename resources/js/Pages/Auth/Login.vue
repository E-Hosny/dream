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
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ t('welcome_back') }}</h1>
            <p class="text-gray-600">{{ t('login_to_account') }}</p>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel :for="'email'" :value="t('email')" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel :for="'password'" :value="t('password')" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ t('remember_me') }}</span>
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    {{ t('forgot_password') }}
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ t('log_in') }}
                </PrimaryButton>
            </div>

            <!-- Register Link -->
            <div class="mt-6 text-center">
                <span class="text-sm text-gray-600">
                    {{ currentLocale === 'en' ? "Don't have an account?" : "ليس لديك حساب؟" }}
                </span>
                <Link
                    :href="route('register')"
                    class="ms-1 text-sm text-blue-600 hover:text-blue-500 underline"
                >
                    {{ currentLocale === 'en' ? 'Register here' : 'سجل هنا' }}
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
