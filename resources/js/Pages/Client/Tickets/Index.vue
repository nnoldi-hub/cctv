<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
defineProps({ tickets: Object });
const page = usePage();
const form = useForm({ subject: '', description: '', priority: 'medium' });
function submit() { form.post(route('client.tickets.store'), { onSuccess: () => form.reset() }); }
</script>

<template>
    <ClientLayout title="Cereri si tichete"><div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8"><h1 class="text-2xl font-bold text-slate-900">Cereri si tichete</h1><div v-if="page.props.flash.success" class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ page.props.flash.success }}</div><form class="rounded-xl bg-white p-6 shadow-sm" @submit.prevent="submit"><h2 class="font-semibold">Deschide o cerere</h2><div class="mt-4 grid gap-4 sm:grid-cols-2"><input v-model="form.subject" required placeholder="Subiect" class="rounded-md border-slate-300" /><select v-model="form.priority" class="rounded-md border-slate-300"><option value="low">Prioritate redusa</option><option value="medium">Prioritate medie</option><option value="high">Urgenta</option></select><textarea v-model="form.description" required rows="3" placeholder="Descrierea problemei" class="rounded-md border-slate-300 sm:col-span-2" /></div><button class="mt-4 rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Trimite cererea</button></form><div class="overflow-x-auto rounded-xl bg-white shadow-sm"><table class="min-w-full divide-y"><thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Subiect</th><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Status</th><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Tehnician</th></tr></thead><tbody class="divide-y"><tr v-for="ticket in tickets.data" :key="ticket.id"><td class="px-4 py-3 font-medium">{{ ticket.subject }}</td><td class="px-4 py-3 text-sm">{{ ticket.status }}</td><td class="px-4 py-3 text-sm">{{ ticket.assigned_to?.name || 'Neasignat' }}</td></tr></tbody></table></div></div></ClientLayout>
</template>
