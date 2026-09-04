<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';

const props = defineProps({
    tickets: Object,
    filters: Object,
});

const form = reactive({
    status: props.filters.status ?? '',
    priority: props.filters.priority ?? '',
});

watch(form, () => {
    router.get(route('technical.tickets.index'), form, { preserveState: true, replace: true });
});

const statusClasses = {
    open: 'bg-blue-100 text-blue-700',
    in_progress: 'bg-amber-100 text-amber-800',
    resolved: 'bg-green-100 text-green-800',
    closed: 'bg-slate-100 text-slate-500',
};

const priorityClasses = {
    low: 'bg-slate-100 text-slate-600',
    medium: 'bg-amber-100 text-amber-800',
    high: 'bg-red-100 text-red-700',
};
</script>

<template>
    <Head title="Tichete suport" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Tichete suport</h2>
                <Link :href="route('technical.tickets.create')" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-500">
                    Tichet nou
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <select v-model="form.status" class="rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Toate statusurile</option>
                        <option value="open">Deschis</option>
                        <option value="in_progress">In lucru</option>
                        <option value="resolved">Rezolvat</option>
                        <option value="closed">Inchis</option>
                    </select>
                    <select v-model="form.priority" class="rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Toate prioritatile</option>
                        <option value="low">Scazuta</option>
                        <option value="medium">Medie</option>
                        <option value="high">Ridicata</option>
                    </select>
                </div>

                <div class="rounded-lg bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Subiect</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Client</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Prioritate</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-slate-500">Asignat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="ticket in tickets.data" :key="ticket.id" class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <Link :href="route('technical.tickets.show', ticket.id)" class="font-medium text-blue-600 hover:text-blue-500">
                                        {{ ticket.subject }}
                                    </Link>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ ticket.client.name }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium" :class="priorityClasses[ticket.priority]">{{ ticket.priority }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium" :class="statusClasses[ticket.status]">{{ ticket.status }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ ticket.assigned_to?.name ?? '-' }}</td>
                            </tr>
                            <tr v-if="!tickets.data.length">
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-400">Niciun tichet gasit.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>

                <div v-if="tickets.links.length > 3" class="mt-4 flex flex-wrap gap-2">
                    <Link
                        v-for="link in tickets.links"
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
