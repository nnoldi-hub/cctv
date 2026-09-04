<script setup>
import { computed } from 'vue';

const props = defineProps({
    form: Object,
    clients: Array,
    offers: Array,
    technicians: Array,
});

const filteredOffers = computed(() => props.offers.filter((o) => o.client_id === props.form.client_id));

function useClientAddress() {
    const client = props.clients.find((c) => c.id === props.form.client_id);
    if (client && !props.form.address) {
        props.form.address = [client.address, client.city].filter(Boolean).join(', ');
    }
}
</script>

<template>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-slate-700">Client *</label>
            <select v-model.number="form.client_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" @change="useClientAddress">
                <option :value="null" disabled>Selecteaza client</option>
                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
            </select>
            <p v-if="form.errors.client_id" class="mt-1 text-sm text-red-600">{{ form.errors.client_id }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Oferta acceptata (optional)</label>
            <select v-model.number="form.offer_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option :value="null">Fara oferta asociata</option>
                <option v-for="offer in filteredOffers" :key="offer.id" :value="offer.id">{{ offer.title }}</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Tip</label>
            <select v-model="form.type" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="instalare">Instalare</option>
                <option value="interventie">Interventie</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Tehnician</label>
            <select v-model.number="form.technician_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option :value="null">Neasignat</option>
                <option v-for="tech in technicians" :key="tech.id" :value="tech.id">{{ tech.name }}</option>
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Adresa</label>
            <input v-model="form.address" type="text" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Latitudine (optional)</label>
            <input v-model.number="form.latitude" type="number" step="0.0000001" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Longitudine (optional)</label>
            <input v-model.number="form.longitude" type="number" step="0.0000001" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Data si ora programate</label>
            <input v-model="form.scheduled_at" type="datetime-local" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Status</label>
            <select v-model="form.status" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="scheduled">Programata</option>
                <option value="in_progress">In desfasurare</option>
                <option value="completed">Finalizata</option>
                <option value="cancelled">Anulata</option>
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Note tehnice</label>
            <textarea v-model="form.notes" rows="3" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
        </div>
    </div>
</template>
