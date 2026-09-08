<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    invoice: Object,
});
const page = usePage();

const statusClasses = {
    unpaid: 'bg-amber-100 text-amber-800',
    paid: 'bg-green-100 text-green-800',
    overdue: 'bg-red-100 text-red-700',
    cancelled: 'bg-slate-100 text-slate-500',
};

function money(value) {
    return Number(value).toLocaleString('ro-RO', { minimumFractionDigits: 2 });
}

function markPaid() {
    router.patch(route('admin.invoices.pay', props.invoice.id), {}, { preserveScroll: true });
}

function syncFgo() {
    router.post(route('admin.invoices.fgo-sync', props.invoice.id), {}, { preserveScroll: true });
}

function destroy() {
    if (confirm('Stergi aceasta factura?')) {
        router.delete(route('admin.invoices.destroy', props.invoice.id));
    }
}
</script>

<template>
    <Head :title="invoice.invoice_number" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Factura {{ invoice.invoice_number }}</h2>
                <div class="flex gap-2">
                    <a :href="route('admin.invoices.pdf', invoice.id)" target="_blank" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Descarca PDF
                    </a>
                    <Link :href="route('admin.invoices.edit', invoice.id)" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Editeaza
                    </Link>
                    <button class="rounded-md border border-red-300 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50" @click="destroy">
                        Sterge
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div v-if="page.props.flash.success" class="mb-4 rounded-md bg-green-50 p-3 text-sm text-green-800">{{ page.props.flash.success }}</div>
                    <div v-if="page.props.flash.error" class="mb-4 rounded-md bg-red-50 p-3 text-sm text-red-800">{{ page.props.flash.error }}</div>
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-slate-500">Client</div>
                            <Link :href="route('sales.clients.show', invoice.client.id)" class="font-medium text-blue-600 hover:text-blue-500">
                                {{ invoice.client.name }}
                            </Link>
                        </div>
                        <span class="rounded-full px-3 py-1 text-sm font-medium" :class="statusClasses[invoice.status]">
                            {{ invoice.status }}
                        </span>
                    </div>

                    <dl class="mt-6 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-slate-400">Suma</dt>
                            <dd class="text-lg font-semibold text-slate-900">{{ money(invoice.amount) }} lei</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Oferta asociata</dt>
                            <dd class="text-slate-900">{{ invoice.offer?.title ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Data emiterii</dt>
                            <dd class="text-slate-900">{{ invoice.issued_at ? new Date(invoice.issued_at).toLocaleDateString('ro-RO') : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400">Scadenta</dt>
                            <dd class="text-slate-900">{{ invoice.due_at ? new Date(invoice.due_at).toLocaleDateString('ro-RO') : '-' }}</dd>
                        </div>
                        <div v-if="invoice.paid_at">
                            <dt class="text-slate-400">Data platii</dt>
                            <dd class="text-slate-900">{{ new Date(invoice.paid_at).toLocaleDateString('ro-RO') }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 rounded-md border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="text-sm font-medium text-slate-700">Integrare FGO</div>
                                <div class="mt-1 text-xs text-slate-500">
                                    <span v-if="invoice.fgo_id">ID FGO: {{ invoice.fgo_id }}</span>
                                    <span v-else>Factura nu este sincronizata.</span>
                                </div>
                            </div>
                            <span
                                class="rounded-full px-2 py-1 text-xs font-medium"
                                :class="invoice.fgo_status === 'error' ? 'bg-red-100 text-red-700' : invoice.fgo_id ? 'bg-green-100 text-green-700' : 'bg-slate-200 text-slate-600'"
                            >
                                {{ invoice.fgo_status ?? 'neconfigurata' }}
                            </span>
                        </div>
                        <p v-if="invoice.fgo_error" class="mt-2 text-xs text-red-600">{{ invoice.fgo_error }}</p>
                        <button class="mt-3 rounded-md border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-white" @click="syncFgo">
                            Sincronizeaza cu FGO
                        </button>
                    </div>

                    <button
                        v-if="invoice.status === 'unpaid'"
                        class="mt-6 w-full rounded-md bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-500"
                        @click="markPaid"
                    >
                        Marcheaza ca platita
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
