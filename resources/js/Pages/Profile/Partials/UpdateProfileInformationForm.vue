<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-6 -mx-8 -mt-8 mb-8">
            <h2 class="text-xl font-semibold text-white">
                Profile Information
            </h2>
            <p class="text-emerald-100 mt-1">
                Update your account's profile information and email address.
            </p>
        </div>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="space-y-8"
        >
            <!-- Name Field -->
            <div class="group">
                <label for="name" class="block text-sm font-semibold text-gray-800 mb-3 transition-colors group-focus-within:text-emerald-600">
                    Name
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        placeholder="Enter your full name"
                        class="w-full pl-12 pr-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md focus:shadow-lg"
                        :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.name }"
                        required
                        autofocus
                        autocomplete="name"
                    >
                </div>
                <div v-if="form.errors.name" class="mt-2 flex items-center text-sm text-red-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ form.errors.name }}
                </div>
            </div>

            <!-- Email Field -->
            <div class="group">
                <label for="email" class="block text-sm font-semibold text-gray-800 mb-3 transition-colors group-focus-within:text-emerald-600">
                    Email Address
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        placeholder="Enter your email address"
                        class="w-full pl-12 pr-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md focus:shadow-lg"
                        :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.email }"
                        required
                        autocomplete="username"
                    >
                </div>
                <div v-if="form.errors.email" class="mt-2 flex items-center text-sm text-red-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ form.errors.email }}
                </div>
            </div>

            <!-- Email Verification Notice -->
            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm text-yellow-800">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="font-medium text-yellow-900 underline hover:text-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors"
                            >
                                Click here to re-send the verification email.
                            </Link>
                        </p>

                        <div
                            v-show="status === 'verification-link-sent'"
                            class="mt-2 text-sm font-medium text-green-700 bg-green-50 px-3 py-2 rounded-lg"
                        >
                            A new verification link has been sent to your email address.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-gray-50 -mx-8 -mb-8 px-8 py-6 mt-8">
                <div class="flex items-center justify-between">
                    <Transition
                        enter-active-class="transition ease-in-out duration-200"
                        enter-from-class="opacity-0 scale-95"
                        leave-active-class="transition ease-in-out duration-200"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div v-if="form.recentlySuccessful" class="flex items-center text-green-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Profile updated successfully!
                        </div>
                    </Transition>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="group flex items-center px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 focus:ring-4 focus:ring-emerald-300 transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-lg transform hover:scale-105 active:scale-95 font-semibold"
                    >
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else class="w-5 h-5 mr-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </button>
                </div>
            </div>
        </form>
    </section>
</template>
