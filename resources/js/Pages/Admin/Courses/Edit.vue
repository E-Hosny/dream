<template>
    <AdminLayout>
        <Head :title="t('edit_course')" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                                    {{ t('edit_course') }}
                                </h1>
                                <p class="text-gray-600 dark:text-gray-400 mt-1">
                                    {{ t('update_course_information') }}
                                </p>
                            </div>
                            <Link
                                :href="route('admin.courses.show', course.id)"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 ml-2 rtl:ml-0 rtl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                {{ t('back_to_course') }}
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Edit Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <!-- Basic Information -->
                        <div class="mb-8">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                {{ t('basic_information') }}
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel :value="t('title')" for="title" />
                                    <TextInput
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        :placeholder="t('enter_course_title')"
                                        required
                                    />
                                    <InputError :message="form.errors.title" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel :value="t('title_ar')" for="title_ar" />
                                    <TextInput
                                        id="title_ar"
                                        v-model="form.title_ar"
                                        type="text"
                                        class="mt-1 block w-full"
                                        :placeholder="t('enter_course_title_arabic')"
                                    />
                                    <InputError :message="form.errors.title_ar" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel :value="t('instructor')" for="instructor_id" />
                                    <select
                                        id="instructor_id"
                                        v-model="form.instructor_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="">{{ t('select_instructor') }}</option>
                                        <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                                            {{ teacher.name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.instructor_id" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel :value="t('status')" for="status" />
                                    <select
                                        id="status"
                                        v-model="form.status"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="draft">{{ t('draft') }}</option>
                                        <option value="published">{{ t('published') }}</option>
                                        <option value="archived">{{ t('archived') }}</option>
                                    </select>
                                    <InputError :message="form.errors.status" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel :value="t('level')" for="level" />
                                    <select
                                        id="level"
                                        v-model="form.level"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="beginner">{{ t('beginner') }}</option>
                                        <option value="intermediate">{{ t('intermediate') }}</option>
                                        <option value="advanced">{{ t('advanced') }}</option>
                                    </select>
                                    <InputError :message="form.errors.level" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel :value="t('price')" for="price" />
                                    <TextInput
                                        id="price"
                                        v-model="form.price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="mt-1 block w-full"
                                        :placeholder="t('enter_course_price')"
                                        required
                                    />
                                    <InputError :message="form.errors.price" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel :value="t('duration')" for="duration_hours" />
                                    <TextInput
                                        id="duration_hours"
                                        v-model="form.duration_hours"
                                        type="number"
                                        min="1"
                                        class="mt-1 block w-full"
                                        :placeholder="t('enter_duration_hours')"
                                        required
                                    />
                                    <InputError :message="form.errors.duration_hours" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel :value="t('max_students')" for="max_students" />
                                    <TextInput
                                        id="max_students"
                                        v-model="form.max_students"
                                        type="number"
                                        min="1"
                                        class="mt-1 block w-full"
                                        :placeholder="t('enter_max_students')"
                                    />
                                    <InputError :message="form.errors.max_students" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel :value="t('start_date')" for="start_date" />
                                    <TextInput
                                        id="start_date"
                                        v-model="form.start_date"
                                        type="date"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.start_date" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel :value="t('end_date')" for="end_date" />
                                    <TextInput
                                        id="end_date"
                                        v-model="form.end_date"
                                        type="date"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.end_date" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-8">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                {{ t('description') }}
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel :value="t('description')" for="description" />
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="4"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                                        :placeholder="t('enter_course_description')"
                                        required
                                    ></textarea>
                                    <InputError :message="form.errors.description" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel :value="t('description_ar')" for="description_ar" />
                                    <textarea
                                        id="description_ar"
                                        v-model="form.description_ar"
                                        rows="4"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                                        :placeholder="t('enter_course_description_arabic')"
                                    ></textarea>
                                    <InputError :message="form.errors.description_ar" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Requirements and Learning Outcomes -->
                        <div class="mb-8">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                {{ t('requirements_and_outcomes') }}
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel :value="t('requirements')" />
                                    <div class="space-y-2">
                                        <div v-for="(requirement, index) in form.requirements" :key="index" class="flex items-center space-x-2 rtl:space-x-reverse">
                                            <TextInput
                                                v-model="form.requirements[index]"
                                                type="text"
                                                class="flex-1"
                                                :placeholder="t('enter_requirement')"
                                            />
                                            <button
                                                type="button"
                                                @click="removeRequirement(index)"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <button
                                            type="button"
                                            @click="addRequirement"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        >
                                            <svg class="w-4 h-4 ml-2 rtl:ml-0 rtl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            {{ t('add_requirement') }}
                                        </button>
                                    </div>
                                    <InputError :message="form.errors.requirements" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel :value="t('learning_outcomes')" />
                                    <div class="space-y-2">
                                        <div v-for="(outcome, index) in form.learning_outcomes" :key="index" class="flex items-center space-x-2 rtl:space-x-reverse">
                                            <TextInput
                                                v-model="form.learning_outcomes[index]"
                                                type="text"
                                                class="flex-1"
                                                :placeholder="t('enter_learning_outcome')"
                                            />
                                            <button
                                                type="button"
                                                @click="removeOutcome(index)"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <button
                                            type="button"
                                            @click="addOutcome"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        >
                                            <svg class="w-4 h-4 ml-2 rtl:ml-0 rtl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            {{ t('add_outcome') }}
                                        </button>
                                    </div>
                                    <InputError :message="form.errors.learning_outcomes" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Course Schedules -->
                        <div class="mb-8">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                {{ t('course_schedules') }}
                            </h2>
                            <div class="space-y-4">
                                <div v-for="(schedule, index) in form.schedules" :key="index" class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div>
                                        <InputLabel :value="t('day_of_week')" />
                                        <select
                                            v-model="schedule.day_of_week"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                                            required
                                        >
                                            <option value="saturday">{{ t('saturday') }}</option>
                                            <option value="sunday">{{ t('sunday') }}</option>
                                            <option value="monday">{{ t('monday') }}</option>
                                            <option value="tuesday">{{ t('tuesday') }}</option>
                                            <option value="wednesday">{{ t('wednesday') }}</option>
                                            <option value="thursday">{{ t('thursday') }}</option>
                                            <option value="friday">{{ t('friday') }}</option>
                                        </select>
                                    </div>

                                    <div>
                                        <InputLabel :value="t('start_time')" />
                                        <TextInput
                                            v-model="schedule.start_time"
                                            type="time"
                                            class="mt-1 block w-full"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <InputLabel :value="t('end_time')" />
                                        <TextInput
                                            v-model="schedule.end_time"
                                            type="time"
                                            class="mt-1 block w-full"
                                            required
                                        />
                                    </div>

                                    <div class="flex items-end">
                                        <button
                                            type="button"
                                            @click="removeSchedule(index)"
                                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                        >
                                            <svg class="w-4 h-4 ml-2 rtl:ml-0 rtl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            {{ t('remove') }}
                                        </button>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    @click="addSchedule"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    <svg class="w-4 h-4 ml-2 rtl:ml-0 rtl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    {{ t('add_schedule') }}
                                </button>
                            </div>
                            <InputError :message="form.errors.schedules" class="mt-2" />
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3 rtl:space-x-reverse">
                            <Link
                                :href="route('admin.courses.show', course.id)"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                {{ t('cancel') }}
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                {{ t('update_course') }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');

const props = defineProps({
    course: Object,
    teachers: Array
});

const form = useForm({
    title: props.course.title || '',
    title_ar: props.course.title_ar || '',
    description: props.course.description || '',
    description_ar: props.course.description_ar || '',
    price: props.course.price || 0,
    duration_hours: props.course.duration_hours || 1,
    level: props.course.level || 'beginner',
    status: props.course.status || 'draft',
    instructor_id: props.course.instructor_id || '',
    max_students: props.course.max_students || '',
    start_date: props.course.start_date || '',
    end_date: props.course.end_date || '',
    requirements: props.course.requirements || [],
    learning_outcomes: props.course.learning_outcomes || [],
    schedules: props.course.schedules ? props.course.schedules.map(s => ({
        day_of_week: s.day_of_week,
        start_time: s.start_time,
        end_time: s.end_time
    })) : []
});

// Initialize schedules if empty
onMounted(() => {
    if (form.schedules.length === 0) {
        addSchedule();
    }
});

// Helper functions
const addRequirement = () => {
    form.requirements.push('');
};

const removeRequirement = (index) => {
    form.requirements.splice(index, 1);
};

const addOutcome = () => {
    form.learning_outcomes.push('');
};

const removeOutcome = (index) => {
    form.learning_outcomes.splice(index, 1);
};

const addSchedule = () => {
    form.schedules.push({
        day_of_week: 'saturday',
        start_time: '08:00',
        end_time: '09:00'
    });
};

const removeSchedule = (index) => {
    form.schedules.splice(index, 1);
};

const submit = () => {
    form.put(route('admin.courses.update', props.course.id));
};

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            edit_course: 'Edit Course',
            update_course_information: 'Update course information and settings',
            back_to_course: 'Back to Course',
            basic_information: 'Basic Information',
            title: 'Course Title (English)',
            title_ar: 'Course Title (Arabic)',
            instructor: 'Instructor',
            status: 'Status',
            level: 'Level',
            price: 'Price',
            duration: 'Duration (Hours)',
            max_students: 'Max Students',
            start_date: 'Start Date',
            end_date: 'End Date',
            description: 'Description (English)',
            description_ar: 'Description (Arabic)',
            requirements_and_outcomes: 'Requirements and Learning Outcomes',
            requirements: 'Requirements',
            learning_outcomes: 'Learning Outcomes',
            course_schedules: 'Course Schedules',
            day_of_week: 'Day of Week',
            start_time: 'Start Time',
            end_time: 'End Time',
            add_requirement: 'Add Requirement',
            add_outcome: 'Add Learning Outcome',
            add_schedule: 'Add Schedule',
            remove: 'Remove',
            cancel: 'Cancel',
            update_course: 'Update Course',
            select_instructor: 'Select an instructor',
            enter_course_title: 'Enter course title',
            enter_course_title_arabic: 'Enter course title in Arabic',
            enter_course_price: 'Enter course price',
            enter_duration_hours: 'Enter duration in hours',
            enter_max_students: 'Enter maximum students',
            enter_course_description: 'Enter course description',
            enter_course_description_arabic: 'Enter course description in Arabic',
            enter_requirement: 'Enter requirement',
            enter_learning_outcome: 'Enter learning outcome',
            // Status translations
            draft: 'Draft',
            published: 'Published',
            archived: 'Archived',
            // Level translations
            beginner: 'Beginner',
            intermediate: 'Intermediate',
            advanced: 'Advanced',
            // Day translations
            saturday: 'Saturday',
            sunday: 'Sunday',
            monday: 'Monday',
            tuesday: 'Tuesday',
            wednesday: 'Wednesday',
            thursday: 'Thursday',
            friday: 'Friday'
        },
        ar: {
            edit_course: 'تعديل الكورس',
            update_course_information: 'تحديث معلومات الكورس والإعدادات',
            back_to_course: 'العودة للكورس',
            basic_information: 'المعلومات الأساسية',
            title: 'عنوان الكورس (إنجليزي)',
            title_ar: 'عنوان الكورس (عربي)',
            instructor: 'المعلم',
            status: 'الحالة',
            level: 'المستوى',
            price: 'السعر',
            duration: 'المدة (ساعات)',
            max_students: 'الحد الأقصى للطلاب',
            start_date: 'تاريخ البداية',
            end_date: 'تاريخ الانتهاء',
            description: 'الوصف (إنجليزي)',
            description_ar: 'الوصف (عربي)',
            requirements_and_outcomes: 'المتطلبات والنتائج التعليمية',
            requirements: 'المتطلبات',
            learning_outcomes: 'النتائج التعليمية',
            course_schedules: 'جدولة الكورس',
            day_of_week: 'يوم الأسبوع',
            start_time: 'وقت البداية',
            end_time: 'وقت الانتهاء',
            add_requirement: 'إضافة متطلب',
            add_outcome: 'إضافة نتيجة تعليمية',
            add_schedule: 'إضافة جدول',
            remove: 'إزالة',
            cancel: 'إلغاء',
            update_course: 'تحديث الكورس',
            select_instructor: 'اختر المعلم',
            enter_course_title: 'أدخل عنوان الكورس',
            enter_course_title_arabic: 'أدخل عنوان الكورس بالعربية',
            enter_course_price: 'أدخل سعر الكورس',
            enter_duration_hours: 'أدخل المدة بالساعات',
            enter_max_students: 'أدخل الحد الأقصى للطلاب',
            enter_course_description: 'أدخل وصف الكورس',
            enter_course_description_arabic: 'أدخل وصف الكورس بالعربية',
            enter_requirement: 'أدخل المتطلب',
            enter_learning_outcome: 'أدخل النتيجة التعليمية',
            // Status translations
            draft: 'مسودة',
            published: 'منشور',
            archived: 'مؤرشف',
            // Level translations
            beginner: 'مبتدئ',
            intermediate: 'متوسط',
            advanced: 'متقدم',
            // Day translations
            saturday: 'السبت',
            sunday: 'الأحد',
            monday: 'الاثنين',
            tuesday: 'الثلاثاء',
            wednesday: 'الأربعاء',
            thursday: 'الخميس',
            friday: 'الجمعة'
        }
    };
    
    return translations[currentLocale.value]?.[key] || key;
};
</script>
