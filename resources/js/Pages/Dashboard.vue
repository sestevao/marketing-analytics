<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { PlusIcon, TrashIcon, GlobeAltIcon, ChartBarIcon } from '@heroicons/vue/24/outline';
import axios from 'axios';
import LineChart from '@/Components/LineChart.vue';
import { computed } from 'vue';

const props = defineProps({
    accounts: Array,
    recentLeads: Object,
});

const showModal = ref(false);
const selectedAccount = ref(null);
const analyticsData = ref(null);
const loadingAnalytics = ref(false);
const selectedMetric = ref('impressions');

const chartData = computed(() => {
    if (!analyticsData.value || !analyticsData.value.daily_data) return [];
    
    return analyticsData.value.daily_data.map(item => ({
        date: item.date,
        value: item.impressions // Default to impressions for now
    }));
});

const form = useForm({
    name: '',
    platform: 'google',
    account_id: '',
    access_token: '', // Mocking token input
});

const submit = () => {
    form.post(route('ad-accounts.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};

const deleteAccount = (id) => {
    if (confirm('Are you sure you want to disconnect this account?')) {
        useForm({}).delete(route('ad-accounts.destroy', id), {
             onSuccess: () => {
                 if (selectedAccount.value && selectedAccount.value.id === id) {
                     selectedAccount.value = null;
                     analyticsData.value = null;
                 }
             }
        });
    }
};

const viewAnalytics = async (account) => {
    selectedAccount.value = account;
    loadingAnalytics.value = true;
    analyticsData.value = null;
    try {
        const response = await axios.get(route('ad-accounts.analytics', account.id));
        analyticsData.value = response.data;
    } catch (error) {
        console.error(error);
        alert('Failed to load analytics');
    } finally {
        loadingAnalytics.value = false;
    }
};

const platforms = [
    { id: 'google', name: 'Google Ads' },
    { id: 'facebook', name: 'Facebook Ads' },
    { id: 'linkedin', name: 'LinkedIn Ads' },
    { id: 'other', name: 'Other SEO Account' },
];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Marketing Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">                
                        <!-- Connected Accounts Section -->
                        <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Connected Accounts</h3>
                                <button
                                    @click="showModal = true"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    <PlusIcon class="w-4 h-4 mr-2" />
                                    Connect Account
                                </button>
                            </div>

                            <div v-if="accounts.length === 0" class="text-center py-8 text-gray-500">
                                No accounts connected yet. Connect your ad accounts to see analytics.
                            </div>

                            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div v-for="account in accounts" :key="account.id" class="border rounded-lg p-4 flex flex-col justify-between hover:shadow-md transition bg-white" :class="{'ring-2 ring-indigo-500': selectedAccount?.id === account.id}">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center">
                                            <div class="p-2 bg-gray-100 rounded-full mr-3">
                                                <!-- Simple icon logic -->
                                                <GlobeAltIcon class="w-6 h-6 text-gray-600" />
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ account.name }}</h4>
                                                <p class="text-sm text-gray-500 capitalize">{{ account.platform }}</p>
                                            </div>
                                        </div>
                                        <span :class="{'bg-green-100 text-green-800': account.is_active, 'bg-red-100 text-red-800': !account.is_active}" class="px-2 py-1 text-xs rounded-full">
                                            {{ account.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    
                                    <div class="mt-4 pt-4 border-t flex justify-between items-center">
                                        <button @click="viewAnalytics(account)" class="text-indigo-600 hover:text-indigo-800 text-sm flex items-center font-medium">
                                            <ChartBarIcon class="w-4 h-4 mr-1" />
                                            View Analytics
                                        </button>
                                        <button @click="deleteAccount(account.id)" class="text-red-600 hover:text-red-800 text-sm flex items-center">
                                            <TrashIcon class="w-4 h-4 mr-1" />
                                            Disconnect
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Analytics Overview -->
                        <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Analytics Overview</h3>
                            
                            <div v-if="loadingAnalytics" class="h-64 flex items-center justify-center text-gray-500">
                                Loading...
                            </div>

                            <div v-else-if="analyticsData" class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xl font-bold text-gray-800">{{ analyticsData.platform }} Performance</h4>
                                    <span class="text-sm text-gray-500">Last 30 Days</span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                        <div class="text-sm text-blue-600 font-medium">Impressions</div>
                                        <div class="text-2xl font-bold text-blue-900">{{ analyticsData.impressions.toLocaleString() }}</div>
                                    </div>
                                    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                                        <div class="text-sm text-green-600 font-medium">Clicks</div>
                                        <div class="text-2xl font-bold text-green-900">{{ analyticsData.clicks.toLocaleString() }}</div>
                                    </div>
                                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                                        <div class="text-sm text-purple-600 font-medium">Conversions</div>
                                        <div class="text-2xl font-bold text-purple-900">{{ analyticsData.conversions.toLocaleString() }}</div>
                                    </div>
                                    <div class="bg-pink-50 p-4 rounded-lg border border-pink-100">
                                        <div class="text-sm text-pink-600 font-medium">Leads</div>
                                        <div class="text-2xl font-bold text-pink-900">{{ analyticsData.leads.toLocaleString() }}</div>
                                    </div>
                                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                                        <div class="text-sm text-yellow-600 font-medium">Spend</div>
                                        <div class="text-2xl font-bold text-yellow-900">${{ analyticsData.spend.toLocaleString() }}</div>
                                    </div>
                                </div>

                                <!-- Chart -->
                                <div class="mt-6 h-64 bg-white border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <h5 class="text-sm font-medium text-gray-500 capitalize">{{ selectedMetric }} Trend</h5>
                                        <select v-model="selectedMetric" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <option value="impressions">Impressions</option>
                                            <option value="clicks">Clicks</option>
                                            <option value="conversions">Conversions</option>
                                            <option value="leads">Leads</option>
                                            <option value="spend">Spend</option>
                                        </select>
                                    </div>
                                    <LineChart :data="chartData" :label="selectedMetric.charAt(0).toUpperCase() + selectedMetric.slice(1)" />
                                </div>



                            </div>

                            <div v-else class="h-64 bg-gray-50 rounded flex items-center justify-center text-gray-400">
                                Select an account above to view detailed analytics
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <!-- Recent Leads (All Accounts) -->
                        <div class="bg-white p-6 shadow-sm sm:rounded-lg h-full overflow-hidden flex flex-col">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Leads (All Accounts)</h3>
                            
                            <div v-if="!recentLeads.data || recentLeads.data.length === 0" class="text-center py-8 text-gray-500">
                                No leads found recently. Connect accounts to see leads.
                            </div>

                            <div v-else class="flex-1 flex flex-col min-h-0">
                                <div class="overflow-x-auto border rounded-lg flex-1">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Platform</th>
                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="lead in recentLeads.data" :key="lead.id" :class="{'bg-indigo-50': selectedAccount?.id === lead.account_id, 'transition-colors duration-200': true}">
                                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500">{{ lead.created_at.substring(0, 10) }}</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ lead.name }}</td>
                                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-500 capitalize">{{ lead.platform }}</td>
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                        :class="{
                                                            'bg-gray-100 text-gray-800': lead.status === 'New',
                                                            'bg-yellow-100 text-yellow-800': lead.status === 'Contacted',
                                                            'bg-blue-100 text-blue-800': lead.status === 'Qualified',
                                                            'bg-green-100 text-green-800': lead.status === 'Converted'
                                                        }">
                                                        {{ lead.status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Pagination -->
                                <div class="mt-4 flex flex-col gap-2">
                                    <div class="text-xs text-gray-500 text-center">
                                        Showing <span class="font-medium">{{ recentLeads.from }}</span> to <span class="font-medium">{{ recentLeads.to }}</span> of <span class="font-medium">{{ recentLeads.total }}</span>
                                    </div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px justify-center" aria-label="Pagination">
                                        <Component
                                            :is="link.url ? Link : 'span'"
                                            v-for="(link, index) in recentLeads.links"
                                            :key="index"
                                            :href="link.url"
                                            v-html="link.label"
                                            class="relative inline-flex items-center px-2 py-1 border text-xs font-medium"
                                            :class="{
                                                'z-10 bg-indigo-50 border-indigo-500 text-indigo-600': link.active,
                                                'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active && link.url,
                                                'bg-gray-100 text-gray-400 border-gray-300': !link.url
                                            }"
                                        />
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Connect Ad Account
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Account Name</label>
                                        <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g. My Google Ads">
                                        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Platform</label>
                                        <select v-model="form.platform" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option v-for="p in platforms" :key="p.id" :value="p.id">{{ p.name }}</option>
                                        </select>
                                         <div v-if="form.errors.platform" class="text-red-500 text-xs mt-1">{{ form.errors.platform }}</div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Account ID (Optional)</label>
                                        <input v-model="form.account_id" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    
                                     <div>
                                        <label class="block text-sm font-medium text-gray-700">Access Token (Mock)</label>
                                        <textarea v-model="form.access_token" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Paste API Token here"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="submit" :disabled="form.processing" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Connect
                        </button>
                        <button @click="showModal = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
