<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';

const props = defineProps({
    invoices: Object,
    filters: Object,
    summary: Object,
});

const form = reactive({
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
});

let debounceTimer = null;
watch(form, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('admin.invoices.index'), form, { preserveState: true, replace: true });
    }, 300);
});

const statusClasses = {
    unpaid: 'bg-amber-100 text-amber-800',
    partial: 'bg-blue-100 text-blue-800',
    paid: 'bg-green-100 text-green-800',
    overdue: 'bg-red-100 text-red-700',
    cancelled: 'bg-slate-100 text-slate-500',
};

function money(value) {
    return Number(value).toLocaleString('ro-RO', { minimumFractionDigits: 2 });
}

function isPartial(invoice) {
    return invoice.status === 'unpaid' && Number(invoice.paid_amount) > 0;
}

function exportUrl() {
    const params = new URLSearchParams(form).toString();
    return route('admin.invoices.export') + (params ? `?${params}` : '');
}

function markPaid(invoice) {
    router.patch(route('admin.invoices.pay', invoice.id), {}, { preserveScroll: true });
}
</script>

<template>
    <Head title="Facturi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Facturi</h2>
                <div class="flex gap-2">
                    <a :href="exportUrl()" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Export Excel
                    </a>
                    <Link :href="route('admin.invoices.create')" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                        Factura noua
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="text-sm text-slate-500">Neincasat</div>
                        <div class="text-2xl font-semibold text-amber-600">{{ money(summary.unpaid) }} lei</div>
                    </div>
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="text-sm text-slate-500">Incasat</div>
                        <div class="text-2xl font-semibold text-green-600">{{ money(summary.paid) }} lei</div>
                    </div>
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="text-sm text-slate-500">Facturi restante</div>
                        <div class="text-2xl font-semibold" :class="summary.overdue > 0 ? 'text-red-600' : 'text-slate-900'">{{ summary.overdue }}</div>
                    </div>
                </div>

                <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <input v-model="form.search" type="text" placeholder="Cauta dupa numar factura sau client..." class="rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    <select v-model="form.status" class="rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Toate statusurile</option>
                        <option value="unpaid">Neplatita</option>
                        <option value="partial">Plata partiala</option>
                        <option value="paid">Platita</option>
                        <option value="overdue">Restanta</option>
                        <option value="cancelled">Anulata</option>
                    </select>
                </div>

                <div class="rounded-lg bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Numar</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Client</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-slate-500">Suma</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Scadenta</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Status</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="invoice in invoices.data" :key="invoice.id" class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <Link :href="route('admin.invoices.show', invoice.id)" class="font-medium text-blue-600 hover:text-blue-500">
                                        {{ invoice.invoice_number }}
                                    </Link>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ invoice.client.name }}</td>
                                <td class="px-4 py-3 text-right font-medium text-slate-900">
                                    {{ money(invoice.amount) }} lei
                                    <div v-if="Number(invoice.paid_amount) > 0" class="text-xs text-slate-500">Achitat: {{ money(invoice.paid_amount) }} lei</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ invoice.due_at ? new Date(invoice.due_at).toLocaleDateString('ro-RO') : '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium" :class="isPartial(invoice) ? statusClasses.partial : statusClasses[invoice.status]">{{ isPartial(invoice) ? 'Plata partiala' : invoice.status }}</span>
                                </td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <button v-if="['unpaid', 'overdue'].includes(invoice.status)" class="text-green-600 hover:text-green-700" @click="markPaid(invoice)">
                                        Inregistreaza plata
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!invoices.data.length">
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400">Nicio factura gasita.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>

                <div v-if="invoices.links.length > 3" class="mt-4 flex flex-wrap gap-2">
                    <Link
                        v-for="link in invoices.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        :class="[
                            'rounded-md px-3 py-1.5 text-sm',
                            link.active ? 'bg-blue-600 text-white' : 'bg-white text-slate-500 hover:bg-slate-100',
                            !link.url ? 'pointer-events-none opacity-40' : '',
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
