<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Link } from '@inertiajs/vue3';
defineProps({ invoices: Object, summary: Object });

function statusLabel(status) {
    return { unpaid: 'Neachitata', paid: 'Achitata', overdue: 'Restanta', cancelled: 'Anulata' }[status] || status;
}
function statusClass(status) {
    return {
        unpaid: 'bg-amber-100 text-amber-700',
        paid: 'bg-green-100 text-green-700',
        overdue: 'bg-red-100 text-red-700',
        cancelled: 'bg-slate-200 text-slate-600',
    }[status] || 'bg-slate-100 text-slate-600';
}
</script>
<template>
    <ClientLayout title="Facturile mele">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-2xl font-bold text-slate-900">Facturile mele</h1>
                <a :href="route('client.invoices.statement')" target="_blank" class="rounded-lg bg-brand-navy px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Descarca situatia financiara (PDF)</a>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <div class="text-sm text-slate-500">Total facturat</div>
                    <div class="mt-2 text-2xl font-bold text-slate-900">{{ summary.invoiceTotal.toFixed(2) }} lei</div>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <div class="text-sm text-slate-500">Total achitat</div>
                    <div class="mt-2 text-2xl font-bold text-green-600">{{ summary.invoicePaid.toFixed(2) }} lei</div>
                </div>
                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <div class="text-sm text-slate-500">Sold de plata</div>
                    <div class="mt-2 text-2xl font-bold text-red-600">{{ summary.invoiceBalance.toFixed(2) }} lei</div>
                </div>
            </div>

            <div class="mt-6 overflow-x-auto rounded-xl bg-white shadow-sm">
                <table class="min-w-full divide-y">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Numar</th>
                            <th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Emisa</th>
                            <th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Scadenta</th>
                            <th class="px-4 py-3 text-right text-xs uppercase text-slate-500">Total</th>
                            <th class="px-4 py-3 text-right text-xs uppercase text-slate-500">Achitat</th>
                            <th class="px-4 py-3 text-right text-xs uppercase text-slate-500">Sold</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="invoice in invoices.data" :key="invoice.id">
                            <td class="px-4 py-3 font-medium">{{ invoice.invoice_number }}</td>
                            <td class="px-4 py-3 text-sm">{{ invoice.issued_at || '-' }}</td>
                            <td class="px-4 py-3 text-sm">{{ invoice.due_at || '-' }}</td>
                            <td class="px-4 py-3 text-right">{{ Number(invoice.amount).toFixed(2) }} lei</td>
                            <td class="px-4 py-3 text-right">{{ Number(invoice.paid_amount).toFixed(2) }} lei</td>
                            <td class="px-4 py-3 text-right">{{ Math.max(Number(invoice.amount) - Number(invoice.paid_amount), 0).toFixed(2) }} lei</td>
                            <td class="px-4 py-3 text-right">
                                <span class="mr-3 rounded-full px-2 py-1 text-xs" :class="statusClass(invoice.status)">{{ statusLabel(invoice.status) }}</span>
                                <a :href="route('client.invoices.pdf', invoice.id)" class="text-sm text-blue-600">Descarca PDF</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p v-if="!invoices.data.length" class="p-6 text-slate-500">Nu exista facturi.</p>
            </div>
        </div>
    </ClientLayout>
</template>
