<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    stats: Object,
    upcomingInstallations: Array,
    openTicketsList: Array,
});

const page = usePage();
const roles = computed(() => page.props.auth.roles ?? []);
const canManageOperations = computed(() => roles.value.includes('admin') || roles.value.includes('tehnic'));
</script>

<template>
    <Head title="Dashboard Tehnic" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Modul Tehnic</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="canManageOperations" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">Echipamente</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ stats.equipment }}</div>
                    </div>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">Stoc scazut</div>
                        <div class="text-3xl font-semibold" :class="stats.lowStock > 0 ? 'text-red-600' : 'text-gray-900'">{{ stats.lowStock }}</div>
                    </div>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">Instalari programate</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ stats.scheduled }}</div>
                    </div>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">In desfasurare</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ stats.inProgress }}</div>
                    </div>
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">Tichete deschise</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ stats.openTickets }}</div>
                    </div>
                </div>
                <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm text-gray-500">Tichete deschise</div>
                        <div class="text-3xl font-semibold text-gray-900">{{ stats.openTickets }}</div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-6" :class="canManageOperations ? 'lg:grid-cols-2' : ''">
                    <div v-if="canManageOperations" class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-500">Programari apropiate</h3>
                            <Link :href="route('technical.installations.index')" class="text-sm text-blue-600 hover:text-blue-500">Vezi toate</Link>
                        </div>
                        <div v-if="upcomingInstallations.length" class="mt-4 divide-y divide-slate-100">
                            <Link
                                v-for="item in upcomingInstallations"
                                :key="item.id"
                                :href="route('technical.installations.show', item.id)"
                                class="flex items-center justify-between py-3 hover:bg-slate-50"
                            >
                                <div>
                                    <div class="font-medium text-slate-900">{{ item.client.name }}</div>
                                    <div class="text-xs text-slate-400 capitalize">{{ item.type }}</div>
                                </div>
                                <div class="text-xs text-slate-400">
                                    {{ item.scheduled_at ? new Date(item.scheduled_at).toLocaleString('ro-RO') : 'Neprogramata' }}
                                </div>
                            </Link>
                        </div>
                        <p v-else class="mt-4 text-sm text-slate-400">Nicio programare apropiata.</p>
                    </div>

                    <div class="rounded-lg bg-white p-6 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-slate-500">Tichete deschise</h3>
                            <Link :href="route('technical.tickets.index')" class="text-sm text-blue-600 hover:text-blue-500">Vezi toate</Link>
                        </div>
                        <div v-if="openTicketsList.length" class="mt-4 divide-y divide-slate-100">
                            <Link
                                v-for="ticket in openTicketsList"
                                :key="ticket.id"
                                :href="route('technical.tickets.show', ticket.id)"
                                class="flex items-center justify-between py-3 hover:bg-slate-50"
                            >
                                <div>
                                    <div class="font-medium text-slate-900">{{ ticket.subject }}</div>
                                    <div class="text-xs text-slate-400">{{ ticket.client.name }}</div>
                                </div>
                                <div class="text-xs capitalize text-slate-400">{{ ticket.priority }}</div>
                            </Link>
                        </div>
                        <p v-else class="mt-4 text-sm text-slate-400">Niciun tichet deschis.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
