<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    invoice: Object,
    clients: Array,
    offers: Array,
});

const form = useForm({
    client_id: props.invoice.client_id,
    offer_id: props.invoice.offer_id,
    invoice_number: props.invoice.invoice_number,
    amount: Number(props.invoice.amount),
    status: props.invoice.status,
    issued_at: props.invoice.issued_at ? props.invoice.issued_at.substring(0, 10) : '',
    due_at: props.invoice.due_at ? props.invoice.due_at.substring(0, 10) : '',
});

function submit() {
    form.put(route('admin.invoices.update', props.invoice.id));
}
</script>

<template>
    <Head title="Editeaza factura" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Editeaza factura {{ invoice.invoice_number }}</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <form class="space-y-6 rounded-lg bg-white p-6 shadow-sm" @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Client *</label>
                            <select v-model.number="form.client_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Numar factura</label>
                            <input v-model="form.invoice_number" type="text" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Suma (lei) *</label>
                            <input v-model.number="form.amount" type="number" min="0" step="0.01" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Status</label>
                            <select v-model="form.status" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="unpaid">Neplatita</option>
                                <option value="paid">Platita</option>
                                <option value="overdue">Restanta</option>
                                <option value="cancelled">Anulata</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Data emiterii</label>
                            <input v-model="form.issued_at" type="date" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Scadenta</label>
                            <input v-model="form.due_at" type="date" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500 disabled:opacity-50">
                            Salveaza modificarile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
