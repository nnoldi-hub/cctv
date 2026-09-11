<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({ order: Object });

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
    <ClientLayout :title="`Comanda ${order.order_number}`">
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-slate-900">Comanda {{ order.order_number }}</h1>
                <Link :href="route('client.shop-orders.index')" class="text-sm text-blue-600">&larr; Toate comenzile</Link>
            </div>

            <div class="mt-6 rounded-xl bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">{{ statusLabels[order.status] }}</span>
                    <Link v-if="order.invoice" :href="route('client.invoices.pdf', order.invoice.id)" class="text-sm text-blue-600">Descarca factura</Link>
                </div>

                <table class="mt-4 min-w-full divide-y">
                    <thead>
                        <tr class="text-left text-xs uppercase text-slate-500">
                            <th class="py-2">Produs</th>
                            <th class="py-2 text-right">Pret</th>
                            <th class="py-2 text-center">Cantitate</th>
                            <th class="py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr v-for="item in order.items" :key="item.id">
                            <td class="py-2">{{ item.name }}</td>
                            <td class="py-2 text-right">{{ money(item.unit_price) }} lei</td>
                            <td class="py-2 text-center">{{ item.quantity }}</td>
                            <td class="py-2 text-right">{{ money(item.line_total) }} lei</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4 ml-auto max-w-xs space-y-1 text-sm">
                    <div class="flex justify-between"><span class="text-slate-500">Subtotal</span><span>{{ money(order.subtotal) }} lei</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Reduceri</span><span>-{{ money(Number(order.discount_total) + Number(order.manual_discount)) }} lei</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Transport</span><span>{{ Number(order.shipping_cost) === 0 ? 'Gratuit' : money(order.shipping_cost) + ' lei' }}</span></div>
                    <div class="flex justify-between text-base font-bold text-slate-900"><span>Total</span><span>{{ money(order.total) }} lei</span></div>
                </div>

                <dl class="mt-6 grid grid-cols-2 gap-2 border-t pt-4 text-sm">
                    <div><dt class="inline font-medium text-slate-500">Livrare la: </dt><dd class="inline">{{ order.shipping_address }}, {{ order.shipping_city }}</dd></div>
                    <div><dt class="inline font-medium text-slate-500">Plata: </dt><dd class="inline">{{ order.payment_method === 'cod' ? 'Ramburs' : 'Transfer bancar' }}</dd></div>
                    <div><dt class="inline font-medium text-slate-500">Montaj dorit: </dt><dd class="inline">{{ order.wants_installation ? 'Da' : 'Nu' }}</dd></div>
                </dl>
            </div>
        </div>
    </ClientLayout>
</template>
