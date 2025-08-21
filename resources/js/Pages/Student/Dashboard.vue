<script setup>
import StudentLayout from '@/Layouts/StudentLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');
const user = computed(() => page.props.auth.user);

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            student_dashboard: 'Student Dashboard',
            welcome_message: 'Welcome to your learning journey',
            learning_progress: 'Learning Progress',
            enrolled_courses: 'Enrolled Courses',
            completed_lessons: 'Completed Lessons',
            pending_assignments: 'Pending Assignments',
            achievement_points: 'Achievement Points',
            continue_learning: 'Continue Learning',
            recent_courses: 'Recent Courses',
            upcoming_deadlines: 'Upcoming Deadlines',
            achievements: 'Achievements',
            view_all: 'View All',
            resume: 'Resume',
            quick_access: 'Quick Access',
            browse_courses: 'Browse Courses',
            my_assignments: 'My Assignments',
            study_schedule: 'Study Schedule',
            discussion_forum: 'Discussion Forum',
            progress_overview: 'Progress Overview',
            course_completion: 'Course Completion',
            average_score: 'Average Score',
            study_streak: 'Study Streak',
            days: 'days',
            hours_studied: 'Hours Studied',
            this_week: 'This Week',
            certificates_earned: 'Certificates Earned',
            view_certificate: 'View Certificate',
            due_in: 'Due in',
            assignment: 'Assignment',
            quiz: 'Quiz',
            project: 'Project',
            completed: 'Completed',
            in_progress: 'In Progress',
            not_started: 'Not Started',
        },
        ar: {
            student_dashboard: 'لوحة تحكم الطالب',
            welcome_message: 'مرحباً بك في رحلة التعلم',
            learning_progress: 'تقدم التعلم',
            enrolled_courses: 'الكورسات المسجلة',
            completed_lessons: 'الدروس المكتملة',
            pending_assignments: 'الواجبات المعلقة',
            achievement_points: 'نقاط الإنجاز',
            continue_learning: 'متابعة التعلم',
            recent_courses: 'الكورسات الأخيرة',
            upcoming_deadlines: 'المواعيد النهائية القادمة',
            achievements: 'الإنجازات',
            view_all: 'عرض الكل',
            resume: 'استكمال',
            quick_access: 'الوصول السريع',
            browse_courses: 'تصفح الكورسات',
            my_assignments: 'واجباتي',
            study_schedule: 'جدول المذاكرة',
            discussion_forum: 'منتدى النقاش',
            progress_overview: 'نظرة عامة على التقدم',
            course_completion: 'إكمال الكورسات',
            average_score: 'متوسط النتائج',
            study_streak: 'سلسلة المذاكرة',
            days: 'أيام',
            hours_studied: 'ساعات المذاكرة',
            this_week: 'هذا الأسبوع',
            certificates_earned: 'الشهادات المكتسبة',
            view_certificate: 'عرض الشهادة',
            due_in: 'مستحق خلال',
            assignment: 'واجب',
            quiz: 'اختبار',
            project: 'مشروع',
            completed: 'مكتمل',
            in_progress: 'قيد التقدم',
            not_started: 'لم يبدأ',
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

// Sample data - في التطبيق الحقيقي ستأتي من الخادم
const stats = {
    enrolledCourses: 6,
    completedLessons: 45,
    pendingAssignments: 3,
    achievementPoints: 1250,
    courseCompletion: 68,
    averageScore: 87,
    studyStreak: 12,
    hoursThisWeek: 18
};

const recentCourses = [
    {
        title: 'Advanced Mathematics',
        progress: 75,
        lastAccessed: '2 hours ago',
        nextLesson: 'Calculus Fundamentals',
        instructor: 'Dr. Ahmed Hassan',
        color: 'from-blue-500 to-indigo-600'
    },
    {
        title: 'Web Development',
        progress: 45,
        lastAccessed: '1 day ago',
        nextLesson: 'React Components',
        instructor: 'Sarah Johnson',
        color: 'from-emerald-500 to-teal-600'
    },
    {
        title: 'Physics 101',
        progress: 90,
        lastAccessed: '3 days ago',
        nextLesson: 'Quantum Mechanics',
        instructor: 'Prof. Mohamed Ali',
        color: 'from-purple-500 to-pink-600'
    }
];

const upcomingDeadlines = [
    {
        type: 'assignment',
        title: 'Mathematics Problem Set 5',
        course: 'Advanced Mathematics',
        dueDate: '2 days',
        priority: 'high'
    },
    {
        type: 'quiz',
        title: 'JavaScript Fundamentals Quiz',
        course: 'Web Development',
        dueDate: '5 days',
        priority: 'medium'
    },
    {
        type: 'project',
        title: 'Physics Lab Report',
        course: 'Physics 101',
        dueDate: '1 week',
        priority: 'low'
    }
];

const achievements = [
    {
        title: 'Fast Learner',
        description: 'Completed 5 lessons in one day',
        icon: '⚡',
        earned: true
    },
    {
        title: 'Perfect Score',
        description: 'Got 100% on an assignment',
        icon: '🎯',
        earned: true
    },
    {
        title: 'Study Streak',
        description: 'Study for 7 consecutive days',
        icon: '🔥',
        earned: true
    },
    {
        title: 'Course Master',
        description: 'Complete your first course',
        icon: '🏆',
        earned: false
    }
];
</script>

<template>
    <Head :title="t('student_dashboard')" />

    <StudentLayout>
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ t('student_dashboard') }}</h1>
            <p class="text-gray-600">{{ t('welcome_message') }}</p>
            <div class="mt-4 text-sm text-gray-500">
                {{ currentLocale === 'en' ? 'Keep learning,' : 'واصل التعلم،' }} {{ user.name }}
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Enrolled Courses -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ stats.enrolledCourses }}</h3>
                        <p class="text-sm text-gray-500">{{ t('enrolled_courses') }}</p>
                    </div>
                </div>
            </div>

            <!-- Completed Lessons -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ stats.completedLessons }}</h3>
                        <p class="text-sm text-gray-500">{{ t('completed_lessons') }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending Assignments -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-orange-500 to-red-600 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ stats.pendingAssignments }}</h3>
                        <p class="text-sm text-gray-500">{{ t('pending_assignments') }}</p>
                    </div>
                </div>
            </div>

            <!-- Achievement Points -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-r from-purple-500 to-pink-600 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 rtl:ml-0 rtl:mr-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ stats.achievementPoints.toLocaleString() }}</h3>
                        <p class="text-sm text-gray-500">{{ t('achievement_points') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Continue Learning -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">{{ t('continue_learning') }}</h2>
                        <button class="text-sm text-blue-600 hover:text-blue-500 font-medium">{{ t('view_all') }}</button>
                    </div>
                    
                    <div class="space-y-4">
                        <div v-for="course in recentCourses" :key="course.title" class="p-4 rounded-lg border border-gray-100 hover:border-blue-200 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ course.title }}</h3>
                                    <p class="text-sm text-gray-500">{{ currentLocale === 'en' ? 'by' : 'بواسطة' }} {{ course.instructor }}</p>
                                </div>
                                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    {{ t('resume') }}
                                </button>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="mb-3">
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="text-gray-600">{{ course.progress }}% {{ t('completed') }}</span>
                                    <span class="text-gray-500">{{ currentLocale === 'en' ? 'Last accessed:' : 'آخر وصول:' }} {{ course.lastAccessed }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div :class="`h-2 rounded-full bg-gradient-to-r ${course.color}`" :style="`width: ${course.progress}%`"></div>
                                </div>
                            </div>
                            
                            <p class="text-sm text-gray-600">
                                {{ currentLocale === 'en' ? 'Next:' : 'التالي:' }} {{ course.nextLesson }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Progress Overview -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ t('progress_overview') }}</h2>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ stats.courseCompletion }}%</div>
                            <div class="text-sm text-gray-600">{{ t('course_completion') }}</div>
                        </div>
                        <div class="text-center p-4 bg-emerald-50 rounded-lg">
                            <div class="text-2xl font-bold text-emerald-600">{{ stats.averageScore }}%</div>
                            <div class="text-sm text-gray-600">{{ t('average_score') }}</div>
                        </div>
                        <div class="text-center p-4 bg-orange-50 rounded-lg">
                            <div class="text-2xl font-bold text-orange-600">{{ stats.studyStreak }}</div>
                            <div class="text-sm text-gray-600">{{ t('study_streak') }} ({{ t('days') }})</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">{{ stats.hoursThisWeek }}</div>
                            <div class="text-sm text-gray-600">{{ t('hours_studied') }} ({{ t('this_week') }})</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Access -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ t('quick_access') }}</h2>
                    
                    <div class="space-y-3">
                        <button class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="h-5 w-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path>
                            </svg>
                            {{ t('browse_courses') }}
                        </button>

                        <button class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="h-5 w-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"></path>
                            </svg>
                            {{ t('my_assignments') }}
                        </button>

                        <button class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="h-5 w-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5"></path>
                            </svg>
                            {{ t('study_schedule') }}
                        </button>

                        <button class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-lg hover:from-orange-700 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="h-5 w-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            {{ t('discussion_forum') }}
                        </button>
                    </div>
                </div>

                <!-- Upcoming Deadlines -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ t('upcoming_deadlines') }}</h2>
                    
                    <div class="space-y-4">
                        <div v-for="deadline in upcomingDeadlines" :key="deadline.title" class="p-3 rounded-lg border border-gray-100 hover:border-blue-200 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900 text-sm">{{ deadline.title }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">{{ deadline.course }}</p>
                                    <div class="flex items-center mt-2">
                                        <span :class="[
                                            'px-2 py-1 text-xs rounded-full',
                                            deadline.type === 'assignment' ? 'bg-blue-100 text-blue-600' :
                                            deadline.type === 'quiz' ? 'bg-emerald-100 text-emerald-600' :
                                            'bg-purple-100 text-purple-600'
                                        ]">
                                            {{ t(deadline.type) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right rtl:text-left">
                                    <span class="text-xs text-gray-500">{{ t('due_in') }}</span>
                                    <p :class="[
                                        'text-sm font-medium',
                                        deadline.priority === 'high' ? 'text-red-600' :
                                        deadline.priority === 'medium' ? 'text-orange-600' :
                                        'text-green-600'
                                    ]">
                                        {{ deadline.dueDate }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Achievements -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ t('achievements') }}</h2>
                    
                    <div class="space-y-3">
                        <div v-for="achievement in achievements" :key="achievement.title" :class="[
                            'flex items-center p-3 rounded-lg transition-colors',
                            achievement.earned ? 'bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200' : 'bg-gray-50 border border-gray-200'
                        ]">
                            <div class="text-2xl mr-3 rtl:mr-0 rtl:ml-3">{{ achievement.icon }}</div>
                            <div class="flex-1">
                                <h3 :class="[
                                    'font-medium text-sm',
                                    achievement.earned ? 'text-gray-900' : 'text-gray-500'
                                ]">
                                    {{ achievement.title }}
                                </h3>
                                <p :class="[
                                    'text-xs mt-1',
                                    achievement.earned ? 'text-gray-600' : 'text-gray-400'
                                ]">
                                    {{ achievement.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
