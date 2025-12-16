<script setup>
import StudentLayout from '@/Layouts/StudentLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'ar');
const isRTL = computed(() => currentLocale.value === 'ar');

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            help_support: 'Help & Support',
            contact_us: 'Contact Us',
            we_here_help: 'We\'re here to help!',
            support_description: 'Get in touch with our support team through any of the following channels. We\'re available to assist you with any questions or concerns.',
            whatsapp: 'WhatsApp',
            whatsapp_description: 'Chat with us on WhatsApp for instant support',
            call_us: 'Call Us',
            call_description: 'Speak directly with our support team',
            email_us: 'Email Us',
            email_description: 'Send us an email and we\'ll get back to you soon',
            contact_whatsapp: 'Contact via WhatsApp',
            contact_phone: 'Call Now',
            contact_email: 'Send Email',
            phone_number: 'Phone Number',
            email_address: 'Email Address',
        },
        ar: {
            help_support: 'المساعدة والدعم',
            contact_us: 'تواصل معنا',
            we_here_help: 'نحن هنا لمساعدتك!',
            support_description: 'تواصل مع فريق الدعم لدينا من خلال أي من القنوات التالية. نحن متاحون لمساعدتك في أي استفسارات أو مخاوف.',
            whatsapp: 'واتساب',
            whatsapp_description: 'تواصل معنا عبر واتساب للحصول على دعم فوري',
            call_us: 'اتصل بنا',
            call_description: 'تحدث مباشرة مع فريق الدعم لدينا',
            email_us: 'راسلنا',
            email_description: 'أرسل لنا بريداً إلكترونياً وسنعاود الاتصال بك قريباً',
            contact_whatsapp: 'تواصل عبر واتساب',
            contact_phone: 'اتصل الآن',
            contact_email: 'أرسل بريد إلكتروني',
            phone_number: 'رقم الهاتف',
            email_address: 'البريد الإلكتروني',
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

// Contact information
const whatsappNumber = '966542327025';
const phoneNumber = '966542327025';
const emailAddress = 'inskola.academy@gmail.com';

// WhatsApp link
const whatsappLink = computed(() => {
    const message = isRTL.value 
        ? 'مرحباً، أحتاج إلى مساعدة' 
        : 'Hello, I need help';
    return `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
});

// Phone link
const phoneLink = computed(() => `tel:${phoneNumber}`);

// Email link
const emailLink = computed(() => `mailto:${emailAddress}?subject=${encodeURIComponent(isRTL.value ? 'طلب مساعدة' : 'Support Request')}`);
</script>

<template>
    <Head :title="t('help_support')" />
    
    <StudentLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 via-brand/10 to-brand-light/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Header -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4" :class="isRTL ? 'font-cairo' : ''">
                        {{ t('help_support') }}
                    </h1>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto" :class="isRTL ? 'font-cairo' : ''">
                        {{ t('we_here_help') }}
                    </p>
                    <p class="text-gray-500 mt-2 max-w-2xl mx-auto" :class="isRTL ? 'font-cairo' : ''">
                        {{ t('support_description') }}
                    </p>
                </div>

                <!-- Contact Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    <!-- WhatsApp Card -->
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                        <div class="bg-gradient-to-br from-green-500 to-green-600 p-6">
                            <div class="flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mx-auto mb-4">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white text-center" :class="isRTL ? 'font-cairo' : ''">
                                {{ t('whatsapp') }}
                            </h3>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-4 text-center" :class="isRTL ? 'font-cairo' : ''">
                                {{ t('whatsapp_description') }}
                            </p>
                            <a 
                                :href="whatsappLink" 
                                target="_blank"
                                class="block w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 text-center shadow-md hover:shadow-lg"
                                :class="isRTL ? 'font-cairo' : ''"
                            >
                                {{ t('contact_whatsapp') }}
                            </a>
                        </div>
                    </div>

                    <!-- Phone Card -->
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                        <div class="bg-gradient-to-br from-brand to-brand-dark p-6">
                            <div class="flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mx-auto mb-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white text-center" :class="isRTL ? 'font-cairo' : ''">
                                {{ t('call_us') }}
                            </h3>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-4 text-center" :class="isRTL ? 'font-cairo' : ''">
                                {{ t('call_description') }}
                            </p>
                            <a 
                                :href="phoneLink" 
                                class="block w-full bg-brand hover:bg-brand-dark text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 text-center shadow-md hover:shadow-lg"
                                :class="isRTL ? 'font-cairo' : ''"
                            >
                                {{ t('contact_phone') }}
                            </a>
                            <p class="text-sm text-gray-500 mt-3 text-center" :class="isRTL ? 'font-cairo' : ''">
                                {{ phoneNumber }}
                            </p>
                        </div>
                    </div>

                    <!-- Email Card -->
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6">
                            <div class="flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mx-auto mb-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-white text-center" :class="isRTL ? 'font-cairo' : ''">
                                {{ t('email_us') }}
                            </h3>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-4 text-center" :class="isRTL ? 'font-cairo' : ''">
                                {{ t('email_description') }}
                            </p>
                            <a 
                                :href="emailLink" 
                                class="block w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 text-center shadow-md hover:shadow-lg"
                                :class="isRTL ? 'font-cairo' : ''"
                            >
                                {{ t('contact_email') }}
                            </a>
                            <p class="text-sm text-gray-500 mt-3 text-center break-all" :class="isRTL ? 'font-cairo' : ''">
                                {{ emailAddress }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>

<style scoped>
/* Custom styles if needed */
</style>

