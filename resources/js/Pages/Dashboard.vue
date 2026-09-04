<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const roles = computed(() => page.props.auth.roles ?? []);
const hasRole = (...names) => names.some((name) => roles.value.includes(name));

const modules = computed(() => [
    {
        name: 'Vanzari',
        description: 'CRM, clienti, oferte',
        href: route('sales.dashboard'),
        visible: hasRole('admin', 'vanzari'),
    },
    {
        name: 'Tehnic',
        description: 'Echipamente, instalari, suport',
        href: route('technical.dashboard'),
        visible: hasRole('admin', 'tehnic', 'suport'),
    },
    {
        name: 'Administrativ',
        description: 'Utilizatori, facturi, KPI',
        href: route('admin.dashboard'),
        visible: hasRole('admin'),
    },
].filter((m) => m.visible));
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="modules.length" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="module in modules"
                        :key="module.name"
                        :href="module.href"
                        class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow"
                    >
                        <div class="text-lg font-semibold text-gray-900">{{ module.name }}</div>
                        <div class="text-sm text-gray-500">{{ module.description }}</div>
                    </Link>
                </div>
                <div v-else class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6 text-gray-900">
                    Nu ai niciun modul asignat. Contacteaza un administrator.
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
