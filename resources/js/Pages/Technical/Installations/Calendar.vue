<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';

const props = defineProps({
    date: String,
    technicianId: [Number, String, null],
    technicians: Array,
    installations: Array,
});

const filters = reactive({ date: props.date, technician_id: props.technicianId ?? '' });
watch(filters, () => router.get(route('technical.installations.calendar'), filters, { preserveState: true, replace: true }));

const statusLabels = { scheduled: 'Programata', in_progress: 'In desfasurare', completed: 'Finalizata' };
const statusClasses = { scheduled: 'bg-blue-100 text-blue-700', in_progress: 'bg-amber-100 text-amber-800', completed: 'bg-green-100 text-green-800' };
function time(value) { return value ? new Date(value).toLocaleTimeString('ro-RO', { hour: '2-digit', minute: '2-digit' }) : '-'; }
</script>

<template>
    <Head title="Calendar programari" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Calendar programari</h2>
                <Link :href="route('technical.installations.create')" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white">Programare noua</Link>
            </div>
        </template>
        <div class="py-8">
            <div class="mx-auto max-w-5xl space-y-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-3 rounded-lg bg-white p-4 shadow-sm sm:flex-row">
                    <input v-model="filters.date" type="date" class="rounded-md border-slate-300 text-sm" />
                    <select v-model="filters.technician_id" class="rounded-md border-slate-300 text-sm">
                        <option value="">Toti tehnicienii</option>
                        <option v-for="tech in technicians" :key="tech.id" :value="tech.id">{{ tech.name }}</option>
                    </select>
                    <Link :href="route('technical.installations.index')" class="rounded-md border border-slate-300 px-3 py-2 text-center text-sm text-slate-600">Lista programari</Link>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">{{ new Date(`${date}T12:00:00`).toLocaleDateString('ro-RO', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}</h3>
                    <div v-if="installations.length" class="mt-5 divide-y divide-slate-100">
                        <Link v-for="installation in installations" :key="installation.id" :href="route('technical.installations.show', installation.id)" class="flex gap-4 py-4 hover:bg-slate-50">
                            <div class="w-16 flex-shrink-0 text-lg font-semibold text-blue-600">{{ time(installation.scheduled_at) }}</div>
                            <div class="min-w-0 flex-1">
                                <div class="font-medium text-slate-900">{{ installation.client.name }}</div>
                                <div class="mt-1 text-sm text-slate-500">{{ installation.type }} · {{ installation.technician?.name ?? 'Neasignat' }}</div>
                                <div v-if="installation.address" class="mt-1 text-xs text-slate-400">{{ installation.address }}</div>
                            </div>
                            <span class="h-fit rounded-full px-2 py-1 text-xs font-medium" :class="statusClasses[installation.status]">{{ statusLabels[installation.status] }}</span>
                        </Link>
                    </div>
                    <p v-else class="mt-6 text-sm text-slate-400">Nu exista programari pentru criteriile selectate.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
