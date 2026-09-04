<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    recentLeads: Array,
    recentOffers: Array,
});

function money(value) {
    return Number(value).toLocaleString('ro-RO', { maximumFractionDigits: 0 });
}
</script>

<template>
    <Head title="Dashboard Vanzari" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Modul Vanzari</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">Clienti</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ stats.clients }}</div>
                    </div>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">Lead-uri active</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ stats.leads }}</div>
                    </div>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">Oferte</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ stats.offers }}</div>
                    </div>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">Oferte acceptate</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ stats.offersAccepted }}</div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-500">Lead-uri recente</h3>
                            <Link :href="route('sales.clients.index', { status: 'lead' })" class="text-sm text-blue-600 hover:text-blue-500">Vezi toate</Link>
                        </div>
                        <div v-if="recentLeads.length" class="mt-4 divide-y divide-slate-100">
                            <Link
                                v-for="lead in recentLeads"
                                :key="lead.id"
                                :href="route('sales.clients.show', lead.id)"
                                class="flex items-center justify-between py-3 hover:bg-slate-50"
                            >
                                <div>
                                    <div class="font-medium text-slate-900">{{ lead.name }}</div>
                                    <div class="text-xs text-slate-400">{{ lead.phone }} &middot; {{ lead.source }}</div>
                                </div>
                                <div class="text-xs text-slate-400">{{ new Date(lead.created_at).toLocaleDateString('ro-RO') }}</div>
                            </Link>
                        </div>
                        <p v-else class="mt-4 text-sm text-slate-400">Niciun lead nou.</p>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-500">Oferte recente</h3>
                            <Link :href="route('sales.offers.index')" class="text-sm text-blue-600 hover:text-blue-500">Vezi toate</Link>
                        </div>
                        <div v-if="recentOffers.length" class="mt-4 divide-y divide-slate-100">
                            <Link
                                v-for="offer in recentOffers"
                                :key="offer.id"
                                :href="route('sales.offers.show', offer.id)"
                                class="flex items-center justify-between py-3 hover:bg-slate-50"
                            >
                                <div>
                                    <div class="font-medium text-slate-900">{{ offer.title }}</div>
                                    <div class="text-xs text-slate-400">{{ offer.client.name }} &middot; {{ offer.status }}</div>
                                </div>
                                <div class="text-sm font-semibold text-slate-900">{{ money(offer.total_amount) }} lei</div>
                            </Link>
                        </div>
                        <p v-else class="mt-4 text-sm text-slate-400">Nicio oferta creata inca.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
