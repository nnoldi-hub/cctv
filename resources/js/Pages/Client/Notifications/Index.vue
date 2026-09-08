<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { router } from '@inertiajs/vue3';
defineProps({ notifications: Object });
function markRead(notification) { router.patch(route('client.notifications.read', notification.id)); }
</script>
<template><ClientLayout title="Notificarile mele"><div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8"><h1 class="text-2xl font-bold text-slate-900">Notificari</h1><div class="mt-6 divide-y rounded-xl bg-white shadow-sm"><div v-for="notification in notifications.data" :key="notification.id" class="flex items-start justify-between gap-4 p-5" :class="notification.read_at ? 'opacity-60' : ''"><div><div class="font-medium text-slate-900">{{ notification.data?.title || 'Notificare' }}</div><p class="mt-1 text-sm text-slate-600">{{ notification.data?.message || notification.data?.body || 'Ai o actualizare noua.' }}</p><div class="mt-2 text-xs text-slate-400">{{ notification.created_at }}</div></div><button v-if="!notification.read_at" class="text-xs text-blue-600" @click="markRead(notification)">Marcheaza citita</button></div><p v-if="!notifications.data.length" class="p-6 text-slate-500">Nu ai notificari.</p></div></div></ClientLayout></template>
