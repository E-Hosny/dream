<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                إدارة المدفوعات
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">فواتير الدفع</h3>
                                    <p class="text-gray-600 mt-2">إنشاء ومتابعة فواتير الدفع الإلكتروني للطلاب</p>
                                </div>
                                <Link
                                    :href="route('admin.payments.create')"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-600 to-amber-700 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-amber-700 hover:to-amber-800 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl"
                                >
                                    <svg class="w-5 h-5 mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    إنشاء فاتورة جديدة
                                </Link>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                                    <p class="text-blue-100 text-sm">إجمالي الفواتير</p>
                                    <p class="text-2xl font-bold">{{ stats?.total || 0 }}</p>
                                </div>
                                <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl p-6 text-white shadow-lg">
                                    <p class="text-amber-100 text-sm">غير مدفوعة</p>
                                    <p class="text-2xl font-bold">{{ stats?.unpaid || 0 }}</p>
                                </div>
                                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                                    <p class="text-green-100 text-sm">مدفوعة</p>
                                    <p class="text-2xl font-bold">{{ stats?.paid || 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                            {{ $page.props.flash.success }}
                        </div>
                        <div v-if="$page.props.errors?.error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
                            {{ $page.props.errors.error }}
                        </div>

                        <!-- Filters -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <input
                                v-model="searchForm.search"
                                type="text"
                                placeholder="بحث..."
                                class="border-gray-300 rounded-lg shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                @keyup.enter="applyFilters"
                            />
                            <select v-model="searchForm.course_id" class="border-gray-300 rounded-lg shadow-sm focus:border-amber-500 focus:ring-amber-500" @change="applyFilters">
                                <option value="">كل الكورسات</option>
                                <option v-for="course in courses" :key="course.id" :value="course.id">
                                    {{ course.title_ar || course.title }}
                                </option>
                            </select>
                            <select v-model="searchForm.student_id" class="border-gray-300 rounded-lg shadow-sm focus:border-amber-500 focus:ring-amber-500" @change="applyFilters">
                                <option value="">كل الطلاب</option>
                                <option v-for="student in students" :key="student.id" :value="student.id">
                                    {{ student.name }}
                                </option>
                            </select>
                            <select v-model="searchForm.status" class="border-gray-300 rounded-lg shadow-sm focus:border-amber-500 focus:ring-amber-500" @change="applyFilters">
                                <option value="">كل الحالات</option>
                                <option value="initiated">غير مدفوعة</option>
                                <option value="paid">مدفوعة</option>
                                <option value="failed">فاشلة</option>
                                <option value="expired">منتهية</option>
                                <option value="canceled">ملغاة</option>
                            </select>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">الطالب</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">الكورس</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">المبلغ</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">الحالة</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="payment in payments.data" :key="payment.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-medium text-gray-900">{{ payment.student?.name }}</div>
                                            <div class="text-sm text-gray-500">{{ payment.student?.email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                            {{ payment.course?.title_ar || payment.course?.title }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-center">
                                            {{ payment.amount_format || formatAmount(payment.amount, payment.currency) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span :class="getStatusClass(payment.status)" class="inline-block px-2 py-1 text-xs font-medium rounded-full">
                                                {{ getStatusText(payment.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ formatDate(payment.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            <div class="flex items-center justify-center gap-3">
                                                <a
                                                    v-if="payment.status === 'initiated'"
                                                    :href="payment.payment_url"
                                                    target="_blank"
                                                    class="text-amber-600 hover:text-amber-800 font-medium"
                                                >
                                                    فتح الرابط
                                                </a>
                                                <button
                                                    v-if="payment.status === 'initiated' && payment.payment_url"
                                                    type="button"
                                                    class="font-medium"
                                                    :class="copiedId === payment.id ? 'text-green-600' : 'text-blue-600 hover:text-blue-800'"
                                                    @click="copyPaymentLink(payment)"
                                                >
                                                    {{ copiedId === payment.id ? 'تم النسخ' : 'نسخ الرابط' }}
                                                </button>
                                                <button
                                                    v-if="payment.status === 'initiated'"
                                                    type="button"
                                                    class="text-gray-600 hover:text-gray-800 font-medium"
                                                    @click="cancelPayment(payment)"
                                                >
                                                    إلغاء
                                                </button>
                                                <button
                                                    v-if="payment.status !== 'paid'"
                                                    type="button"
                                                    class="text-red-600 hover:text-red-800 font-medium"
                                                    @click="deletePayment(payment)"
                                                >
                                                    حذف
                                                </button>
                                                <span v-if="payment.status === 'paid'" class="text-gray-400">—</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="!payments.data?.length">
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            لا توجد فواتير
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="payments.links?.length > 3" class="mt-6 flex justify-center">
                            <nav class="flex space-x-1 rtl:space-x-reverse">
                                <Link
                                    v-for="link in payments.links"
                                    :key="link.label"
                                    :href="link.url || '#'"
                                    :class="[
                                        'px-3 py-2 text-sm rounded-md',
                                        link.active ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50 border',
                                        !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                    ]"
                                >
                                    <span v-html="link.label"></span>
                                </Link>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    payments: Object,
    courses: Array,
    students: Array,
    stats: Object,
    filters: Object,
})

const searchForm = reactive({
    search: props.filters?.search || '',
    course_id: props.filters?.course_id || '',
    student_id: props.filters?.student_id || '',
    status: props.filters?.status || '',
})

const copiedId = ref(null)
let copiedTimeout = null

const copyPaymentLink = async (payment) => {
    const url = payment.payment_url
    if (!url) {
        return
    }

    try {
        await navigator.clipboard.writeText(url)
    } catch (error) {
        const input = document.createElement('textarea')
        input.value = url
        document.body.appendChild(input)
        input.select()
        document.execCommand('copy')
        document.body.removeChild(input)
    }

    copiedId.value = payment.id
    if (copiedTimeout) {
        clearTimeout(copiedTimeout)
    }
    copiedTimeout = setTimeout(() => {
        copiedId.value = null
    }, 2000)
}

const applyFilters = () => {
    router.get(route('admin.payments.index'), searchForm, {
        preserveState: true,
        preserveScroll: true,
    })
}

const cancelPayment = (payment) => {
    if (!confirm('هل تريد إلغاء هذه الفاتورة غير المدفوعة؟ لن يستطيع الطالب الدفع بعدها.')) {
        return
    }

    router.post(route('admin.payments.cancel', payment.id), {}, {
        preserveScroll: true,
    })
}

const deletePayment = (payment) => {
    if (!confirm('هل تريد حذف هذه الفاتورة؟ لا يمكن التراجع عن هذا الإجراء.')) {
        return
    }

    router.delete(route('admin.payments.destroy', payment.id), {
        preserveScroll: true,
    })
}

const formatAmount = (halalas, currency = 'SAR') => {
    const label = currency === 'SAR' ? 'ر.س' : currency
    return (halalas / 100).toFixed(2) + ' ' + label
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('ar-SA', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

const getStatusText = (status) => {
    const map = {
        initiated: 'غير مدفوعة',
        paid: 'مدفوعة',
        failed: 'فاشلة',
        expired: 'منتهية',
        canceled: 'ملغاة',
    }
    return map[status] || status
}

const getStatusClass = (status) => {
    const map = {
        initiated: 'bg-amber-100 text-amber-800',
        paid: 'bg-green-100 text-green-800',
        failed: 'bg-red-100 text-red-800',
        expired: 'bg-gray-100 text-gray-800',
        canceled: 'bg-gray-100 text-gray-600',
    }
    return map[status] || 'bg-gray-100 text-gray-800'
}
</script>
