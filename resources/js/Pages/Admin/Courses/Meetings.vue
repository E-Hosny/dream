<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, usePage, Link, router } from '@inertiajs/vue3';
import { computed, nextTick, ref, watch } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'ar');

const props = defineProps({
    course: Object,
    meetings: Array,
    sessionStats: Object,
});

const meetingsList = ref([...(props.meetings || [])]);
const dueNoticeSummary = ref(props.course?.due_notice_summary || null);
const prepaid = ref(props.course?.prepaid || { remaining: 0, applied_count: 0, remaining_value: 0, remaining_value_format: '0.00 ر.س' });
const prepaidCount = ref(null);
const prepaidInput = ref(null);
const showPrepaidForm = ref(false);
const sessionStats = ref(props.sessionStats || page.props.sessionStats || null);
watch(() => props.sessionStats, (value) => {
    sessionStats.value = value || null;
});
const paidDisplayCount = computed(() => (sessionStats.value?.paid_taken_sessions || 0) + (prepaid.value?.remaining || 0));
const paidDisplayAmount = computed(() => {
    const takenAmount = (sessionStats.value?.paid_amount || 0) - (sessionStats.value?.prepaid_amount || 0);
    const total = takenAmount + (Number(prepaid.value?.remaining_value) || 0);
    return Number(total).toFixed(2) + ' ر.س';
});
const togglingPaymentId = ref(null);
const selectedIds = ref([]);
const bulkBusy = ref(false);

const changeStatsMonth = (event) => {
    router.get(route('admin.courses.meetings', props.course.id), {
        stats_month: event.target.value || undefined,
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true,
    });
};

const formatMonthOption = (monthKey) => {
    if (!monthKey) return '';
    const [year, month] = monthKey.split('-');
    const date = new Date(Number(year), Number(month) - 1, 1);
    return date.toLocaleDateString(currentLocale.value === 'ar' ? 'ar-SA-u-ca-gregory' : 'en-US', {
        year: 'numeric',
        month: 'long',
    });
};

const t = (key) => {
    const translations = {
        en: {
            course_details: 'Course Details',
            back_to_courses: 'Back to Courses',
            meetings_history: 'Meetings History',
            start_time: 'Start Time',
            end_time: 'End Time',
            meeting_duration: 'Duration',
            session_price: 'Session price',
            payment_status: 'Payment',
            paid: 'Paid',
            unpaid: 'Unpaid',
            mark_as_paid: 'Mark as paid',
            mark_as_unpaid: 'Mark as unpaid',
            mark_selected_paid: 'Mark selected as paid',
            mark_selected_unpaid: 'Mark selected as unpaid',
            show_due_notice: 'Show due notice to student',
            clear_due_notice: 'Clear due notice',
            select_all: 'Select all',
            clear_selection: 'Clear selection',
            selected_count: 'selected',
            due_notice_active: 'Due notice',
            due_notice_banner: 'Student due notice is active',
            password: 'Password',
            no_meetings: 'No meetings yet',
            minutes: 'Minutes',
            assignment: 'Assignment',
            view: 'View',
            download: 'Download',
            student_solutions: 'Student Solutions',
            no_assignment_uploaded: 'No assignment uploaded yet',
            delete: 'Delete',
            delete_meeting: 'Delete Meeting',
            select_meetings_first: 'Select at least one meeting first',
            select_unpaid_for_notice: 'Select unpaid meetings to show the due notice',
            month_stats: 'Monthly sessions summary',
            filter_by_month: 'Month',
            total_sessions: 'Sessions taken',
            paid_sessions: 'Paid sessions',
            unpaid_sessions: 'Unpaid sessions',
            paid_value: 'Paid value',
            unpaid_value: 'Unpaid value',
            total_value: 'Total value',
            due_notice_sessions: 'Notified due sessions',
            prepaid_title: 'Prepaid',
            prepaid_count: 'Sessions',
            add_prepaid: 'Add prepaid sessions',
            save_prepaid: 'Save',
            cancel: 'Cancel',
            prepaid_badge: 'Prepaid',
            prepaid_in_paid: 'prepaid remaining',
            enter_prepaid_count: 'Enter how many sessions were prepaid',
        },
        ar: {
            course_details: 'تفاصيل الكورس',
            back_to_courses: 'العودة للكورسات',
            meetings_history: 'تاريخ الاجتماعات',
            start_time: 'وقت البداية',
            end_time: 'وقت النهاية',
            meeting_duration: 'المدة',
            session_price: 'سعر الحصة',
            payment_status: 'الدفع',
            paid: 'مدفوعة',
            unpaid: 'غير مدفوعة',
            mark_as_paid: 'تعليمليم كمدفوعة',
            mark_as_unpaid: 'إلغاء التعليم كمدفوعة',
            mark_selected_paid: 'تعليمليم المحدد كمدفوع',
            mark_selected_unpaid: 'تعليمليم المحدد كغير مدفوع',
            show_due_notice: 'إظهار إشعار المستحقات للطالب',
            clear_due_notice: 'إزالة إشعار المستحقات',
            select_all: 'تحديد الكل',
            clear_selection: 'إلغاء التحديد',
            selected_count: 'محدد',
            due_notice_active: 'إشعار مستحق',
            due_notice_banner: 'إشعار المستحقات مفعّل للطالب',
            password: 'كلمة المرور',
            no_meetings: 'لا توجد اجتماعات بعد',
            minutes: 'دقائق',
            assignment: 'الواجب',
            view: 'عرض',
            download: 'تحميل',
            student_solutions: 'حلول الطلاب',
            no_assignment_uploaded: 'لم يتم رفع واجب بعد',
            delete: 'حذف',
            delete_meeting: 'حذف الاجتماع',
            select_meetings_first: 'حدد حصة واحدة على الأقل أولاً',
            select_unpaid_for_notice: 'حدد حصصاً غير مدفوعة لإظهار إشعار المستحقات',
            month_stats: 'ملخص حصص الشهر',
            filter_by_month: 'الشهر',
            total_sessions: 'حصص تم أخذها',
            paid_sessions: 'حصص مدفوعة',
            unpaid_sessions: 'حصص غير مدفوعة',
            paid_value: 'قيمة المدفوع',
            unpaid_value: 'قيمة غير المدفوع',
            total_value: 'الإجمالي',
            due_notice_sessions: 'حصص بإشعار مستحق',
            prepaid_title: 'دفع مقدم',
            prepaid_count: 'عدد الحصص',
            add_prepaid: 'إضافة حصص مدفوعة مقدماً',
            save_prepaid: 'حفظ',
            cancel: 'إلغاء',
            prepaid_badge: 'مدفوعة مقدماً',
            prepaid_in_paid: 'مدفوعة مقدماً',
            enter_prepaid_count: 'أدخل عدد الحصص المدفوعة مقدماً',
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

const csrfHeaders = () => ({
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json',
    'Content-Type': 'application/json',
});

const formatDateTime = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const options = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        timeZone: 'Asia/Riyadh'
    };
    return date.toLocaleString(currentLocale.value === 'ar' ? 'ar-SA-u-ca-gregory' : 'en-US', options);
};

const allSelected = computed(() =>
    meetingsList.value.length > 0 && selectedIds.value.length === meetingsList.value.length
);

const selectedUnpaidIds = computed(() =>
    meetingsList.value
        .filter((m) => selectedIds.value.includes(m.id) && !m.is_paid)
        .map((m) => m.id)
);

const toggleSelect = (id) => {
    if (selectedIds.value.includes(id)) {
        selectedIds.value = selectedIds.value.filter((item) => item !== id);
    } else {
        selectedIds.value = [...selectedIds.value, id];
    }
};

const toggleSelectAll = () => {
    if (allSelected.value) {
        selectedIds.value = [];
    } else {
        selectedIds.value = meetingsList.value.map((m) => m.id);
    }
};

const clearSelection = () => {
    selectedIds.value = [];
};

const applyLiveStats = (data) => {
    dueNoticeSummary.value = data.due_notice_summary || dueNoticeSummary.value;
    if (data.prepaid) prepaid.value = data.prepaid;
    if (data.session_stats) sessionStats.value = data.session_stats;
};

const statsMonthPayload = () => ({
    stats_month: sessionStats.value?.month || undefined,
});

const openPrepaidForm = async () => {
    prepaidCount.value = null;
    showPrepaidForm.value = true;
    await nextTick();
    prepaidInput.value?.focus();
};

const cancelPrepaidForm = () => {
    showPrepaidForm.value = false;
    prepaidCount.value = null;
};

const applyMeetingUpdates = (updates) => {
    const map = new Map(updates.map((item) => [item.id, item]));
    meetingsList.value = meetingsList.value.map((meeting) => {
        const update = map.get(meeting.id);
        return update ? { ...meeting, ...update } : meeting;
    });
};

const viewAssignment = (assignment) => {
    window.open(`/assignments/${assignment.id}/view`, '_blank');
};

const downloadAssignment = (assignment) => {
    window.open(`/assignments/${assignment.id}/download`, '_blank');
};

const viewSubmissions = (assignment) => {
    window.open(`/assignments/${assignment.id}/submissions`, '_blank');
};

const togglePayment = async (meeting) => {
    if (togglingPaymentId.value || bulkBusy.value) {
        return;
    }

    togglingPaymentId.value = meeting.id;

    try {
        const response = await fetch(route('admin.courses.meetings.payment', [props.course.id, meeting.id]), {
            method: 'PATCH',
            headers: csrfHeaders(),
            body: JSON.stringify(statsMonthPayload()),
        });

        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Failed to update payment status');
        }

        applyMeetingUpdates([{
            id: meeting.id,
            is_paid: data.is_paid,
            is_prepaid: data.is_prepaid,
            due_notice: data.due_notice,
            session_price: data.session_price,
            session_price_format: data.session_price_format,
        }]);
        applyLiveStats(data);
    } catch (error) {
        console.error('Error toggling meeting payment:', error);
        alert(currentLocale.value === 'ar'
            ? `تعذر تحديث حالة الدفع: ${error.message}`
            : `Could not update payment status: ${error.message}`
        );
    } finally {
        togglingPaymentId.value = null;
    }
};

const bulkUpdatePayment = async (isPaid) => {
    if (!selectedIds.value.length) {
        alert(t('select_meetings_first'));
        return;
    }
    if (bulkBusy.value) return;

    bulkBusy.value = true;
    try {
        const response = await fetch(route('admin.courses.meetings.bulk-payment', props.course.id), {
            method: 'PATCH',
            headers: csrfHeaders(),
            body: JSON.stringify({
                meeting_ids: selectedIds.value,
                is_paid: isPaid,
                ...statsMonthPayload(),
            }),
        });
        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Bulk update failed');
        }
        applyMeetingUpdates(data.meetings || []);
        applyLiveStats(data);
        clearSelection();
    } catch (error) {
        console.error('Error bulk updating payment:', error);
        alert(currentLocale.value === 'ar'
            ? `تعذر تحديث الحصص: ${error.message}`
            : `Could not update meetings: ${error.message}`
        );
    } finally {
        bulkBusy.value = false;
    }
};

const addPrepaid = async () => {
    const count = Number(prepaidCount.value);
    if (!count || count < 1) {
        alert(t('enter_prepaid_count'));
        return;
    }
    if (bulkBusy.value) return;

    bulkBusy.value = true;
    try {
        const response = await fetch(route('admin.courses.meetings.prepaid', props.course.id), {
            method: 'PATCH',
            headers: csrfHeaders(),
            body: JSON.stringify({
                sessions: count,
                ...statsMonthPayload(),
            }),
        });
        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Failed to add prepaid sessions');
        }
        applyMeetingUpdates(data.meetings || []);
        applyLiveStats(data);
        cancelPrepaidForm();
    } catch (error) {
        console.error('Error adding prepaid sessions:', error);
        alert(currentLocale.value === 'ar'
            ? `تعذر إضافة الدفع المقدم: ${error.message}`
            : `Could not add prepaid sessions: ${error.message}`
        );
    } finally {
        bulkBusy.value = false;
    }
};

const showDueNotice = async () => {
    if (!selectedUnpaidIds.value.length) {
        alert(t('select_unpaid_for_notice'));
        return;
    }
    if (bulkBusy.value) return;

    bulkBusy.value = true;
    try {
        const response = await fetch(route('admin.courses.meetings.due-notice', props.course.id), {
            method: 'PATCH',
            headers: csrfHeaders(),
            body: JSON.stringify({
                meeting_ids: selectedUnpaidIds.value,
                due_notice: true,
            }),
        });
        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Failed to enable due notice');
        }
        applyMeetingUpdates(data.meetings || []);
        dueNoticeSummary.value = data.due_notice_summary || null;
        clearSelection();
    } catch (error) {
        console.error('Error enabling due notice:', error);
        alert(currentLocale.value === 'ar'
            ? `تعذر تفعيل الإشعار: ${error.message}`
            : `Could not enable notice: ${error.message}`
        );
    } finally {
        bulkBusy.value = false;
    }
};

const clearDueNotice = async () => {
    if (bulkBusy.value) return;

    const confirmClear = confirm(currentLocale.value === 'ar'
        ? 'هل تريد إزالة إشعار المستحقات من صفحة الطالب؟'
        : 'Clear the due notice from the student page?'
    );
    if (!confirmClear) return;

    bulkBusy.value = true;
    try {
        const response = await fetch(route('admin.courses.meetings.due-notice', props.course.id), {
            method: 'PATCH',
            headers: csrfHeaders(),
            body: JSON.stringify({
                meeting_ids: [],
                due_notice: false,
            }),
        });
        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Failed to clear due notice');
        }
        applyMeetingUpdates(data.meetings || []);
        dueNoticeSummary.value = data.due_notice_summary || null;
        clearSelection();
    } catch (error) {
        console.error('Error clearing due notice:', error);
        alert(currentLocale.value === 'ar'
            ? `تعذر إزالة الإشعار: ${error.message}`
            : `Could not clear notice: ${error.message}`
        );
    } finally {
        bulkBusy.value = false;
    }
};

const deleteMeeting = async (meeting) => {
    if (!confirm(currentLocale.value === 'ar' ?
        'هل أنت متأكد من حذف هذا الاجتماع؟ سيتم حذف جميع البيانات المرتبطة به أيضاً.' :
        'Are you sure you want to delete this meeting? All related data will be deleted too.'
    )) {
        return;
    }

    try {
        const response = await fetch(`/admin/courses/${props.course.id}/meetings/${meeting.id}`, {
            method: 'DELETE',
            headers: csrfHeaders(),
        });

        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            if (data.success) {
                alert(currentLocale.value === 'ar' ? 'تم حذف الاجتماع بنجاح!' : 'Meeting deleted successfully!');
                window.location.reload();
            } else {
                throw new Error(data.message || 'حدث خطأ أثناء حذف الاجتماع');
            }
        } else if (response.ok) {
            alert(currentLocale.value === 'ar' ? 'تم حذف الاجتماع بنجاح!' : 'Meeting deleted successfully!');
            window.location.reload();
        } else {
            try {
                const data = await response.json();
                throw new Error(data.message || 'حدث خطأ أثناء حذف الاجتماع');
            } catch (jsonError) {
                throw new Error(currentLocale.value === 'ar' ? 'حدث خطأ أثناء حذف الاجتماع' : 'Error deleting meeting');
            }
        }
    } catch (error) {
        console.error('Error deleting meeting:', error);
        alert(currentLocale.value === 'ar' ?
            `حدث خطأ أثناء حذف الاجتماع: ${error.message}` :
            `Error deleting meeting: ${error.message}`
        );
    }
};
</script>

<template>
    <Head :title="t('course_details')" />

    <AdminLayout>
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <Link :href="route('admin.courses.index')" class="inline-flex items-center text-blue-600 hover:text-blue-700 mb-4">
                        <svg class="w-4 h-4 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ t('back_to_courses') }}
                    </Link>
                    <h1 class="text-3xl font-bold text-gray-900">{{ t('course_details') }}</h1>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto mb-6">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ currentLocale === 'ar' ? course.title : course.titleEn }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ t('session_price') }}: <span class="font-semibold text-amber-700">{{ course.session_price_format }}</span>
                    </p>
                </div>
                <div class="flex items-end gap-2">
                    <button
                        v-if="!showPrepaidForm"
                        type="button"
                        @click="openPrepaidForm"
                        class="px-4 py-2 rounded-lg text-sm font-medium bg-emerald-600 text-white hover:bg-emerald-700"
                    >
                        {{ t('add_prepaid') }}
                    </button>
                    <template v-else>
                        <input
                            ref="prepaidInput"
                            v-model.number="prepaidCount"
                            type="number"
                            min="1"
                            max="200"
                            class="w-32 rounded-lg border-gray-300 focus:border-brand focus:ring-brand"
                            :placeholder="t('prepaid_count')"
                            :aria-label="t('prepaid_count')"
                            @keydown.enter.prevent="addPrepaid"
                        >
                        <button
                            type="button"
                            @click="addPrepaid"
                            :disabled="bulkBusy"
                            class="px-4 py-2 rounded-lg text-sm font-medium bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-50"
                        >
                            {{ t('save_prepaid') }}
                        </button>
                        <button
                            type="button"
                            @click="cancelPrepaidForm"
                            class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100"
                        >
                            {{ t('cancel') }}
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <div v-if="sessionStats" class="max-w-6xl mx-auto mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ t('month_stats') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ currentLocale === 'ar' ? sessionStats.month_label_ar : sessionStats.month_label_en }}
                    </p>
                </div>
                <div class="w-full sm:w-56">
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('filter_by_month') }}</label>
                    <select
                        :value="sessionStats.month"
                        class="w-full rounded-lg border-gray-300 focus:border-brand focus:ring-brand"
                        @change="changeStatsMonth"
                    >
                        <option
                            v-for="monthKey in sessionStats.month_options"
                            :key="monthKey"
                            :value="monthKey"
                        >
                            {{ formatMonthOption(monthKey) }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                    <p class="text-xs text-slate-600 mb-1">{{ t('total_sessions') }}</p>
                    <p class="text-2xl font-bold text-slate-900">{{ sessionStats.total_sessions }}</p>
                    <p class="text-sm font-semibold text-slate-700 mt-1">{{ sessionStats.total_amount_format }}</p>
                </div>
                <div class="rounded-xl bg-green-50 border border-green-200 p-4">
                    <p class="text-xs text-green-700 mb-1">{{ t('paid_sessions') }}</p>
                    <p class="text-2xl font-bold text-green-800">{{ paidDisplayCount }}</p>
                    <p class="text-sm font-semibold text-green-700 mt-1">{{ paidDisplayAmount }}</p>
                    <div
                        v-if="prepaid.remaining"
                        class="mt-2 inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800"
                    >
                        {{ t('prepaid_in_paid') }}: {{ prepaid.remaining }} ({{ prepaid.remaining_value_format }})
                    </div>
                </div>
                <div class="rounded-xl bg-amber-50 border border-amber-200 p-4">
                    <p class="text-xs text-amber-700 mb-1">{{ t('unpaid_sessions') }}</p>
                    <p class="text-2xl font-bold text-amber-800">{{ sessionStats.unpaid_sessions }}</p>
                    <p class="text-sm font-semibold text-amber-700 mt-1">{{ sessionStats.unpaid_amount_format }}</p>
                    <p v-if="sessionStats.due_notice_sessions" class="text-xs text-amber-600 mt-2">
                        {{ t('due_notice_sessions') }}: {{ sessionStats.due_notice_sessions }}
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex flex-col gap-4 mb-6">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <h2 class="text-xl font-semibold text-gray-900">{{ t('meetings_history') }}</h2>
                        <div v-if="meetingsList.length" class="flex items-center gap-2 flex-wrap">
                            <button
                                type="button"
                                @click="toggleSelectAll"
                                class="px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50"
                            >
                                {{ allSelected ? t('clear_selection') : t('select_all') }}
                            </button>
                            <span v-if="selectedIds.length" class="text-xs text-gray-500">
                                {{ selectedIds.length }} {{ t('selected_count') }}
                            </span>
                        </div>
                    </div>

                    <div
                        v-if="dueNoticeSummary"
                        class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900"
                    >
                        <p class="font-semibold">{{ t('due_notice_banner') }}</p>
                        <p class="mt-1">
                            {{ currentLocale === 'ar' ? dueNoticeSummary.message_ar : dueNoticeSummary.message_en }}
                        </p>
                    </div>

                    <div
                        v-if="meetingsList.length"
                        class="flex flex-wrap gap-2 p-3 rounded-lg bg-gray-50 border border-gray-200"
                    >
                        <button
                            type="button"
                            @click="bulkUpdatePayment(true)"
                            :disabled="bulkBusy || !selectedIds.length"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium bg-green-600 text-white hover:bg-green-700 disabled:opacity-50"
                        >
                            {{ t('mark_selected_paid') }}
                        </button>
                        <button
                            type="button"
                            @click="bulkUpdatePayment(false)"
                            :disabled="bulkBusy || !selectedIds.length"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium bg-amber-600 text-white hover:bg-amber-700 disabled:opacity-50"
                        >
                            {{ t('mark_selected_unpaid') }}
                        </button>
                        <button
                            type="button"
                            @click="showDueNotice"
                            :disabled="bulkBusy || !selectedUnpaidIds.length"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{ t('show_due_notice') }}
                        </button>
                        <button
                            type="button"
                            @click="clearDueNotice"
                            :disabled="bulkBusy || !dueNoticeSummary"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-700 text-white hover:bg-gray-800 disabled:opacity-50"
                        >
                            {{ t('clear_due_notice') }}
                        </button>
                    </div>
                </div>

                <div v-if="meetingsList.length > 0" class="space-y-6">
                    <div
                        v-for="meeting in meetingsList"
                        :key="meeting.id"
                        class="p-6 rounded-lg border transition-all duration-200 hover:shadow-md"
                        :class="[
                            meeting.is_paid ? 'bg-green-50/40 border-green-200' : 'border-gray-200 hover:border-blue-300',
                            selectedIds.includes(meeting.id) ? 'ring-2 ring-blue-400' : '',
                            meeting.due_notice && !meeting.is_paid ? 'border-amber-400' : '',
                        ]"
                    >
                        <div class="flex items-start gap-3 mb-4">
                            <input
                                type="checkbox"
                                class="mt-1.5 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                :checked="selectedIds.includes(meeting.id)"
                                @change="toggleSelect(meeting.id)"
                            >
                            <div class="flex-1 flex items-center justify-between gap-3 flex-wrap">
                                <h3 class="font-medium text-gray-900 text-lg">{{ meeting.topic }}</h3>
                                <div class="flex items-center space-x-2 rtl:space-x-reverse flex-wrap gap-2">
                                    <span
                                        v-if="meeting.due_notice && !meeting.is_paid"
                                        class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800"
                                    >
                                        {{ t('due_notice_active') }}
                                    </span>
                                    <span
                                        v-if="meeting.is_prepaid && meeting.is_paid"
                                        class="px-3 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800"
                                    >
                                        {{ t('prepaid_badge') }}
                                    </span>
                                    <span
                                        class="px-3 py-1 text-xs font-medium rounded-full"
                                        :class="meeting.is_paid ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800'"
                                    >
                                        {{ meeting.is_paid ? t('paid') : t('unpaid') }}
                                    </span>
                                    <span :class="`px-3 py-1 text-xs font-medium rounded-full ${meeting.status_color}`">
                                        {{ meeting.status_text }}
                                    </span>
                                    <button
                                        type="button"
                                        @click="togglePayment(meeting)"
                                        :disabled="togglingPaymentId === meeting.id || bulkBusy"
                                        class="px-3 py-1.5 rounded-lg transition-colors text-xs font-medium disabled:opacity-50"
                                        :class="meeting.is_paid
                                            ? 'bg-amber-600 text-white hover:bg-amber-700'
                                            : 'bg-green-600 text-white hover:bg-green-700'"
                                    >
                                        {{ meeting.is_paid ? t('mark_as_unpaid') : t('mark_as_paid') }}
                                    </button>
                                    <button
                                        @click="deleteMeeting(meeting)"
                                        class="px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-xs font-medium"
                                        :title="t('delete_meeting')"
                                    >
                                        {{ t('delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 text-sm text-gray-700 mb-4 ps-7">
                            <div>
                                <span class="font-medium">{{ t('start_time') }}:</span>
                                <br>{{ formatDateTime(meeting.actual_start_time || meeting.start_time) }}
                            </div>
                            <div>
                                <span class="font-medium">{{ t('end_time') }}:</span>
                                <br>{{ formatDateTime(meeting.actual_end_time || meeting.end_time) }}
                            </div>
                            <div>
                                <span class="font-medium">{{ t('meeting_duration') }}:</span>
                                <br>{{ meeting.duration }} {{ t('minutes') }}
                            </div>
                            <div>
                                <span class="font-medium">{{ t('session_price') }}:</span>
                                <br>
                                <span class="font-semibold text-amber-700">{{ meeting.session_price_format }}</span>
                            </div>
                        </div>

                        <div v-if="meeting.password" class="mb-4 text-sm ps-7">
                            <span class="font-medium text-gray-700">{{ t('password') }}:</span>
                            <code class="ml-2 bg-gray-100 px-2 py-1 rounded">{{ meeting.password }}</code>
                        </div>

                        <div class="border-t pt-4 ps-7">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-medium text-gray-800">{{ t('assignment') }}</h4>
                                <div class="flex space-x-2 rtl:space-x-reverse" v-if="meeting.assignment">
                                    <button @click="viewAssignment(meeting.assignment)"
                                            class="px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-xs font-medium">
                                        {{ t('view') }}
                                    </button>
                                    <button @click="downloadAssignment(meeting.assignment)"
                                            class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-xs font-medium">
                                        {{ t('download') }}
                                    </button>
                                    <button @click="viewSubmissions(meeting.assignment)"
                                            class="px-3 py-1.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-xs font-medium">
                                        {{ t('student_solutions') }} ({{ meeting.assignment.submissions_count || 0 }})
                                    </button>
                                </div>
                            </div>

                            <div v-if="meeting.assignment" class="bg-gray-50 p-3 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="font-medium text-gray-900">{{ meeting.assignment.title }}</h5>
                                    <span class="text-xs text-gray-500">{{ meeting.assignment.formatted_file_size }}</span>
                                </div>
                                <p v-if="meeting.assignment.description" class="text-sm text-gray-600 mb-2">
                                    {{ meeting.assignment.description }}
                                </p>
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ meeting.assignment.file_name }}</span>
                                    <span>{{ formatDateTime(meeting.assignment.created_at) }}</span>
                                </div>
                            </div>

                            <div v-else class="text-center py-4 text-gray-500 text-sm">
                                {{ t('no_assignment_uploaded') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ t('no_meetings') }}</h3>
                    <p class="text-gray-500 mb-4">{{ currentLocale === 'ar' ? 'لم يتم عقد أي اجتماعات بعد' : 'No meetings have been held yet' }}</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
