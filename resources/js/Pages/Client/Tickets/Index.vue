<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
defineProps({ tickets: Object });
const page = usePage();
const form = useForm({ subject: '', description: '', priority: 'medium' });
const commentBody = ref('');
function submit() { form.post(route('client.tickets.store'), { onSuccess: () => form.reset() }); }
function submitComment(ticket) {
    useForm({ body: commentBody.value }).post(route('client.tickets.comments', ticket.id), {
        preserveScroll: true,
        onSuccess: () => { commentBody.value = ''; },
    });
}
</script>

<template>
    <ClientLayout title="Cereri si tichete">
        <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-slate-900">Cereri si tichete</h1>
            <div v-if="page.props.flash.success" class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ page.props.flash.success }}</div>
            <form class="rounded-xl bg-white p-6 shadow-sm" @submit.prevent="submit">
                <h2 class="font-semibold">Deschide o cerere</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2"><input v-model="form.subject" required placeholder="Subiect" class="rounded-md border-slate-300" /><select v-model="form.priority" class="rounded-md border-slate-300"><option value="low">Prioritate redusa</option><option value="medium">Prioritate medie</option><option value="high">Urgenta</option></select><textarea v-model="form.description" required rows="3" placeholder="Descrierea problemei" class="rounded-md border-slate-300 sm:col-span-2" /></div>
                <button class="mt-4 rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Trimite cererea</button>
            </form>
            <div v-for="ticket in tickets.data" :key="ticket.id" class="rounded-xl bg-white p-6 shadow-sm">
                <div class="flex flex-wrap justify-between gap-3"><div><h2 class="font-semibold text-slate-900">{{ ticket.subject }}</h2><p class="text-sm text-slate-500">Creat la {{ new Date(ticket.created_at).toLocaleString('ro-RO') }}</p></div><span class="rounded-full bg-blue-50 px-3 py-1 text-xs text-blue-700">{{ ticket.status }}</span></div>
                <p class="mt-3 whitespace-pre-line text-sm text-slate-700">{{ ticket.description }}</p>
                <div class="mt-5 border-l-2 border-blue-100 pl-4"><h3 class="text-sm font-semibold text-slate-700">Timeline</h3><div v-for="event in ticket.events" :key="event.id" class="mt-3"><p class="text-sm text-slate-700">{{ event.description }}</p><p class="text-xs text-slate-400">{{ event.user?.name || 'Sistem' }} · {{ new Date(event.created_at).toLocaleString('ro-RO') }}</p></div></div>
                <form class="mt-5" @submit.prevent="submitComment(ticket)">
                    <textarea v-model="commentBody" required rows="2" placeholder="Adauga un raspuns..." class="w-full rounded-md border-slate-300"></textarea>
                    <button class="mt-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Trimite raspuns</button>
                </form>
            </div>
            <p v-if="!tickets.data.length" class="rounded-xl bg-white p-6 text-slate-500">Nu exista cereri inregistrate.</p>
        </div>
    </ClientLayout>
</template>
