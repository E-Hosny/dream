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
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ t('create_new_account') }}</h1>
            <p class="text-gray-600">{{ t('enter_details') }}</p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel :for="'name'" :value="t('name')" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel :for="'email'" :value="t('email')" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
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
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    :for="'password_confirmation'"
                    :value="t('confirm_password')"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    {{ t('already_registered') }}
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ t('register') }}
                </PrimaryButton>
            </div>

            <!-- Login Link -->
            <div class="mt-6 text-center">
                <span class="text-sm text-gray-600">
                    {{ currentLocale === 'en' ? 'Already have an account?' : 'لديك حساب بالفعل؟' }}
                </span>
                <Link
                    :href="route('login')"
                    class="ms-1 text-sm text-blue-600 hover:text-blue-500 underline"
                >
                    {{ t('login_here') }}
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
