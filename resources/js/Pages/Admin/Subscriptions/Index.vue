<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';

const props = defineProps({
    subscriptions: Object,
    filters: Object,
    monthlyRecurringRevenue: Number,
});

const form = reactive({ status: props.filters.status ?? '' });
watch(form, () => {
    router.get(route('admin.subscriptions.index'), form, { preserveState: true, replace: true });
});

const statusClasses = {
    active: 'bg-green-100 text-green-800',
    paused: 'bg-amber-100 text-amber-800',
    cancelled: 'bg-slate-100 text-slate-500',
};

function money(value) {
    return Number(value).toLocaleString('ro-RO', { minimumFractionDigits: 2 });
}

function exportUrl() {
    const params = new URLSearchParams(form).toString();
    return route('admin.subscriptions.export') + (params ? `?${params}` : '');
}

function destroy(subscription) {
    if (confirm(`Stergi abonamentul "${subscription.plan}"?`)) {
        router.delete(route('admin.subscriptions.destroy', subscription.id));
    }
}
</script>

<template>
    <Head title="Abonamente mentenanta" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Abonamente mentenanta</h2>
                <div class="flex gap-2">
                    <a :href="exportUrl()" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Export Excel
                    </a>
                    <Link :href="route('admin.subscriptions.create')" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                        Abonament nou
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 rounded-lg bg-white p-6 shadow-sm">
                    <div class="text-sm text-slate-500">Venit lunar recurent (MRR) estimat</div>
                    <div class="text-2xl font-semibold text-slate-900">{{ money(monthlyRecurringRevenue) }} lei / luna</div>
                </div>

                <div class="mb-4">
                    <select v-model="form.status" class="rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Toate statusurile</option>
                        <option value="active">Activ</option>
                        <option value="paused">Suspendat</option>
                        <option value="cancelled">Anulat</option>
                    </select>
                </div>

                <div class="rounded-lg bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Client</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Plan</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-slate-500">Pret</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Urmatoarea facturare</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Status</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="sub in subscriptions.data" :key="sub.id" class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <Link :href="route('sales.clients.show', sub.client.id)" class="text-blue-600 hover:text-blue-500">{{ sub.client.name }}</Link>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-900">{{ sub.plan }}</td>
                                <td class="px-4 py-3 text-right text-sm text-slate-900">{{ money(sub.price) }} lei / {{ sub.billing_cycle === 'yearly' ? 'an' : 'luna' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ sub.next_billing_at ? new Date(sub.next_billing_at).toLocaleDateString('ro-RO') : '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium" :class="statusClasses[sub.status]">{{ sub.status }}</span>
                                </td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <Link :href="route('admin.subscriptions.edit', sub.id)" class="text-slate-500 hover:text-slate-700">Editeaza</Link>
                                    <button class="ml-3 text-red-500 hover:text-red-700" @click="destroy(sub)">Sterge</button>
                                </td>
                            </tr>
                            <tr v-if="!subscriptions.data.length">
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400">Niciun abonament gasit.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>

                <div v-if="subscriptions.links.length > 3" class="mt-4 flex flex-wrap gap-2">
                    <Link
                        v-for="link in subscriptions.links"
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
