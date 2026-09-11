<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';

const props = defineProps({
    orders: Object,
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
        router.get(route('admin.shop-orders.index'), form, { preserveState: true, replace: true });
    }, 300);
});

const statusLabels = {
    new: 'Noua',
    confirmed: 'Confirmata',
    shipped: 'Expediata',
    completed: 'Finalizata',
    cancelled: 'Anulata',
};
const statusClasses = {
    new: 'bg-amber-100 text-amber-800',
    confirmed: 'bg-blue-100 text-blue-800',
    shipped: 'bg-purple-100 text-purple-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-slate-100 text-slate-500',
};

function money(value) {
    return Number(value).toLocaleString('ro-RO', { minimumFractionDigits: 2 });
}
</script>

<template>
    <Head title="Comenzi magazin" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Comenzi magazin</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="text-sm text-slate-500">Comenzi noi</div>
                        <div class="text-2xl font-semibold text-amber-600">{{ summary.new }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="text-sm text-slate-500">Confirmate</div>
                        <div class="text-2xl font-semibold text-blue-600">{{ summary.confirmed }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="text-sm text-slate-500">Venit total (magazin)</div>
                        <div class="text-2xl font-semibold text-green-600">{{ money(summary.totalRevenue) }} lei</div>
                    </div>
                </div>

                <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <input v-model="form.search" type="text" placeholder="Cauta dupa numar, client, telefon..." class="rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    <select v-model="form.status" class="rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Toate statusurile</option>
                        <option value="new">Noua</option>
                        <option value="confirmed">Confirmata</option>
                        <option value="shipped">Expediata</option>
                        <option value="completed">Finalizata</option>
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
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-slate-500">Total</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Data</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Status</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="order in orders.data" :key="order.id" class="hover:bg-slate-50">
                                    <td class="px-4 py-3">
                                        <Link :href="route('admin.shop-orders.show', order.id)" class="font-medium text-blue-600 hover:text-blue-500">{{ order.order_number }}</Link>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ order.name }}<div class="text-xs text-slate-400">{{ order.phone }}</div></td>
                                    <td class="px-4 py-3 text-right font-medium text-slate-900">{{ money(order.total) }} lei</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ new Date(order.created_at).toLocaleDateString('ro-RO') }}</td>
                                    <td class="px-4 py-3"><span class="rounded-full px-2 py-1 text-xs font-medium" :class="statusClasses[order.status]">{{ statusLabels[order.status] }}</span></td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        <Link :href="route('admin.shop-orders.show', order.id)" class="text-blue-600 hover:text-blue-700">Deschide</Link>
                                    </td>
                                </tr>
                                <tr v-if="!orders.data.length">
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400">Nicio comanda gasita.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div v-if="orders.links.length > 3" class="mt-4 flex flex-wrap gap-2">
                    <Link
                        v-for="link in orders.links"
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
