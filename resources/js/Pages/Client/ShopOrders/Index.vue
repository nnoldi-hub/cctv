<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({ orders: Object });

const statusLabels = {
    new: 'Noua',
    confirmed: 'Confirmata',
    shipped: 'Expediata',
    completed: 'Finalizata',
    cancelled: 'Anulata',
};

function money(value) {
    return Number(value).toLocaleString('ro-RO', { minimumFractionDigits: 2 });
}
</script>

<template>
    <ClientLayout title="Comenzile mele">
        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-slate-900">Comenzile mele din magazin</h1>

            <div class="mt-6 overflow-hidden rounded-xl bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Comanda</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Data</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-slate-500">Total</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="order in orders.data" :key="order.id">
                            <td class="px-4 py-3 font-medium text-slate-800">{{ order.order_number }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ new Date(order.created_at).toLocaleDateString('ro-RO') }}</td>
                            <td class="px-4 py-3"><span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800">{{ statusLabels[order.status] }}</span></td>
                            <td class="px-4 py-3 text-right">{{ money(order.total) }} lei</td>
                            <td class="px-4 py-3 text-right">
                                <Link :href="route('client.shop-orders.show', order.id)" class="text-blue-600 hover:text-blue-700">Detalii</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p v-if="!orders.data.length" class="p-6 text-slate-500">Nu ai plasat inca nicio comanda in magazin.</p>
            </div>
        </div>
    </ClientLayout>
</template>
