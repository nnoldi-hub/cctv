<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Icon from '@/Components/Icon.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ stats: { type: Object, default: () => ({}) } });

const page = usePage();
const roles = computed(() => page.props.auth.roles ?? []);
const hasRole = (...names) => names.some((name) => roles.value.includes(name));
const userName = computed(() => page.props.auth.user?.name?.split(' ')[0] ?? '');

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Buna dimineata';
    if (hour < 18) return 'Buna ziua';
    return 'Buna seara';
});

const quickStats = computed(() => {
    const s = props.stats ?? {};
    const items = [];

    if (s.leads !== undefined) {
        items.push({ label: 'Lead-uri noi', value: s.leads, icon: 'user-plus', color: 'text-blue-600 bg-blue-50', href: route('sales.clients.index', { status: 'lead' }) });
    }
    if (s.pipelineValue !== undefined) {
        items.push({ label: 'Pipeline oferte', value: `${Number(s.pipelineValue).toLocaleString('ro-RO')} lei`, icon: 'trending-up', color: 'text-indigo-600 bg-indigo-50', href: route('sales.offers.index') });
    }
    if (s.openTickets !== undefined) {
        items.push({ label: 'Tichete deschise', value: s.openTickets, icon: 'ticket', color: 'text-amber-600 bg-amber-50', href: route('technical.tickets.index') });
    }
    if (s.lowStock !== undefined) {
        items.push({ label: 'Stoc scazut', value: s.lowStock, icon: 'alert-triangle', color: 'text-rose-600 bg-rose-50', href: route('technical.equipment.index', { low_stock: 1 }) });
    }
    if (s.unpaidAmount !== undefined) {
        items.push({ label: 'Facturi neincasate', value: `${Number(s.unpaidAmount).toLocaleString('ro-RO')} lei`, icon: 'file-text', color: 'text-red-600 bg-red-50', href: route('admin.invoices.index', { status: 'unpaid' }) });
    }
    if (s.newShopOrders !== undefined) {
        items.push({ label: 'Comenzi magazin noi', value: s.newShopOrders, icon: 'shopping-cart', color: 'text-emerald-600 bg-emerald-50', href: route('admin.shop-orders.index', { status: 'new' }) });
    }

    return items;
});

const modules = computed(() => [
    {
        name: 'Vanzari',
        description: 'CRM, clienti, lead-uri si oferte',
        href: route('sales.dashboard'),
        icon: 'briefcase',
        color: 'bg-blue-600',
        visible: hasRole('admin', 'vanzari'),
    },
    {
        name: 'Magazin',
        description: 'Comenzi online, reduceri si produse',
        href: route('admin.shop-orders.index'),
        icon: 'shopping-cart',
        color: 'bg-emerald-600',
        visible: hasRole('admin'),
    },
    {
        name: 'Tehnic',
        description: 'Echipamente, instalari si suport',
        href: route('technical.dashboard'),
        icon: 'wrench',
        color: 'bg-orange-500',
        visible: hasRole('admin', 'tehnic', 'suport'),
    },
    {
        name: 'Financiar',
        description: 'Facturi, cheltuieli, abonamente si rapoarte',
        href: route('admin.invoices.index'),
        icon: 'credit-card',
        color: 'bg-purple-600',
        visible: hasRole('admin'),
    },
    {
        name: 'Site & Continut',
        description: 'Pachete, pagini publice si blog',
        href: route('admin.pages.index'),
        icon: 'globe',
        color: 'bg-sky-600',
        visible: hasRole('admin'),
    },
    {
        name: 'Sistem',
        description: 'Utilizatori, jurnal audit si setari',
        href: route('admin.users.index'),
        icon: 'shield-check',
        color: 'bg-slate-700',
        visible: hasRole('admin'),
    },
].filter((m) => m.visible));
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ greeting }}{{ userName ? `, ${userName}` : '' }}
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-if="quickStats.length" class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    <Link
                        v-for="stat in quickStats"
                        :key="stat.label"
                        :href="stat.href"
                        class="rounded-xl bg-white p-4 shadow-sm transition hover:shadow-md"
                    >
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg" :class="stat.color">
                                <Icon :name="stat.icon" class="h-5 w-5" />
                            </span>
                            <div class="min-w-0">
                                <div class="truncate text-lg font-bold text-slate-900">{{ stat.value }}</div>
                                <div class="truncate text-xs text-slate-500">{{ stat.label }}</div>
                            </div>
                        </div>
                    </Link>
                </div>

                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-500">Module</h3>
                <div v-if="modules.length" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="module in modules"
                        :key="module.name"
                        :href="module.href"
                        class="group overflow-hidden rounded-xl bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg"
                    >
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl text-white" :class="module.color">
                            <Icon :name="module.icon" class="h-6 w-6" />
                        </span>
                        <div class="mt-4 text-lg font-semibold text-slate-900 group-hover:text-blue-700">{{ module.name }}</div>
                        <div class="mt-1 text-sm text-slate-500">{{ module.description }}</div>
                    </Link>
                </div>
                <div v-else class="overflow-hidden rounded-xl bg-white p-6 text-gray-900 shadow-sm">
                    Nu ai niciun modul asignat. Contacteaza un administrator.
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
