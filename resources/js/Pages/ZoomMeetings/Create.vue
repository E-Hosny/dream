<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    {{ currentLocale === 'ar' ? 'إنشاء اجتماع جديد' : 'Create New Meeting' }}
                </h1>
                <p class="text-gray-600">
                    {{ currentLocale === 'ar' ? 'إنشاء اجتماع Zoom جديد للكورس' : 'Create a new Zoom meeting for the course' }}
                </p>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Course Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ currentLocale === 'ar' ? 'الكورس' : 'Course' }} *
                        </label>
                        <select 
                            v-model="form.course_id" 
                            @change="loadSchedules"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                        >
                            <option value="">{{ currentLocale === 'ar' ? 'اختر الكورس' : 'Select Course' }}</option>
                            <option v-for="course in courses" :key="course.id" :value="course.id">
                                {{ currentLocale === 'ar' ? course.title_ar : course.titleEn }}
                            </option>
                        </select>
                    </div>

                    <!-- Course Schedule Selection -->
                    <div v-if="schedules.length > 0">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ currentLocale === 'ar' ? 'موعد الكورس (اختياري)' : 'Course Schedule (Optional)' }}
                        </label>
                        <select 
                            v-model="form.course_schedule_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">{{ currentLocale === 'ar' ? 'بدون موعد محدد' : 'No specific schedule' }}</option>
                            <option v-for="schedule in schedules" :key="schedule.id" :value="schedule.id">
                                {{ schedule.day }} - {{ schedule.start_time }} إلى {{ schedule.end_time }}
                            </option>
                        </select>
                    </div>

                    <!-- Meeting Topic -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ currentLocale === 'ar' ? 'عنوان الاجتماع' : 'Meeting Topic' }} *
                        </label>
                        <input 
                            v-model="form.topic" 
                            type="text" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :placeholder="currentLocale === 'ar' ? 'أدخل عنوان الاجتماع' : 'Enter meeting topic'"
                            required
                        />
                    </div>

                    <!-- Start Time -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ currentLocale === 'ar' ? 'وقت البدء' : 'Start Time' }} *
                        </label>
                        <input 
                            v-model="form.start_time" 
                            type="datetime-local" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                        />
                    </div>

                    <!-- Duration -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ currentLocale === 'ar' ? 'المدة (بالدقائق)' : 'Duration (minutes)' }} *
                        </label>
                        <input 
                            v-model="form.duration" 
                            type="number" 
                            min="15" 
                            max="480" 
                            step="15"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :placeholder="currentLocale === 'ar' ? 'مثال: 60' : 'Example: 60'"
                            required
                        />
                        <p class="text-sm text-gray-500 mt-1">
                            {{ currentLocale === 'ar' ? 'المدة من 15 دقيقة إلى 8 ساعات' : 'Duration from 15 minutes to 8 hours' }}
                        </p>
                    </div>

                    <!-- Timezone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ currentLocale === 'ar' ? 'المنطقة الزمنية' : 'Timezone' }}
                        </label>
                        <select 
                            v-model="form.timezone" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="Asia/Riyadh">Asia/Riyadh (GMT+3)</option>
                            <option value="Asia/Dubai">Asia/Dubai (GMT+4)</option>
                            <option value="Europe/London">Europe/London (GMT+0)</option>
                            <option value="America/New_York">America/New_York (GMT-5)</option>
                        </select>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ currentLocale === 'ar' ? 'كلمة المرور (اختياري)' : 'Password (Optional)' }}
                        </label>
                        <input 
                            v-model="form.password" 
                            type="text" 
                            maxlength="10"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :placeholder="currentLocale === 'ar' ? 'اتركها فارغة لإنشاء كلمة مرور تلقائية' : 'Leave empty for auto-generated password'"
                        />
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3 rtl:space-x-reverse">
                        <button 
                            type="button" 
                            @click="$router.back()"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
                        >
                            {{ currentLocale === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="form.processing">
                                {{ currentLocale === 'ar' ? 'جاري الإنشاء...' : 'Creating...' }}
                            </span>
                            <span v-else>
                                {{ currentLocale === 'ar' ? 'إنشاء الاجتماع' : 'Create Meeting' }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');

// Props
const props = defineProps({
    courses: Array,
    schedules: Array,
    selectedCourseId: String
});

// Form
const form = useForm({
    course_id: props.selectedCourseId || '',
    course_schedule_id: '',
    topic: '',
    start_time: '',
    duration: 60,
    timezone: 'Asia/Riyadh',
    password: ''
});

// Local state
const schedules = ref(props.schedules || []);

// Methods
const loadSchedules = async () => {
    if (form.course_id) {
        try {
            const response = await fetch(`/api/courses/${form.course_id}/schedules`);
            const data = await response.json();
            schedules.value = data;
        } catch (error) {
            console.error('Error loading schedules:', error);
            schedules.value = [];
        }
    } else {
        schedules.value = [];
    }
    form.course_schedule_id = '';
};

const submitForm = () => {
    form.post(route('zoom-meetings.store'));
};

// Set default start time to next hour
onMounted(() => {
    const nextHour = new Date();
    nextHour.setHours(nextHour.getHours() + 1);
    nextHour.setMinutes(0);
    nextHour.setSeconds(0);
    form.start_time = nextHour.toISOString().slice(0, 16);
});
</script>
