<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';

const props = defineProps({
    installation: Object,
});

const pendingChecklistIndexes = reactive(new Set());

const statusOptions = [
    { value: 'scheduled', label: 'Programata' },
    { value: 'in_progress', label: 'In desfasurare' },
    { value: 'completed', label: 'Finalizata' },
    { value: 'cancelled', label: 'Anulata' },
];

const statusClasses = {
    scheduled: 'bg-blue-100 text-blue-700',
    in_progress: 'bg-amber-100 text-amber-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-700',
};

const mapQuery = computed(() => {
    if (props.installation.latitude && props.installation.longitude) {
        return `${props.installation.latitude},${props.installation.longitude}`;
    }
    if (props.installation.address) {
        return props.installation.address;
    }
    return null;
});

const mapSrc = computed(() => mapQuery.value
    ? `https://maps.google.com/maps?q=${encodeURIComponent(mapQuery.value)}&z=15&output=embed`
    : null);

const mapLink = computed(() => mapQuery.value
    ? `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(mapQuery.value)}`
    : null);

function setStatus(status) {
    router.patch(route('technical.installations.status', props.installation.id), { status }, { preserveScroll: true });
}

function toggleItem(index) {
    const done = !props.installation.checklist[index].done;
    pendingChecklistIndexes.add(index);
    router.patch(route('technical.installations.checklist', props.installation.id), { index, done }, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => pendingChecklistIndexes.delete(index),
    });
}

const checklistProgress = computed(() => {
    if (!props.installation.checklist?.length) return 0;
    const done = props.installation.checklist.filter((i) => i.done).length;
    return Math.round((done / props.installation.checklist.length) * 100);
});
</script>

<template>
    <Head :title="`${installation.type === 'interventie' ? 'Interventie' : 'Instalare'} #${installation.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ installation.type === 'interventie' ? 'Interventie' : 'Instalare' }} #{{ installation.id }} - {{ installation.client.name }}
                </h2>
                <div class="flex gap-2">
                    <a :href="route('technical.installations.pdf', installation.id)" target="_blank" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Raport PDF
                    </a>
                    <Link :href="route('technical.installations.edit', installation.id)" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Editeaza
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto grid max-w-6xl grid-cols-1 gap-6 sm:px-6 lg:grid-cols-3 lg:px-8">
                <div class="space-y-6 lg:col-span-1">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <h3 class="text-sm font-semibold text-slate-500">Detalii</h3>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div>
                                <dt class="text-slate-400">Client</dt>
                                <dd>
                                    <Link :href="route('sales.clients.show', installation.client.id)" class="text-blue-600 hover:text-blue-500">
                                        {{ installation.client.name }}
                                    </Link>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-slate-400">Adresa</dt>
                                <dd class="text-slate-900">{{ installation.address ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400">Tehnician</dt>
                                <dd class="text-slate-900">{{ installation.technician?.name ?? 'Neasignat' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400">Data programata</dt>
                                <dd class="text-slate-900">{{ installation.scheduled_at ? new Date(installation.scheduled_at).toLocaleString('ro-RO') : '-' }}</dd>
                            </div>
                            <div v-if="installation.notes">
                                <dt class="text-slate-400">Note</dt>
                                <dd class="whitespace-pre-line text-slate-900">{{ installation.notes }}</dd>
                            </div>
                        </dl>

                        <h3 class="mt-6 text-sm font-semibold text-slate-500">Status</h3>
                        <span class="mt-2 inline-block rounded-full px-2 py-1 text-xs font-medium" :class="statusClasses[installation.status]">
                            {{ statusOptions.find((s) => s.value === installation.status)?.label }}
                        </span>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button
                                v-for="option in statusOptions"
                                :key="option.value"
                                :disabled="installation.status === option.value"
                                class="rounded-md border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
                                @click="setStatus(option.value)"
                            >
                                {{ option.label }}
                            </button>
                        </div>
                    </div>

                    <div v-if="mapSrc" class="overflow-hidden rounded-lg bg-white shadow-sm">
                        <iframe :src="mapSrc" class="h-56 w-full border-0" loading="lazy"></iframe>
                        <a :href="mapLink" target="_blank" class="block p-3 text-center text-sm text-blue-600 hover:text-blue-500">
                            Deschide in Google Maps
                        </a>
                    </div>
                </div>

                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-500">Checklist instalare</h3>
                            <span class="text-xs text-slate-400">{{ checklistProgress }}% complet</span>
                        </div>
                        <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full bg-green-500 transition-all" :style="{ width: checklistProgress + '%' }"></div>
                        </div>
                        <ul class="mt-4 space-y-2">
                            <li v-for="(item, index) in installation.checklist" :key="index">
                                <label class="flex items-center gap-3 text-sm">
                                    <input
                                        type="checkbox"
                                        :checked="item.done"
                                        :disabled="pendingChecklistIndexes.has(index)"
                                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 disabled:opacity-50"
                                        @change="toggleItem(index)"
                                    />
                                    <span :class="item.done ? 'text-slate-400 line-through' : 'text-slate-700'">{{ item.label }}</span>
                                </label>
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-500">Tichete asociate ({{ installation.tickets.length }})</h3>
                            <Link :href="route('technical.tickets.create', { client_id: installation.client.id })" class="text-sm text-blue-600 hover:text-blue-500">
                                Deschide tichet nou
                            </Link>
                        </div>
                        <div v-if="installation.tickets.length" class="mt-4 divide-y divide-slate-100">
                            <Link
                                v-for="ticket in installation.tickets"
                                :key="ticket.id"
                                :href="route('technical.tickets.show', ticket.id)"
                                class="block py-3 hover:bg-slate-50"
                            >
                                <div class="font-medium text-slate-900">{{ ticket.subject }}</div>
                                <div class="text-xs text-slate-400">{{ ticket.status }}</div>
                            </Link>
                        </div>
                        <p v-else class="mt-4 text-sm text-slate-400">Niciun tichet deschis pentru aceasta interventie.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
