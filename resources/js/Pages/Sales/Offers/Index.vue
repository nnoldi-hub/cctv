<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';

const props = defineProps({
    offers: Object,
    filters: Object,
    pipeline: Object,
});

const form = reactive({
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
});

let debounceTimer = null;
watch(form, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get(route('sales.offers.index'), form, { preserveState: true, replace: true });
    }, 300);
});

const pipelineStages = [
    { key: 'draft', label: 'Draft', color: 'bg-slate-100 text-slate-600' },
    { key: 'sent', label: 'Trimise', color: 'bg-blue-100 text-blue-700' },
    { key: 'accepted', label: 'Acceptate', color: 'bg-green-100 text-green-800' },
    { key: 'rejected', label: 'Respinse', color: 'bg-red-100 text-red-700' },
    { key: 'expired', label: 'Expirate', color: 'bg-slate-100 text-slate-500' },
];

function money(value) {
    return Number(value ?? 0).toLocaleString('ro-RO', { maximumFractionDigits: 0 });
}

function exportUrl() {
    const params = new URLSearchParams(form).toString();
    return route('sales.offers.export') + (params ? `?${params}` : '');
}
</script>

<template>
    <Head title="Oferte" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Oferte</h2>
                <div class="flex gap-2">
                    <a :href="exportUrl()" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Export Excel
                    </a>
                    <Link :href="route('sales.offers.create')" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                        Oferta noua
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-5">
                    <button
                        v-for="stage in pipelineStages"
                        :key="stage.key"
                        class="rounded-lg bg-white p-4 text-left shadow-sm ring-1 ring-transparent hover:ring-blue-300"
                        :class="{ 'ring-2 ring-blue-500': form.status === stage.key }"
                        @click="form.status = form.status === stage.key ? '' : stage.key"
                    >
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="stage.color">{{ stage.label }}</span>
                        <div class="mt-2 text-2xl font-bold text-slate-900">{{ pipeline[stage.key]?.total ?? 0 }}</div>
                        <div class="text-xs text-slate-400">{{ money(pipeline[stage.key]?.amount) }} lei</div>
                    </button>
                </div>

                <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <input
                        v-model="form.search"
                        type="text"
                        placeholder="Cauta dupa numele clientului..."
                        class="rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    />
                </div>

                <div class="rounded-lg bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Client</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Titlu</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-slate-500">Valoare</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Creata la</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="offer in offers.data" :key="offer.id" class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <Link :href="route('sales.clients.show', offer.client.id)" class="text-blue-600 hover:text-blue-500">
                                        {{ offer.client.name }}
                                    </Link>
                                </td>
                                <td class="px-4 py-3">
                                    <Link :href="route('sales.offers.show', offer.id)" class="font-medium text-slate-900 hover:text-blue-600">
                                        {{ offer.title }}
                                    </Link>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="rounded-full px-2 py-1 text-xs font-medium"
                                        :class="pipelineStages.find((s) => s.key === offer.status)?.color"
                                    >
                                        {{ offer.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-slate-900">{{ money(offer.total_amount) }} lei</td>
                                <td class="px-4 py-3 text-sm text-slate-500">{{ new Date(offer.created_at).toLocaleDateString('ro-RO') }}</td>
                            </tr>
                            <tr v-if="!offers.data.length">
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-400">Nicio oferta gasita.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>

                <div v-if="offers.links.length > 3" class="mt-4 flex flex-wrap gap-2">
                    <Link
                        v-for="link in offers.links"
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
