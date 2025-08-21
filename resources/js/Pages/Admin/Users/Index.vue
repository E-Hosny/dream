<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const currentLocale = computed(() => page.props.locale || 'en');

defineProps({
    users: Object,
    roles: Array,
    filters: Object
});

const searchForm = ref({
    search: '',
    role: ''
});

const search = () => {
    router.get(route('admin.users.index'), searchForm.value, {
        preserveState: true,
        replace: true
    });
};

// Translation helper
const t = (key) => {
    const translations = {
        en: {
            users_management: 'Users Management',
            add_new_user: 'Add New User',
            search_users: 'Search users...',
            filter_by_role: 'Filter by role',
            all_roles: 'All Roles',
            name: 'Name',
            email: 'Email',
            role: 'Role',
            created_at: 'Created At',
            actions: 'Actions',
            edit: 'Edit',
            delete: 'Delete',
            view: 'View',
            no_users: 'No users found',
            search: 'Search',
            clear: 'Clear'
        },
        ar: {
            users_management: 'إدارة المستخدمين',
            add_new_user: 'إضافة مستخدم جديد',
            search_users: 'البحث عن المستخدمين...',
            filter_by_role: 'فلترة حسب الدور',
            all_roles: 'جميع الأدوار',
            name: 'الاسم',
            email: 'البريد الإلكتروني',
            role: 'الدور',
            created_at: 'تاريخ الإنشاء',
            actions: 'الإجراءات',
            edit: 'تعديل',
            delete: 'حذف',
            view: 'عرض',
            no_users: 'لا يوجد مستخدمين',
            search: 'بحث',
            clear: 'مسح'
        }
    };
    return translations[currentLocale.value]?.[key] || key;
};

const deleteUser = (user) => {
    if (confirm(`Are you sure you want to delete ${user.name}?`)) {
        router.delete(route('admin.users.destroy', user.id));
    }
};
</script>

<template>
    <Head :title="t('users_management')" />

    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ t('users_management') }}</h1>
                <p class="text-gray-600 mt-2">{{ currentLocale === 'ar' ? 'إدارة المستخدمين والأدوار' : 'Manage users and their roles' }}</p>
            </div>
            <Link :href="route('admin.users.create')" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg">
                <svg class="w-5 h-5 inline mr-2 rtl:mr-0 rtl:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ t('add_new_user') }}
            </Link>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-lg border-0 p-6 mb-6 backdrop-blur-sm bg-white/95">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('search') }}</label>
                    <input 
                        v-model="searchForm.search"
                        type="text" 
                        :placeholder="t('search_users')"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        @keyup.enter="search"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('filter_by_role') }}</label>
                    <select v-model="searchForm.role" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ t('all_roles') }}</option>
                        <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                    </select>
                </div>
                <div class="flex items-end space-x-2 rtl:space-x-reverse">
                    <button @click="search" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                        {{ t('search') }}
                    </button>
                    <button @click="searchForm = { search: '', role: '' }; search()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                        {{ t('clear') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-2xl shadow-xl border-0 overflow-hidden backdrop-blur-sm bg-white/95">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left rtl:text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ t('name') }}
                            </th>
                            <th class="px-6 py-3 text-left rtl:text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ t('email') }}
                            </th>
                            <th class="px-6 py-3 text-left rtl:text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ t('role') }}
                            </th>
                            <th class="px-6 py-3 text-left rtl:text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ t('created_at') }}
                            </th>
                            <th class="px-6 py-3 text-left rtl:text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ t('actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                                            {{ user.name.charAt(0) }}
                                        </div>
                                    </div>
                                    <div class="ml-4 rtl:ml-0 rtl:mr-4">
                                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ user.email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span v-for="role in user.roles" :key="role.id" :class="[
                                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                    role.name === 'admin' ? 'bg-red-100 text-red-800' :
                                    role.name === 'teacher' ? 'bg-green-100 text-green-800' :
                                    'bg-blue-100 text-blue-800'
                                ]">
                                    {{ role.name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ new Date(user.created_at).toLocaleDateString() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right rtl:text-left text-sm font-medium">
                                <div class="flex space-x-2 rtl:space-x-reverse">
                                    <Link :href="route('admin.users.show', user.id)" class="text-indigo-600 hover:text-indigo-900">
                                        {{ t('view') }}
                                    </Link>
                                    <Link :href="route('admin.users.edit', user.id)" class="text-green-600 hover:text-green-900">
                                        {{ t('edit') }}
                                    </Link>
                                    <button @click="deleteUser(user)" class="text-red-600 hover:text-red-900">
                                        {{ t('delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- No Results -->
            <div v-if="users.data.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a.75.75 0 01-.75.75H5.25a.75.75 0 010-1.5h13.5a.75.75 0 01.75.75z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">{{ t('no_users') }}</h3>
            </div>

            <!-- Pagination -->
            <div v-if="users.links && users.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        {{ currentLocale === 'ar' ? 'عرض' : 'Showing' }} {{ users.from }} {{ currentLocale === 'ar' ? 'إلى' : 'to' }} {{ users.to }} {{ currentLocale === 'ar' ? 'من' : 'of' }} {{ users.total }} {{ currentLocale === 'ar' ? 'نتيجة' : 'results' }}
                    </div>
                    <div class="flex space-x-1 rtl:space-x-reverse">
                        <Link
                            v-for="link in users.links"
                            :key="link.label"
                            :href="link.url"
                            :class="[
                                'px-3 py-2 text-sm rounded-md',
                                link.active 
                                    ? 'bg-indigo-600 text-white' 
                                    : link.url 
                                        ? 'text-gray-700 hover:text-gray-900 hover:bg-gray-100' 
                                        : 'text-gray-400 cursor-not-allowed'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
