<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({ title: { type: String, default: 'Portal Client' } });
const page = usePage();
const user = page.props.auth.user;
const logout = useForm({});
function signOut() { logout.post(route('logout')); }
</script>

<template>
    <Head :title="title" />
    <div class="min-h-screen bg-slate-100">
        <header class="bg-brand-navy text-white shadow">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <Link :href="route('client.dashboard')" class="flex items-center gap-2"><ApplicationLogo class="h-8 w-8" /><span class="font-display font-bold">Portal <span class="text-orange-400">Client</span></span></Link>
                <div class="flex items-center gap-4 text-sm"><span class="hidden text-slate-300 sm:inline">{{ user.name }}</span><button class="text-slate-300 hover:text-white" @click="signOut">Iesire</button></div>
            </div>
        </header>
        <nav class="border-b bg-white">
            <div class="mx-auto flex max-w-7xl gap-1 overflow-x-auto px-4 py-2 text-sm sm:px-6 lg:px-8">
                <Link v-for="item in [{ label: 'Dashboard', route: 'client.dashboard' }, { label: 'Cereri', route: 'client.tickets.index' }, { label: 'Lucrari', route: 'client.works.index' }, { label: 'Facturi', route: 'client.invoices.index' }, { label: 'Abonament', route: 'client.subscriptions.index' }, { label: 'Echipamente', route: 'client.equipment.index' }, { label: 'Notificari', route: 'client.notifications.index' }]" :key="item.route" :href="route(item.route)" class="whitespace-nowrap rounded px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-blue-700">{{ item.label }}</Link>
            </div>
        </nav>
        <main><slot /></main>
    </div>
</template>
