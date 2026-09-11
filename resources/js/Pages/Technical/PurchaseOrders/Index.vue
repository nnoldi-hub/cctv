<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
const props = defineProps({ orders: Object });
function receive(order) { if (confirm(`Receptionezi comanda ${order.order_number}?`)) router.patch(route('technical.purchase-orders.receive', order.id)); }
</script>
<template>
    <Head title="Comenzi furnizori" />
    <AuthenticatedLayout>
        <template #header><div class="flex items-center justify-between"><h2 class="text-xl font-semibold text-gray-800">Comenzi furnizori</h2><Link :href="route('technical.purchase-orders.create')" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white">Comanda noua</Link></div></template>
        <div class="py-8"><div class="mx-auto max-w-6xl sm:px-6 lg:px-8"><div class="overflow-x-auto rounded-lg bg-white shadow-sm"><table class="min-w-full divide-y divide-slate-200"><thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Comanda</th><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Furnizor</th><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Status</th><th class="px-4 py-3 text-right text-xs uppercase text-slate-500">Total</th><th></th></tr></thead><tbody class="divide-y divide-slate-100"><tr v-for="order in props.orders.data" :key="order.id"><td class="px-4 py-3 font-medium">{{ order.order_number }}<div class="text-xs text-slate-400">{{ order.items_count }} materiale</div></td><td class="px-4 py-3">{{ order.supplier.name }}</td><td class="px-4 py-3"><span class="rounded-full px-2 py-1 text-xs" :class="order.status === 'received' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'">{{ order.status === 'received' ? 'Receptionata' : 'Comandata' }}</span></td><td class="px-4 py-3 text-right">{{ Number(order.total_amount).toLocaleString('ro-RO', { minimumFractionDigits: 2 }) }} lei</td><td class="px-4 py-3 text-right"><button v-if="order.status !== 'received'" class="text-emerald-600 hover:text-emerald-800" @click="receive(order)">Receptioneaza</button></td></tr><tr v-if="!props.orders.data.length"><td colspan="5" class="px-4 py-8 text-center text-sm text-slate-400">Nu exista comenzi.</td></tr></tbody></table></div></div></div>
    </AuthenticatedLayout>
</template>
