<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    client: Object,
});

const statusLabels = { lead: 'Lead', client: 'Client', inactive: 'Inactiv' };
const statusClasses = {
    lead: 'bg-amber-100 text-amber-800',
    client: 'bg-green-100 text-green-800',
    inactive: 'bg-slate-100 text-slate-600',
};

const offerStatusClasses = {
    draft: 'bg-slate-100 text-slate-600',
    sent: 'bg-blue-100 text-blue-700',
    accepted: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-700',
    expired: 'bg-slate-100 text-slate-500',
};

function money(value) {
    return Number(value).toLocaleString('ro-RO', { minimumFractionDigits: 2 });
}
</script>

<template>
    <Head :title="client.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ client.name }}</h2>
                <div class="flex gap-2">
                    <Link :href="route('sales.offers.create', { client_id: client.id })" class="rounded-md bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                        Oferta noua
                    </Link>
                    <Link :href="route('sales.clients.edit', client.id)" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Editeaza
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto grid max-w-7xl grid-cols-1 gap-6 sm:px-6 lg:grid-cols-3 lg:px-8">
                <div class="rounded-lg bg-white p-6 shadow-sm lg:col-span-1">
                    <h3 class="text-sm font-semibold text-slate-500">Detalii client</h3>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div v-if="client.company_name">
                            <dt class="text-slate-400">Firma</dt>
                            <dd class="text-slate-900">{{ client.company_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Telefon</dt>
                            <dd class="text-slate-900">{{ client.phone ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Email</dt>
                            <dd class="text-slate-900">{{ client.email ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Adresa</dt>
                            <dd class="text-slate-900">{{ [client.address, client.city, client.county].filter(Boolean).join(', ') || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Sursa</dt>
                            <dd class="capitalize text-slate-900">{{ client.source }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Status</dt>
                            <dd>
                                <span class="rounded-full px-2 py-1 text-xs font-medium" :class="statusClasses[client.status]">
                                    {{ statusLabels[client.status] }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Asignat</dt>
                            <dd class="text-slate-900">{{ client.assigned_to?.name ?? '-' }}</dd>
                        </div>
                        <div v-if="client.notes">
                            <dt class="text-slate-400">Notite</dt>
                            <dd class="whitespace-pre-line text-slate-900">{{ client.notes }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-500">Oferte ({{ client.offers.length }})</h3>
                        <div v-if="client.offers.length" class="mt-4 divide-y divide-slate-100">
                            <Link
                                v-for="offer in client.offers"
                                :key="offer.id"
                                :href="route('sales.offers.show', offer.id)"
                                class="flex items-center justify-between py-3 hover:bg-slate-50"
                            >
                                <div>
                                    <div class="font-medium text-slate-900">{{ offer.title }}</div>
                                    <div class="text-xs text-slate-400">{{ new Date(offer.created_at).toLocaleDateString('ro-RO') }}</div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-semibold text-slate-900">{{ money(offer.total_amount) }} lei</span>
                                    <span class="rounded-full px-2 py-1 text-xs font-medium" :class="offerStatusClasses[offer.status]">
                                        {{ offer.status }}
                                    </span>
                                </div>
                            </Link>
                        </div>
                        <p v-else class="mt-4 text-sm text-slate-400">Nicio oferta pentru acest client inca.</p>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-500">Instalari ({{ client.installations.length }})</h3>
                        <div v-if="client.installations.length" class="mt-4 divide-y divide-slate-100">
                            <div v-for="installation in client.installations" :key="installation.id" class="flex items-center justify-between py-3">
                                <div>
                                    <div class="font-medium text-slate-900">{{ installation.address ?? 'Adresa nespecificata' }}</div>
                                    <div class="text-xs text-slate-400">
                                        {{ installation.scheduled_at ? new Date(installation.scheduled_at).toLocaleString('ro-RO') : 'Neprogramat' }}
                                    </div>
                                </div>
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium capitalize text-slate-600">
                                    {{ installation.status.replace('_', ' ') }}
                                </span>
                            </div>
                        </div>
                        <p v-else class="mt-4 text-sm text-slate-400">Nicio instalare programata.</p>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-500">Facturi ({{ client.invoices.length }})</h3>
                        <div v-if="client.invoices.length" class="mt-4 divide-y divide-slate-100">
                            <div v-for="invoice in client.invoices" :key="invoice.id" class="flex items-center justify-between py-3">
                                <div class="font-medium text-slate-900">{{ invoice.invoice_number }}</div>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-semibold text-slate-900">{{ money(invoice.amount) }} lei</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-medium capitalize text-slate-600">
                                        {{ invoice.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <p v-else class="mt-4 text-sm text-slate-400">Nicio factura emisa.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
