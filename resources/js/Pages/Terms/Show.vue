<script setup>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import TeacherLayout from '@/Layouts/TeacherLayout.vue';

const page = usePage();
const userRoles = computed(() => page.props.auth.user?.roles || []);

const LayoutComponent = computed(() => {
    if (userRoles.value.includes('admin')) {
        return AdminLayout;
    }

    if (userRoles.value.includes('teacher')) {
        return TeacherLayout;
    }

    return StudentLayout;
});
</script>

<template>
    <Head title="الشروط والأحكام" />

    <component :is="LayoutComponent">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sm:p-10">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-8 text-center">
                    الشروط والأحكام وسياسة الخصوصية والاسترجاع
                </h1>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-brand-dark mb-4">أولاً: الشروط والأحكام</h2>
                    <ol class="list-decimal list-inside space-y-2 text-gray-700 leading-relaxed">
                        <li>باستخدام منصة InSkola Academy فإن المستخدم يوافق على جميع الشروط والأحكام الواردة في هذه الوثيقة.</li>
                        <li>تلتزم المنصة بتقديم الخدمات التعليمية عن بُعد وفق المواعيد المتفق عليها.</li>
                        <li>يلتزم الطالب وولي الأمر بتوفير بيئة مناسبة للحصة التعليمية والاتصال بالإنترنت.</li>
                        <li>يلتزم الطالب بالحضور في الوقت المحدد للحصة.</li>
                    </ol>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-brand-dark mb-4">ثانياً: سياسة الحضور والاعتذار</h2>
                    <ol class="list-decimal list-inside space-y-2 text-gray-700 leading-relaxed">
                        <li>يحق للطالب طلب تأجيل أو إعادة جدولة الحصة عند إشعار المنصة قبل موعد الحصة بـ 6 ساعات على الأقل.</li>
                        <li>في حال الاعتذار قبل المدة المحددة يتم إعادة جدولة الحصة حسب المواعيد المتاحة.</li>
                        <li>في حال التغيب أو الاعتذار بعد بدء الحصة أو قبلها بأقل من 6 ساعات تعتبر الحصة مستهلكة ولا يحق المطالبة بالتعويض عنها إلا في الحالات الطارئة التي توافق عليها إدارة المنصة.</li>
                        <li>في حال تأخر الطالب عن موعد الحصة يتم احتساب الوقت المتبقي من الحصة فقط.</li>
                        <li>في حال تعذر إقامة الحصة بسبب المنصة أو المعلم يتم تعويض الطالب بحصة بديلة.</li>
                    </ol>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-brand-dark mb-4">ثالثاً: سياسة الاسترجاع</h2>
                    <ol class="list-decimal list-inside space-y-2 text-gray-700 leading-relaxed">
                        <li>يحق للعميل طلب استرجاع المبلغ قبل بدء أول حصة فقط.</li>
                        <li>بعد بدء تقديم الخدمة أو حضور أي حصة لا يمكن استرجاع قيمة الحصص المنفذة.</li>
                        <li>يتم احتساب قيمة الحصص المنفذة وخصمها قبل إعادة أي مبلغ متبقٍ.</li>
                        <li>تتم معالجة طلبات الاسترجاع خلال مدة لا تتجاوز 14 يوم عمل.</li>
                    </ol>
                </section>

                <section class="mb-8">
                    <h2 class="text-xl font-semibold text-brand-dark mb-4">رابعاً: سياسة الخصوصية</h2>
                    <ol class="list-decimal list-inside space-y-2 text-gray-700 leading-relaxed">
                        <li>تلتزم المنصة بالمحافظة على سرية بيانات المستخدمين وعدم مشاركتها مع أي طرف ثالث إلا وفق الأنظمة المعمول بها.</li>
                        <li>يتم استخدام البيانات لأغراض تقديم الخدمة التعليمية والتواصل مع الطالب وولي الأمر فقط.</li>
                        <li>يحق للمنصة إرسال الإشعارات المتعلقة بالحضور والواجبات والتقييمات والخدمات المقدمة.</li>
                        <li>يتم حفظ البيانات بوسائل آمنة لحمايتها من الوصول غير المصرح به.</li>
                    </ol>
                </section>

                <section>
                    <h2 class="text-xl font-semibold text-brand-dark mb-4">خامساً: أحكام عامة</h2>
                    <ol class="list-decimal list-inside space-y-2 text-gray-700 leading-relaxed">
                        <li>تخضع هذه الشروط والاتفاقية لأنظمة المملكة العربية السعودية وأحكام الشريعة الإسلامية.</li>
                        <li>يحق للمنصة تحديث هذه الشروط والسياسات في أي وقت مع نشر النسخة المحدثة عبر المنصة أو وسائل التواصل الرسمية.</li>
                        <li>استمرار استخدام المنصة بعد نشر التحديثات يعد موافقة عليها.</li>
                    </ol>
                </section>
            </div>
        </div>
    </component>
</template>
