<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6 -mx-8 -mt-8 mb-8">
            <h2 class="text-xl font-semibold text-white">
                Update Password
            </h2>
            <p class="text-blue-100 mt-1">
                Ensure your account is using a long, random password to stay secure.
            </p>
        </div>

        <form @submit.prevent="updatePassword" class="space-y-8">
            <!-- Current Password Field -->
            <div class="group">
                <label for="current_password" class="block text-sm font-semibold text-gray-800 mb-3 transition-colors group-focus-within:text-blue-600">
                    Current Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <input
                        id="current_password"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        type="password"
                        placeholder="Enter current password"
                        class="w-full pl-12 pr-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md focus:shadow-lg"
                        :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.current_password }"
                        autocomplete="current-password"
                    >
                </div>
                <div v-if="form.errors.current_password" class="mt-2 flex items-center text-sm text-red-600">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ form.errors.current_password }}
                </div>
            </div>

            <!-- New Password Fields Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- New Password Field -->
                <div class="group">
                    <label for="password" class="block text-sm font-semibold text-gray-800 mb-3 transition-colors group-focus-within:text-blue-600">
                        New Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </div>
                        <input
                            id="password"
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            placeholder="Enter new password"
                            class="w-full pl-12 pr-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md focus:shadow-lg"
                            :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.password }"
                            autocomplete="new-password"
                        >
                    </div>
                    <div v-if="form.errors.password" class="mt-2 flex items-center text-sm text-red-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ form.errors.password }}
                    </div>
                </div>

                <!-- Confirm Password Field -->
                <div class="group">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-800 mb-3 transition-colors group-focus-within:text-blue-600">
                        Confirm Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            placeholder="Confirm new password"
                            class="w-full pl-12 pr-4 py-4 text-gray-900 bg-gray-50 border-0 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md focus:shadow-lg"
                            :class="{ 'ring-2 ring-red-500 bg-red-50': form.errors.password_confirmation }"
                            autocomplete="new-password"
                        >
                    </div>
                    <div v-if="form.errors.password_confirmation" class="mt-2 flex items-center text-sm text-red-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ form.errors.password_confirmation }}
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
                            Password updated successfully!
                        </div>
                    </Transition>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="group flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:ring-4 focus:ring-blue-300 transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-lg transform hover:scale-105 active:scale-95 font-semibold"
                    >
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else class="w-5 h-5 mr-2 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ form.processing ? 'Updating...' : 'Update Password' }}
                    </button>
                </div>
            </div>
        </form>
    </section>
</template>
