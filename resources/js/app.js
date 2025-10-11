import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { createI18n } from "vue-i18n";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

// إعداد الترجمة
const i18n = createI18n({
    locale: "ar",
    fallbackLocale: "en",
    messages: {
        ar: {
            // Attendance System
            attendance: 'الحضور',
            attendance_reports: 'تقارير الحضور والانصراف',
            attendance_log: 'سجل الحضور والانصراف',
            participant: 'مشارك',
            meeting: 'الاجتماع',
            join_time: 'وقت الدخول',
            leave_time: 'وقت الخروج',
            attendance_duration: 'مدة الحضور',
            status: 'الحالة',
            teacher: 'معلم',
            student: 'طالب',
            connected: 'متصل',
            ended: 'منتهي',
            still_connected: 'لا يزال متصل',
            not_specified: 'غير محدد',
            in_progress: 'جاري...',
            search: 'بحث',
            clear_filters: 'مسح الفلاتر',
            export_data: 'تصدير البيانات',
            no_attendance_records: 'لا توجد سجلات حضور',
            no_records_match_criteria: 'لا توجد سجلات حضور تطابق معايير البحث المحددة',
            showing_results: 'عرض {from} إلى {to} من {total} نتيجة',
            previous: 'السابق',
            next: 'التالي',
            track_attendance_description: 'تتبع حضور وانصراف المعلمين والطلاب في جميع الاجتماعات',
            statistics_dashboard: 'لوحة الإحصائيات',
            search_and_filter: 'البحث والفلترة',
            course: 'الكورس',
            user_type: 'نوع المستخدم',
            user: 'المستخدم',
            all: 'الكل',
            all_courses: 'جميع الكورسات',
            from_date: 'من تاريخ',
            to_date: 'إلى تاريخ',
            teachers: 'معلمين',
            students: 'طلاب',
        },
        en: {
            // Attendance System
            attendance: 'Attendance',
            attendance_reports: 'Attendance Reports',
            attendance_log: 'Attendance & Departure Log',
            participant: 'Participant',
            meeting: 'Meeting',
            join_time: 'Join Time',
            leave_time: 'Leave Time',
            attendance_duration: 'Attendance Duration',
            status: 'Status',
            teacher: 'Teacher',
            student: 'Student',
            connected: 'Connected',
            ended: 'Ended',
            still_connected: 'Still Connected',
            not_specified: 'Not Specified',
            in_progress: 'In Progress...',
            search: 'Search',
            clear_filters: 'Clear Filters',
            export_data: 'Export Data',
            no_attendance_records: 'No Attendance Records',
            no_records_match_criteria: 'No attendance records match the specified search criteria',
            showing_results: 'Showing {from} to {to} of {total} results',
            previous: 'Previous',
            next: 'Next',
            track_attendance_description: 'Track attendance and departure of teachers and students in all meetings',
            statistics_dashboard: 'Statistics Dashboard',
            search_and_filter: 'Search & Filter',
            course: 'Course',
            user_type: 'User Type',
            user: 'User',
            all: 'All',
            all_courses: 'All Courses',
            from_date: 'From Date',
            to_date: 'To Date',
            teachers: 'Teachers',
            students: 'Students',
        },
    },
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue"),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n)
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
