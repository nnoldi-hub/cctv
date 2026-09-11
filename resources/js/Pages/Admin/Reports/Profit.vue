<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    filters: Object,
    summary: Object,
    byClient: Array,
    byTechnician: Array,
    installations: Array,
});

const from = ref(props.filters.from);
const to = ref(props.filters.to);

function money(value) {
    return Number(value).toLocaleString('ro-RO', { minimumFractionDigits: 2 });
}

function applyFilters() {
    router.get(route('admin.reports.profit'), { from: from.value, to: to.value }, { preserveState: true });
}

function marginPercent(revenue, profit) {
    if (!revenue) {
        return 0;
    }

    return Math.round((profit / revenue) * 100);
}
</script>

<template>
    <Head title="Raport profit" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Raport profit</h2>
                <Link :href="route('admin.reports')" class="text-sm font-medium text-blue-600 hover:text-blue-500">Inapoi la rapoarte</Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <section class="rounded-lg bg-white p-6 shadow-sm">
                    <form class="flex flex-wrap items-end gap-4" @submit.prevent="applyFilters">
                        <div>
                            <label class="block text-xs font-medium text-slate-500">De la</label>
                            <input v-model="from" type="date" class="mt-1 rounded-md border-slate-300 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500">Pana la</label>
                            <input v-model="to" type="date" class="mt-1 rounded-md border-slate-300 text-sm" />
                        </div>
                        <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                            Aplica
                        </button>
                    </form>
                </section>

                <section class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div class="rounded-lg bg-white p-4 shadow-sm">
                        <div class="text-xs text-slate-500">Instalari finalizate</div>
                        <div class="mt-1 text-xl font-semibold text-slate-900">{{ summary.installations }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow-sm">
                        <div class="text-xs text-slate-500">Venit (valoare oferte)</div>
                        <div class="mt-1 text-xl font-semibold text-slate-900">{{ money(summary.revenue) }} lei</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow-sm">
                        <div class="text-xs text-slate-500">Costuri totale</div>
                        <div class="mt-1 text-xl font-semibold text-amber-600">{{ money(summary.cost) }} lei</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow-sm">
                        <div class="text-xs text-slate-500">Profit net</div>
                        <div class="mt-1 text-xl font-semibold" :class="summary.profit >= 0 ? 'text-green-600' : 'text-red-600'">
                            {{ money(summary.profit) }} lei
                        </div>
                    </div>
                </section>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <section class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-500">Profit pe client</h3>
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead>
                                    <tr class="text-left text-xs uppercase text-slate-400">
                                        <th class="py-2">Client</th>
                                        <th class="py-2 text-right">Instalari</th>
                                        <th class="py-2 text-right">Venit</th>
                                        <th class="py-2 text-right">Cost</th>
                                        <th class="py-2 text-right">Profit</th>
                                        <th class="py-2 text-right">Marja</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="row in byClient" :key="row.client">
                                        <td class="py-2 font-medium text-slate-900">{{ row.client }}</td>
                                        <td class="py-2 text-right text-slate-600">{{ row.installations }}</td>
                                        <td class="py-2 text-right text-slate-900">{{ money(row.revenue) }} lei</td>
                                        <td class="py-2 text-right text-amber-600">{{ money(row.cost) }} lei</td>
                                        <td class="py-2 text-right font-semibold" :class="row.profit >= 0 ? 'text-green-600' : 'text-red-600'">
                                            {{ money(row.profit) }} lei
                                        </td>
                                        <td class="py-2 text-right text-slate-500">{{ marginPercent(row.revenue, row.profit) }}%</td>
                                    </tr>
                                    <tr v-if="!byClient.length">
                                        <td colspan="6" class="py-4 text-center text-slate-400">Nicio instalare finalizata in perioada selectata.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-500">Profit pe tehnician</h3>
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead>
                                    <tr class="text-left text-xs uppercase text-slate-400">
                                        <th class="py-2">Tehnician</th>
                                        <th class="py-2 text-right">Instalari</th>
                                        <th class="py-2 text-right">Venit</th>
                                        <th class="py-2 text-right">Cost</th>
                                        <th class="py-2 text-right">Profit</th>
                                        <th class="py-2 text-right">Marja</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="row in byTechnician" :key="row.technician">
                                        <td class="py-2 font-medium text-slate-900">{{ row.technician }}</td>
                                        <td class="py-2 text-right text-slate-600">{{ row.installations }}</td>
                                        <td class="py-2 text-right text-slate-900">{{ money(row.revenue) }} lei</td>
                                        <td class="py-2 text-right text-amber-600">{{ money(row.cost) }} lei</td>
                                        <td class="py-2 text-right font-semibold" :class="row.profit >= 0 ? 'text-green-600' : 'text-red-600'">
                                            {{ money(row.profit) }} lei
                                        </td>
                                        <td class="py-2 text-right text-slate-500">{{ marginPercent(row.revenue, row.profit) }}%</td>
                                    </tr>
                                    <tr v-if="!byTechnician.length">
                                        <td colspan="6" class="py-4 text-center text-slate-400">Nicio instalare finalizata in perioada selectata.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                <section class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-slate-500">Detaliu instalari finalizate ({{ installations.length }})</h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase text-slate-400">
                                    <th class="py-2">Data</th>
                                    <th class="py-2">Client</th>
                                    <th class="py-2">Tehnician</th>
                                    <th class="py-2">Tip</th>
                                    <th class="py-2 text-right">Valoare oferta</th>
                                    <th class="py-2 text-right">Cost materiale</th>
                                    <th class="py-2 text-right">Cost manopera</th>
                                    <th class="py-2 text-right">Cheltuieli reale</th>
                                    <th class="py-2 text-right">Profit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="row in installations" :key="row.id">
                                    <td class="py-2 text-slate-600">{{ row.completed_at }}</td>
                                    <td class="py-2 font-medium text-slate-900">{{ row.client_name }}</td>
                                    <td class="py-2 text-slate-600">{{ row.technician_name }}</td>
                                    <td class="py-2 capitalize text-slate-600">{{ row.type }}</td>
                                    <td class="py-2 text-right text-slate-900">{{ money(row.offer_value) }} lei</td>
                                    <td class="py-2 text-right text-slate-600">{{ money(row.material_cost) }} lei</td>
                                    <td class="py-2 text-right text-slate-600">{{ money(row.labor_cost) }} lei</td>
                                    <td class="py-2 text-right text-slate-600">{{ money(row.actual_expenses) }} lei</td>
                                    <td class="py-2 text-right font-semibold" :class="row.profit >= 0 ? 'text-green-600' : 'text-red-600'">
                                        {{ money(row.profit) }} lei
                                    </td>
                                </tr>
                                <tr v-if="!installations.length">
                                    <td colspan="9" class="py-4 text-center text-slate-400">Nicio instalare finalizata in perioada selectata.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
