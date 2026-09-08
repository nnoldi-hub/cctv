<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';

const props = defineProps({
    logs: Object,
    filters: Object,
    actions: Array,
    users: Array,
});

const form = reactive({
    action: props.filters.action ?? '',
    user_id: props.filters.user_id ?? '',
});

function applyFilters() {
    router.get(route('admin.audit-logs'), form, { preserveState: true, replace: true });
}

function formatDate(value) {
    return value ? new Date(value).toLocaleString('ro-RO') : '-';
}
</script>

<template>
    <Head title="Jurnal audit" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Jurnal de audit</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div class="rounded-lg bg-white p-4 shadow-sm">
                    <form class="flex flex-col gap-3 sm:flex-row" @submit.prevent="applyFilters">
                        <select v-model="form.action" class="rounded-md border-slate-300 text-sm">
                            <option value="">Toate actiunile</option>
                            <option v-for="action in actions" :key="action" :value="action">{{ action }}</option>
                        </select>
                        <select v-model="form.user_id" class="rounded-md border-slate-300 text-sm">
                            <option value="">Toti utilizatorii</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                            Filtreaza
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Data</th>
                                <th class="px-4 py-3">Utilizator</th>
                                <th class="px-4 py-3">Actiune</th>
                                <th class="px-4 py-3">Descriere</th>
                                <th class="px-4 py-3">IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="log in logs.data" :key="log.id">
                                <td class="whitespace-nowrap px-4 py-3 text-slate-500">{{ formatDate(log.created_at) }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ log.user?.name ?? 'Sistem / anonim' }}</td>
                                <td class="px-4 py-3"><span class="rounded bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">{{ log.action }}</span></td>
                                <td class="px-4 py-3 text-slate-700">{{ log.description }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ log.ip_address ?? '-' }}</td>
                            </tr>
                            <tr v-if="!logs.data.length">
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400">Nu exista evenimente inregistrate.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="logs.links?.length" class="flex flex-wrap gap-2">
                    <Link v-for="link in logs.links" :key="link.label" :href="link.url ?? '#'" v-html="link.label" class="rounded border px-3 py-1 text-sm" :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-slate-600'" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
