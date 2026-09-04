<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    alerts: Array,
    recentLeads: Array,
    pendingOffers: Array,
    activeInstallations: Array,
    openTickets: Array,
});

const alertClasses = {
    warning: 'bg-amber-50 text-amber-800 border-amber-200',
    danger: 'bg-red-50 text-red-800 border-red-200',
};

function money(value) {
    return Number(value).toLocaleString('ro-RO', { maximumFractionDigits: 0 });
}

const offerStatusClasses = {
    draft: 'bg-slate-100 text-slate-600',
    sent: 'bg-blue-100 text-blue-700',
};

const installationStatusLabels = {
    scheduled: 'Programata',
    in_progress: 'In desfasurare',
};

const priorityClasses = {
    low: 'bg-slate-100 text-slate-600',
    medium: 'bg-amber-100 text-amber-800',
    high: 'bg-red-100 text-red-700',
};

function setOfferStatus(offer, status) {
    router.patch(route('sales.offers.status', offer.id), { status }, { preserveScroll: true });
}

function setInstallationStatus(installation, status) {
    router.patch(route('technical.installations.status', installation.id), { status }, { preserveScroll: true });
}

function resolveTicket(ticket) {
    router.patch(route('technical.tickets.status', ticket.id), { status: 'resolved' }, { preserveScroll: true });
}
</script>

<template>
    <Head title="Dashboard Administrativ" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Centru de comanda</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="alerts.length" class="mb-6 space-y-2">
                    <Link
                        v-for="(alert, index) in alerts"
                        :key="index"
                        :href="alert.href"
                        class="block rounded-md border p-3 text-sm font-medium hover:opacity-80"
                        :class="alertClasses[alert.type]"
                    >
                        {{ alert.message }}
                    </Link>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">Clienti</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ stats.clients }}</div>
                    </div>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">Oferte</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ stats.offers }}</div>
                    </div>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">Utilizatori</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ stats.users }}</div>
                    </div>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">Facturi neplatite</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ stats.invoicesUnpaid }}</div>
                    </div>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">Venit incasat</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ money(stats.revenuePaid) }} RON</div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Leaduri noi -->
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-500">Cereri noi (leaduri)</h3>
                            <Link :href="route('sales.clients.index', { status: 'lead' })" class="text-sm text-blue-600 hover:text-blue-500">Vezi toate</Link>
                        </div>
                        <div v-if="recentLeads.length" class="mt-4 divide-y divide-slate-100">
                            <div v-for="lead in recentLeads" :key="lead.id" class="py-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="font-medium text-slate-900">{{ lead.name }}</div>
                                        <div class="text-xs text-slate-400">{{ lead.phone }} &middot; {{ lead.source }}</div>
                                        <p v-if="lead.notes" class="mt-1 line-clamp-2 text-xs text-slate-500">{{ lead.notes }}</p>
                                    </div>
                                    <div class="flex flex-shrink-0 flex-col gap-1 text-right">
                                        <Link :href="route('sales.clients.show', lead.id)" class="text-xs font-medium text-slate-500 hover:text-slate-700">
                                            Vezi client
                                        </Link>
                                        <Link :href="route('sales.offers.create', { client_id: lead.id })" class="text-xs font-medium text-blue-600 hover:text-blue-500">
                                            Creeaza oferta
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p v-else class="mt-4 text-sm text-slate-400">Niciun lead nou.</p>
                    </div>

                    <!-- Oferte de procesat -->
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-500">Oferte de procesat</h3>
                            <Link :href="route('sales.offers.index')" class="text-sm text-blue-600 hover:text-blue-500">Vezi toate</Link>
                        </div>
                        <div v-if="pendingOffers.length" class="mt-4 divide-y divide-slate-100">
                            <div v-for="offer in pendingOffers" :key="offer.id" class="py-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <Link :href="route('sales.offers.show', offer.id)" class="font-medium text-slate-900 hover:text-blue-600">
                                            {{ offer.title }}
                                        </Link>
                                        <div class="text-xs text-slate-400">
                                            {{ offer.client.name }} &middot; {{ money(offer.total_amount) }} lei
                                            <span class="ml-1 rounded-full px-1.5 py-0.5" :class="offerStatusClasses[offer.status]">{{ offer.status }}</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-shrink-0 gap-2">
                                        <button
                                            v-if="offer.status === 'draft'"
                                            class="rounded-md border border-slate-300 px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50"
                                            @click="setOfferStatus(offer, 'sent')"
                                        >
                                            Trimite
                                        </button>
                                        <button
                                            class="rounded-md bg-green-600 px-2 py-1 text-xs font-medium text-white hover:bg-green-500"
                                            @click="setOfferStatus(offer, 'accepted')"
                                        >
                                            Accepta
                                        </button>
                                        <button
                                            class="rounded-md border border-red-300 px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50"
                                            @click="setOfferStatus(offer, 'rejected')"
                                        >
                                            Respinge
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p v-else class="mt-4 text-sm text-slate-400">Nicio oferta de procesat.</p>
                    </div>

                    <!-- Instalari active -->
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-500">Instalari in curs</h3>
                            <Link :href="route('technical.installations.index')" class="text-sm text-blue-600 hover:text-blue-500">Vezi toate</Link>
                        </div>
                        <div v-if="activeInstallations.length" class="mt-4 divide-y divide-slate-100">
                            <div v-for="installation in activeInstallations" :key="installation.id" class="py-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <Link :href="route('technical.installations.show', installation.id)" class="font-medium text-slate-900 hover:text-blue-600">
                                            {{ installation.client.name }}
                                        </Link>
                                        <div class="text-xs capitalize text-slate-400">
                                            {{ installation.type }} &middot; {{ installationStatusLabels[installation.status] }}
                                        </div>
                                    </div>
                                    <div class="flex flex-shrink-0 gap-2">
                                        <button
                                            v-if="installation.status === 'scheduled'"
                                            class="rounded-md border border-slate-300 px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50"
                                            @click="setInstallationStatus(installation, 'in_progress')"
                                        >
                                            Incepe
                                        </button>
                                        <button
                                            class="rounded-md bg-green-600 px-2 py-1 text-xs font-medium text-white hover:bg-green-500"
                                            @click="setInstallationStatus(installation, 'completed')"
                                        >
                                            Finalizeaza
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p v-else class="mt-4 text-sm text-slate-400">Nicio instalare in curs.</p>
                    </div>

                    <!-- Tichete deschise -->
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-500">Tichete deschise</h3>
                            <Link :href="route('technical.tickets.index')" class="text-sm text-blue-600 hover:text-blue-500">Vezi toate</Link>
                        </div>
                        <div v-if="openTickets.length" class="mt-4 divide-y divide-slate-100">
                            <div v-for="ticket in openTickets" :key="ticket.id" class="py-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <Link :href="route('technical.tickets.show', ticket.id)" class="font-medium text-slate-900 hover:text-blue-600">
                                            {{ ticket.subject }}
                                        </Link>
                                        <div class="text-xs text-slate-400">
                                            {{ ticket.client.name }}
                                            <span class="ml-1 rounded-full px-1.5 py-0.5" :class="priorityClasses[ticket.priority]">{{ ticket.priority }}</span>
                                        </div>
                                    </div>
                                    <button
                                        class="flex-shrink-0 rounded-md bg-green-600 px-2 py-1 text-xs font-medium text-white hover:bg-green-500"
                                        @click="resolveTicket(ticket)"
                                    >
                                        Rezolva
                                    </button>
                                </div>
                            </div>
                        </div>
                        <p v-else class="mt-4 text-sm text-slate-400">Niciun tichet deschis.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
