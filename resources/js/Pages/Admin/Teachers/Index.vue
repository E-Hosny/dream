<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            {{ currentLocale === 'ar' ? 'إدارة المعلمين' : 'Teachers Management' }}
                        </h1>
                        <p class="text-gray-600">
                            {{ currentLocale === 'ar' ? 'إدارة المعلمين وربطهم بحسابات Zoom' : 'Manage teachers and link them to Zoom accounts' }}
                        </p>
                    </div>
                    <div class="flex space-x-3 rtl:space-x-reverse">
                        <button
                            @click="showZoomAccountsModal = true"
                            class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors"
                        >
                            {{ currentLocale === 'ar' ? 'إدارة حسابات Zoom' : 'Manage Zoom Accounts' }}
                        </button>
                        <button
                            @click="showCreateModal = true"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                        >
                            {{ currentLocale === 'ar' ? 'إضافة معلم جديد' : 'Add New Teacher' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input
                            v-model="filters.search"
                            type="text"
                            :placeholder="currentLocale === 'ar' ? 'البحث عن معلم...' : 'Search for teacher...'"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <div class="flex gap-2">
                        <button
                            @click="applyFilters"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                        >
                            {{ currentLocale === 'ar' ? 'بحث' : 'Search' }}
                        </button>
                        <button
                            @click="clearFilters"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors"
                        >
                            {{ currentLocale === 'ar' ? 'مسح' : 'Clear' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Teachers List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ currentLocale === 'ar' ? 'المعلم' : 'Teacher' }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ currentLocale === 'ar' ? 'البريد الإلكتروني' : 'Email' }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ currentLocale === 'ar' ? 'الكورسات' : 'Courses' }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ currentLocale === 'ar' ? 'حساب Zoom' : 'Zoom Account' }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ currentLocale === 'ar' ? 'تاريخ الإنشاء' : 'Created At' }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ currentLocale === 'ar' ? 'الإجراءات' : 'Actions' }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="teacher in teachers.data" :key="teacher.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                <span class="text-purple-600 font-semibold text-lg">
                                                    {{ teacher.name.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">{{ teacher.name }}</div>
                                            <div class="text-sm text-gray-500">{{ teacher.roles[0]?.name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ teacher.email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ teacher.teaching_courses?.length || 0 }} 
                                        {{ currentLocale === 'ar' ? 'كورس' : 'courses' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div v-if="teacher.zoom_account" class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                <svg class="h-4 w-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="mr-3">
                                            <div class="text-sm font-medium text-gray-900">{{ teacher.zoom_account.name }}</div>
                                            <div class="text-sm text-gray-500">{{ teacher.zoom_account.email }}</div>
                                        </div>
                                        <button
                                            @click="unlinkZoomAccount(teacher)"
                                            class="text-red-600 hover:text-red-800 text-sm"
                                            :title="currentLocale === 'ar' ? 'إلغاء الربط' : 'Unlink'"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div v-else class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="mr-3">
                                            <div class="text-sm text-gray-500">
                                                {{ currentLocale === 'ar' ? 'غير مرتبط' : 'Not linked' }}
                                            </div>
                                        </div>
                                                                                    <button
                                                @click="openLinkModal(teacher)"
                                                class="text-blue-600 hover:text-blue-800 text-sm px-2 py-1 border border-blue-300 rounded hover:bg-blue-50"
                                            >
                                                {{ currentLocale === 'ar' ? 'ربط' : 'Link' }}
                                            </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(teacher.created_at) }}
                                </td>
                                                                 <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                     <div class="flex space-x-2 rtl:space-x-reverse">
                                         <button
                                             @click="editTeacher(teacher)"
                                             class="text-blue-600 hover:text-blue-900"
                                         >
                                             {{ currentLocale === 'ar' ? 'تعديل' : 'Edit' }}
                                         </button>
                                         <button
                                             @click="deleteTeacher(teacher)"
                                             class="text-red-600 hover:text-red-900"
                                         >
                                             {{ currentLocale === 'ar' ? 'حذف' : 'Delete' }}
                                         </button>
                                         <button
                                             v-if="teacher.zoom_account"
                                             @click="openStartMeetingModal(teacher)"
                                             class="text-green-600 hover:text-green-900 font-medium"
                                         >
                                             {{ currentLocale === 'ar' ? 'ابدأ الآن' : 'Start Now' }}
                                         </button>
                                     </div>
                                 </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="teachers.links" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <a
                                v-if="teachers.prev_page_url"
                                :href="teachers.prev_page_url"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                {{ currentLocale === 'ar' ? 'السابق' : 'Previous' }}
                            </a>
                            <a
                                v-if="teachers.next_page_url"
                                :href="teachers.next_page_url"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                {{ currentLocale === 'ar' ? 'التالي' : 'Next' }}
                            </a>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    {{ currentLocale === 'ar' ? 'عرض' : 'Showing' }}
                                    <span class="font-medium">{{ teachers.from }}</span>
                                    {{ currentLocale === 'ar' ? 'إلى' : 'to' }}
                                    <span class="font-medium">{{ teachers.to }}</span>
                                    {{ currentLocale === 'ar' ? 'من أصل' : 'of' }}
                                    <span class="font-medium">{{ teachers.total }}</span>
                                    {{ currentLocale === 'ar' ? 'نتيجة' : 'results' }}
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <a
                                        v-for="(link, index) in teachers.links"
                                        :key="index"
                                        :href="link.url"
                                        v-html="link.label"
                                        :class="[
                                            link.active
                                                ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                                        ]"
                                    ></a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Link Zoom Account Modal -->
        <div v-if="showLinkModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ currentLocale === 'ar' ? 'ربط المعلم بحساب Zoom' : 'Link Teacher to Zoom Account' }}
                    </h3>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ currentLocale === 'ar' ? 'اختر حساب Zoom' : 'Select Zoom Account' }}
                        </label>
                        <select
                            v-model="selectedZoomAccount"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">{{ currentLocale === 'ar' ? 'اختر حساب...' : 'Select account...' }}</option>
                            <option
                                v-for="account in availableZoomAccounts"
                                :key="account.id"
                                :value="account.id"
                            >
                                {{ account.name }} ({{ account.email }})
                            </option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3 rtl:space-x-reverse">
                        <button
                            @click="showLinkModal = false"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
                        >
                            {{ currentLocale === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button
                            @click="linkZoomAccount"
                            :disabled="!selectedZoomAccount"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ currentLocale === 'ar' ? 'ربط' : 'Link' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Start Meeting Modal -->
        <div v-if="showStartMeetingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ currentLocale === 'ar' ? 'ابدأ اجتماع Zoom' : 'Start Zoom Meeting' }}
                    </h3>
                    
                    <form @submit.prevent="startInstantMeeting">
                        <!-- Course Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ currentLocale === 'ar' ? 'اختر الكورس' : 'Select Course' }}
                            </label>
                            <select
                                v-model="meetingForm.course_id"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">{{ currentLocale === 'ar' ? 'اختر كورس...' : 'Select course...' }}</option>
                                <option
                                    v-for="course in selectedTeacher?.teaching_courses"
                                    :key="course.id"
                                    :value="course.id"
                                >
                                    {{ course.title_ar || course.title }}
                                </option>
                            </select>
                        </div>

                        <!-- Topic -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ currentLocale === 'ar' ? 'عنوان الاجتماع' : 'Meeting Topic' }}
                            </label>
                            <input
                                v-model="meetingForm.topic"
                                type="text"
                                required
                                :placeholder="currentLocale === 'ar' ? 'أدخل عنوان الاجتماع' : 'Enter meeting topic'"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <!-- Duration -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ currentLocale === 'ar' ? 'مدة الاجتماع (دقائق)' : 'Duration (minutes)' }}
                            </label>
                            <select
                                v-model="meetingForm.duration"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="30">30 {{ currentLocale === 'ar' ? 'دقيقة' : 'minutes' }}</option>
                                <option value="45">45 {{ currentLocale === 'ar' ? 'دقيقة' : 'minutes' }}</option>
                                <option value="60">60 {{ currentLocale === 'ar' ? 'دقيقة' : 'minutes' }}</option>
                                <option value="90">90 {{ currentLocale === 'ar' ? 'دقيقة' : 'minutes' }}</option>
                                <option value="120">120 {{ currentLocale === 'ar' ? 'دقيقة' : 'minutes' }}</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end space-x-3 rtl:space-x-reverse">
                            <button
                                type="button"
                                @click="showStartMeetingModal = false"
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
                            >
                                {{ currentLocale === 'ar' ? 'إلغاء' : 'Cancel' }}
                            </button>
                            <button
                                type="submit"
                                :disabled="meetingLoading"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="meetingLoading">
                                    {{ currentLocale === 'ar' ? 'جاري البدء...' : 'Starting...' }}
                                </span>
                                <span v-else>
                                    {{ currentLocale === 'ar' ? 'ابدأ الآن' : 'Start Now' }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Zoom Accounts Management Modal -->
        <div v-if="showZoomAccountsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ currentLocale === 'ar' ? 'إدارة حسابات Zoom' : 'Zoom Accounts Management' }}
                        </h3>
                        <button
                            @click="showZoomAccountsModal = false"
                            class="text-gray-400 hover:text-gray-600"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Zoom Accounts List -->
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ currentLocale === 'ar' ? 'اسم الحساب' : 'Account Name' }}
                                        </th>
                                                                                 <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                             {{ currentLocale === 'ar' ? 'البريد الإلكتروني' : 'Email' }}
                                         </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ currentLocale === 'ar' ? 'الحالة' : 'Status' }}
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ currentLocale === 'ar' ? 'المعلم المرتبط' : 'Linked Teacher' }}
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ currentLocale === 'ar' ? 'الإجراءات' : 'Actions' }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="account in zoomAccounts" :key="account.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ account.name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ account.email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="[
                                                    account.is_active
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-red-100 text-red-800',
                                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
                                                ]"
                                            >
                                                {{ account.is_active ? (currentLocale === 'ar' ? 'نشط' : 'Active') : (currentLocale === 'ar' ? 'غير نشط' : 'Inactive') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div v-if="account.teachers && account.teachers.length > 0" class="text-sm text-gray-900">
                                                {{ account.teachers[0].name }}
                                            </div>
                                            <div v-else class="text-sm text-gray-500">
                                                {{ currentLocale === 'ar' ? 'غير مرتبط' : 'Not linked' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2 rtl:space-x-reverse">
                                                <button
                                                    @click="editZoomAccount(account)"
                                                    class="text-blue-600 hover:text-blue-900"
                                                >
                                                    {{ currentLocale === 'ar' ? 'تعديل' : 'Edit' }}
                                                </button>
                                                <button
                                                    @click="toggleZoomAccountStatus(account)"
                                                    :class="[
                                                        account.is_active
                                                            ? 'text-red-600 hover:text-red-900'
                                                            : 'text-green-600 hover:text-green-900'
                                                    ]"
                                                >
                                                    {{ account.is_active ? (currentLocale === 'ar' ? 'إلغاء تفعيل' : 'Deactivate') : (currentLocale === 'ar' ? 'تفعيل' : 'Activate') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');

// Props
const props = defineProps({
    teachers: Object,
    zoomAccounts: Array,
    filters: Object
});

// Local state
const showLinkModal = ref(false);
const showZoomAccountsModal = ref(false);
const showCreateModal = ref(false);
const showStartMeetingModal = ref(false);
const selectedTeacher = ref(null);
const selectedZoomAccount = ref('');
const meetingForm = ref({
    course_id: '',
    course_schedule_id: '',
    topic: '',
    duration: 60
});
const meetingLoading = ref(false);

// Computed
const availableZoomAccounts = computed(() => {
    if (!props.zoomAccounts || !Array.isArray(props.zoomAccounts)) {
        return [];
    }
    
    // إرجاع جميع الحسابات النشطة التي لم يتم ربطها بمعلم آخر
    return props.zoomAccounts.filter(account => {
        return account.is_active && !account.teachers?.length;
    });
});

// Methods
const formatDate = (date) => {
    return new Date(date).toLocaleDateString(currentLocale.value === 'ar' ? 'ar-SA' : 'en-US');
};

const openLinkModal = (teacher) => {
    selectedTeacher.value = teacher;
    selectedZoomAccount.value = '';
    showLinkModal.value = true;
};

const linkZoomAccount = async () => {
    if (!selectedTeacher.value || !selectedZoomAccount.value) return;

    try {
        // Debugging CSRF token
        console.log('=== DEBUGGING CSRF TOKEN ===');
        const metaElement = document.querySelector('meta[name="csrf-token"]');
        console.log('Meta element:', metaElement);
        console.log('Meta element exists:', !!metaElement);
        
        if (metaElement) {
            console.log('Meta content attribute:', metaElement.getAttribute('content'));
            console.log('Meta content property:', metaElement.content);
        }
        
        // الحصول على CSRF token بطريقة آمنة
        const csrfToken = metaElement?.getAttribute('content') || 
                         metaElement?.content ||
                         '';

        console.log('Final CSRF token:', csrfToken);
        console.log('CSRF token length:', csrfToken.length);

        if (!csrfToken) {
            console.error('CSRF token not found');
            alert('خطأ في الأمان - يرجى إعادة تحميل الصفحة');
            return;
        }

        const response = await fetch(`/admin/teachers/${selectedTeacher.value.id}/link-zoom-account`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                zoom_account_id: selectedZoomAccount.value
            })
        });

        const data = await response.json();

        if (data.success) {
            showLinkModal.value = false;
            router.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء ربط الحساب');
        }
    } catch (error) {
        console.error('Error linking zoom account:', error);
        alert('حدث خطأ أثناء ربط الحساب');
    }
};

const unlinkZoomAccount = async (teacher) => {
    if (!confirm(currentLocale.value === 'ar' ? 'هل أنت متأكد من إلغاء ربط هذا المعلم بحساب Zoom؟' : 'Are you sure you want to unlink this teacher from Zoom account?')) {
        return;
    }

    try {
        // الحصول على CSRF token بطريقة آمنة
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('meta[name="csrf-token"]')?.content ||
                         '';

        if (!csrfToken) {
            console.error('CSRF token not found');
            alert('خطأ في الأمان - يرجى إعادة تحميل الصفحة');
            return;
        }

        const response = await fetch(`/admin/teachers/${teacher.id}/unlink-zoom-account`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const data = await response.json();

        if (data.success) {
            router.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء إلغاء ربط الحساب');
        }
    } catch (error) {
        console.error('Error unlinking zoom account:', error);
        alert('حدث خطأ أثناء إلغاء ربط الحساب');
    }
};

const toggleZoomAccountStatus = async (account) => {
    try {
        // الحصول على CSRF token بطريقة آمنة
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('meta[name="csrf-token"]')?.content ||
                         '';

        if (!csrfToken) {
            console.error('CSRF token not found');
            alert('خطأ في الأمان - يرجى إعادة تحميل الصفحة');
            return;
        }

        const response = await fetch(`/admin/zoom-accounts/${account.id}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const data = await response.json();

        if (data.success) {
            router.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء تغيير حالة الحساب');
        }
    } catch (error) {
        console.error('Error toggling zoom account status:', error);
        alert('حدث خطأ أثناء تغيير حالة الحساب');
    }
};

const applyFilters = () => {
    router.get('/admin/teachers', props.filters);
};

const clearFilters = () => {
    router.get('/admin/teachers');
};

const editTeacher = (teacher) => {
    router.visit(`/admin/users/${teacher.id}/edit`);
};

const deleteTeacher = (teacher) => {
    if (confirm(currentLocale.value === 'ar' ? 'هل أنت متأكد من حذف هذا المعلم؟' : 'Are you sure you want to delete this teacher?')) {
        router.delete(`/admin/users/${teacher.id}`);
    }
};

const editZoomAccount = (account) => {
    router.visit(`/admin/zoom-accounts/${account.id}/edit`);
};

const openStartMeetingModal = (teacher) => {
    selectedTeacher.value = teacher;
    meetingForm.value = {
        course_id: '',
        course_schedule_id: '',
        topic: `${teacher.name} - اجتماع فوري`,
        duration: 60
    };
    showStartMeetingModal.value = true;
};

const startInstantMeeting = async () => {
    if (!meetingForm.value.course_id || !meetingForm.value.topic) {
        alert(currentLocale.value === 'ar' ? 'يرجى ملء جميع الحقول المطلوبة' : 'Please fill all required fields');
        return;
    }

    meetingLoading.value = true;

    try {
        const response = await fetch('/zoom-meetings/start-instant', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(meetingForm.value)
        });

        const data = await response.json();

        if (data.success) {
            alert(currentLocale.value === 'ar' ? 'تم بدء الاجتماع بنجاح!' : 'Meeting started successfully!');
            
            // فتح Zoom في نافذة جديدة
            if (data.start_url) {
                window.open(data.start_url, '_blank');
            }
            
            showStartMeetingModal.value = false;
            router.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء بدء الاجتماع');
        }
    } catch (error) {
        console.error('Error starting meeting:', error);
        alert('حدث خطأ أثناء بدء الاجتماع');
    } finally {
        meetingLoading.value = false;
    }
};
</script>
