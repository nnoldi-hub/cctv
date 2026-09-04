<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    clients: Array,
    offers: Array,
    nextInvoiceNumber: String,
    preselectedClientId: Number,
});

const form = useForm({
    client_id: props.preselectedClientId ?? null,
    offer_id: null,
    invoice_number: props.nextInvoiceNumber,
    amount: 0,
    status: 'unpaid',
    issued_at: new Date().toISOString().substring(0, 10),
    due_at: '',
});

const filteredOffers = computed(() => props.offers.filter((o) => o.client_id === form.client_id));

function applyOffer() {
    const offer = props.offers.find((o) => o.id === form.offer_id);
    if (offer) {
        form.amount = Number(offer.total_amount);
    }
}

function submit() {
    form.post(route('admin.invoices.store'));
}
</script>

<template>
    <Head title="Factura noua" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Factura noua</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <form class="space-y-6 rounded-lg bg-white p-6 shadow-sm" @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Client *</label>
                            <select v-model.number="form.client_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option :value="null" disabled>Selecteaza client</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                            </select>
                            <p v-if="form.errors.client_id" class="mt-1 text-sm text-red-600">{{ form.errors.client_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Oferta acceptata (optional)</label>
                            <select v-model.number="form.offer_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" @change="applyOffer">
                                <option :value="null">Fara oferta asociata</option>
                                <option v-for="offer in filteredOffers" :key="offer.id" :value="offer.id">{{ offer.title }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Numar factura</label>
                            <input v-model="form.invoice_number" type="text" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Suma (lei) *</label>
                            <input v-model.number="form.amount" type="number" min="0" step="0.01" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            <p v-if="form.errors.amount" class="mt-1 text-sm text-red-600">{{ form.errors.amount }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Data emiterii</label>
                            <input v-model="form.issued_at" type="date" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Scadenta</label>
                            <input v-model="form.due_at" type="date" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Status</label>
                            <select v-model="form.status" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="unpaid">Neplatita</option>
                                <option value="paid">Platita</option>
                                <option value="cancelled">Anulata</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500 disabled:opacity-50">
                            Salveaza factura
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
