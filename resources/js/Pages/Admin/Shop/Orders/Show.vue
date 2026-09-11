<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({ order: Object });

const statusLabels = {
    new: 'Noua',
    confirmed: 'Confirmata',
    shipped: 'Expediata',
    completed: 'Finalizata',
    cancelled: 'Anulata',
};

const discountForm = useForm({ manual_discount: Number(props.order.manual_discount ?? 0) });

function money(value) {
    return Number(value).toLocaleString('ro-RO', { minimumFractionDigits: 2 });
}

function updateStatus(status) {
    router.patch(route('admin.shop-orders.status', props.order.id), { status }, { preserveScroll: true });
}

function applyDiscount() {
    discountForm.patch(route('admin.shop-orders.discount', props.order.id), { preserveScroll: true });
}

function generateInvoice() {
    router.post(route('admin.shop-orders.invoice', props.order.id), {}, { preserveScroll: true });
}
</script>

<template>
    <Head :title="`Comanda ${order.order_number}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Comanda {{ order.order_number }}</h2>
                <Link :href="route('admin.shop-orders.index')" class="text-sm text-blue-600">&larr; Toate comenzile</Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto grid max-w-5xl gap-6 sm:px-6 lg:px-8">
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="font-semibold text-slate-900">Date client</h3>
                        <dl class="mt-3 space-y-1 text-sm">
                            <div class="flex justify-between"><dt class="text-slate-500">Nume</dt><dd>{{ order.name }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Telefon</dt><dd>{{ order.phone }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Email</dt><dd>{{ order.email || '-' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Adresa</dt><dd class="text-right">{{ order.shipping_address }}, {{ order.shipping_city }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Plata</dt><dd>{{ order.payment_method === 'cod' ? 'Ramburs' : 'Transfer bancar' }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Montaj dorit</dt><dd>{{ order.wants_installation ? 'Da' : 'Nu' }}</dd></div>
                            <div v-if="order.client" class="flex justify-between"><dt class="text-slate-500">Client CRM</dt><dd><Link :href="route('sales.clients.show', order.client.id)" class="text-blue-600">{{ order.client.name }}</Link></dd></div>
                        </dl>
                        <p v-if="order.notes" class="mt-3 rounded-md bg-slate-50 p-3 text-sm text-slate-600">{{ order.notes }}</p>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="font-semibold text-slate-900">Status comanda</h3>
                        <p class="mt-2"><span class="rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">{{ statusLabels[order.status] }}</span></p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <button v-for="(label, value) in statusLabels" :key="value" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50" :disabled="order.status === value" @click="updateStatus(value)">
                                {{ label }}
                            </button>
                        </div>

                        <div class="mt-6 border-t pt-4">
                            <h4 class="text-sm font-semibold text-slate-900">Factura</h4>
                            <p v-if="order.invoice" class="mt-2 text-sm text-slate-600">
                                Factura: <Link :href="route('admin.invoices.show', order.invoice.id)" class="text-blue-600">{{ order.invoice.invoice_number }}</Link>
                            </p>
                            <button v-else class="mt-2 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-500" @click="generateInvoice">
                                Genereaza factura
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="font-semibold text-slate-900">Produse comandate</h3>
                    <table class="mt-3 min-w-full divide-y">
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
                        <div class="flex justify-between"><span class="text-slate-500">Reduceri automate</span><span>-{{ money(order.discount_total) }} lei</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Reducere manuala</span><span>-{{ money(order.manual_discount) }} lei</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Transport</span><span>{{ Number(order.shipping_cost) === 0 ? 'Gratuit' : money(order.shipping_cost) + ' lei' }}</span></div>
                        <div class="flex justify-between text-base font-bold text-slate-900"><span>Total</span><span>{{ money(order.total) }} lei</span></div>
                    </div>

                    <div class="mt-6 border-t pt-4">
                        <h4 class="text-sm font-semibold text-slate-900">Reducere manuala (ex: pentru montaj efectuat de noi)</h4>
                        <p class="mt-1 text-xs text-slate-500">Se aplica dupa discutia cu clientul; recalculeaza automat totalul comenzii.</p>
                        <form class="mt-2 flex items-center gap-2" @submit.prevent="applyDiscount">
                            <input v-model.number="discountForm.manual_discount" type="number" min="0" step="0.01" class="w-32 rounded-md border-slate-300 text-sm" />
                            <button type="submit" class="rounded-md bg-slate-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-700" :disabled="discountForm.processing">Aplica</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
