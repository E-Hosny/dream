<template>
    <Head title="إضافة حساب Zoom جديد" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    إضافة حساب Zoom جديد
                </h2>
                <Link :href="route('admin.zoom-accounts.index')"
                      class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-right ml-2"></i>
                    رجوع
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- معلومات تعليمية -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-500 mt-1 ml-3"></i>
                                <div>
                                    <h3 class="text-sm font-semibold text-blue-800 mb-2">كيفية الحصول على بيانات حساب Zoom</h3>
                                    <ol class="text-sm text-blue-700 space-y-1 list-decimal mr-5">
                                        <li>قم بزيارة <a href="https://marketplace.zoom.us/" target="_blank" class="underline">Zoom Marketplace</a></li>
                                        <li>أنشئ تطبيق Server-to-Server OAuth</li>
                                        <li>احصل على Account ID، Client ID، و Client Secret</li>
                                        <li>تأكد من تفعيل الصلاحيات المطلوبة للاجتماعات</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <form @submit.prevent="submit">
                            <!-- معلومات أساسية -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                                    <i class="fas fa-info-circle ml-2 text-blue-500"></i>
                                    المعلومات الأساسية
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                            اسم الحساب <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="name"
                                            v-model="form.name"
                                            type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            placeholder="مثال: حساب Zoom الرئيسي"
                                            required
                                        />
                                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                                    </div>

                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                            البريد الإلكتروني <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="email"
                                            v-model="form.email"
                                            type="email"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            placeholder="zoom@example.com"
                                            required
                                        />
                                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                        الوصف
                                    </label>
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="3"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        placeholder="وصف مختصر للحساب..."
                                    ></textarea>
                                    <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                                </div>
                            </div>

                            <!-- بيانات API -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                                    <i class="fas fa-key ml-2 text-yellow-500"></i>
                                    بيانات الاتصال (API Credentials)
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="account_id" class="block text-sm font-medium text-gray-700 mb-2">
                                            Account ID <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="account_id"
                                            v-model="form.account_id"
                                            type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 font-mono text-sm"
                                            placeholder="m8VMK4ZyRkeAN0btuHP_mA"
                                            required
                                        />
                                        <p v-if="form.errors.account_id" class="mt-1 text-sm text-red-600">{{ form.errors.account_id }}</p>
                                    </div>

                                    <div>
                                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                                            Client ID <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            id="client_id"
                                            v-model="form.client_id"
                                            type="text"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 font-mono text-sm"
                                            placeholder="A_YMIa68Rky5zPRCGHyxOw"
                                            required
                                        />
                                        <p v-if="form.errors.client_id" class="mt-1 text-sm text-red-600">{{ form.errors.client_id }}</p>
                                    </div>

                                    <div>
                                        <label for="client_secret" class="block text-sm font-medium text-gray-700 mb-2">
                                            Client Secret <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input
                                                id="client_secret"
                                                v-model="form.client_secret"
                                                :type="showSecret ? 'text' : 'password'"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 font-mono text-sm"
                                                placeholder="bUKVISRcjhcxMuViOj39hqzi5lt5z44n6"
                                                required
                                            />
                                            <button
                                                type="button"
                                                @click="showSecret = !showSecret"
                                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                            >
                                                <i :class="showSecret ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                            </button>
                                        </div>
                                        <p v-if="form.errors.client_secret" class="mt-1 text-sm text-red-600">{{ form.errors.client_secret }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- إعدادات إضافية -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                                    <i class="fas fa-cog ml-2 text-gray-500"></i>
                                    الإعدادات والقيود
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="max_meetings_per_day" class="block text-sm font-medium text-gray-700 mb-2">
                                            الحد الأقصى للاجتماعات في اليوم
                                        </label>
                                        <input
                                            id="max_meetings_per_day"
                                            v-model="form.max_meetings_per_day"
                                            type="number"
                                            min="1"
                                            max="1000"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        />
                                        <p v-if="form.errors.max_meetings_per_day" class="mt-1 text-sm text-red-600">{{ form.errors.max_meetings_per_day }}</p>
                                    </div>

                                    <div>
                                        <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-2">
                                            الحد الأقصى للمشاركين
                                        </label>
                                        <input
                                            id="max_participants"
                                            v-model="form.max_participants"
                                            type="number"
                                            min="1"
                                            max="1000"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        />
                                        <p v-if="form.errors.max_participants" class="mt-1 text-sm text-red-600">{{ form.errors.max_participants }}</p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        الميزات المتاحة
                                    </label>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        <label class="flex items-center space-x-2 space-x-reverse">
                                            <input
                                                type="checkbox"
                                                value="recording"
                                                v-model="form.features"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            />
                                            <span class="text-sm text-gray-700">تسجيل</span>
                                        </label>
                                        <label class="flex items-center space-x-2 space-x-reverse">
                                            <input
                                                type="checkbox"
                                                value="transcription"
                                                v-model="form.features"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            />
                                            <span class="text-sm text-gray-700">نسخ نصي</span>
                                        </label>
                                        <label class="flex items-center space-x-2 space-x-reverse">
                                            <input
                                                type="checkbox"
                                                value="breakout_rooms"
                                                v-model="form.features"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            />
                                            <span class="text-sm text-gray-700">غرف منفصلة</span>
                                        </label>
                                        <label class="flex items-center space-x-2 space-x-reverse">
                                            <input
                                                type="checkbox"
                                                value="polling"
                                                v-model="form.features"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            />
                                            <span class="text-sm text-gray-700">استطلاعات</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label class="flex items-center space-x-2 space-x-reverse">
                                        <input
                                            type="checkbox"
                                            v-model="form.is_active"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        />
                                        <span class="text-sm font-medium text-gray-700">تفعيل الحساب فوراً</span>
                                    </label>
                                </div>
                            </div>

                            <!-- أزرار الحفظ -->
                            <div class="flex justify-end gap-4">
                                <Link
                                    :href="route('admin.zoom-accounts.index')"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded"
                                >
                                    إلغاء
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded disabled:opacity-50"
                                >
                                    <i v-if="form.processing" class="fas fa-spinner fa-spin ml-2"></i>
                                    <i v-else class="fas fa-save ml-2"></i>
                                    {{ form.processing ? 'جاري الحفظ...' : 'حفظ الحساب' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const showSecret = ref(false);

const form = useForm({
    name: '',
    email: '',
    account_id: '',
    client_id: '',
    client_secret: '',
    description: '',
    max_meetings_per_day: 100,
    max_participants: 300,
    features: ['recording'],
    is_active: true
});

const submit = () => {
    form.post(route('admin.zoom-accounts.store'));
};
</script>

