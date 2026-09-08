<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Link } from '@inertiajs/vue3';
defineProps({ client: Object, openTickets: Number, scheduledWorks: Number, unpaidInvoices: Number, unreadNotifications: Number, recentTickets: Array });
</script>

<template>
    <ClientLayout title="Dashboard client">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8"><h1 class="text-2xl font-bold text-slate-900">Bun venit, {{ client.name }}</h1><p class="mt-1 text-slate-500">{{ client.company_name || 'Portalul tau de client' }}</p></div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4"><Link :href="route('client.tickets.index')" class="rounded-xl bg-white p-5 shadow-sm"><div class="text-sm text-slate-500">Cereri deschise</div><div class="mt-2 text-3xl font-bold text-blue-700">{{ openTickets }}</div></Link><Link :href="route('client.works.index')" class="rounded-xl bg-white p-5 shadow-sm"><div class="text-sm text-slate-500">Lucrari programate</div><div class="mt-2 text-3xl font-bold text-orange-600">{{ scheduledWorks }}</div></Link><Link :href="route('client.invoices.index')" class="rounded-xl bg-white p-5 shadow-sm"><div class="text-sm text-slate-500">Facturi neachitate</div><div class="mt-2 text-3xl font-bold text-red-600">{{ unpaidInvoices }}</div></Link><Link :href="route('client.notifications.index')" class="rounded-xl bg-white p-5 shadow-sm"><div class="text-sm text-slate-500">Notificari necitite</div><div class="mt-2 text-3xl font-bold text-slate-900">{{ unreadNotifications }}</div></Link></div>
            <div class="mt-8 rounded-xl bg-white p-6 shadow-sm"><div class="flex items-center justify-between"><h2 class="font-semibold text-slate-900">Cereri recente</h2><Link :href="route('client.tickets.index')" class="text-sm text-blue-600">Vezi toate</Link></div><div v-if="recentTickets.length" class="mt-4 divide-y"><div v-for="ticket in recentTickets" :key="ticket.id" class="flex items-center justify-between py-3"><div><div class="font-medium text-slate-800">{{ ticket.subject }}</div><div class="text-xs text-slate-500">{{ ticket.created_at }}</div></div><span class="rounded-full bg-slate-100 px-3 py-1 text-xs">{{ ticket.status }}</span></div></div><p v-else class="mt-4 text-sm text-slate-500">Nu ai cereri recente.</p></div>
        </div>
    </ClientLayout>
</template>
