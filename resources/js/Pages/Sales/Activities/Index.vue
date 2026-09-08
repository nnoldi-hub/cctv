<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';

const props = defineProps({ activities: Object, filters: Object, users: Array });
const page = usePage();
const form = reactive({ status: props.filters.status ?? '', assigned_to: props.filters.assigned_to ?? '' });
watch(form, () => router.get(route('sales.activities.index'), form, { preserveState: true, replace: true }));
const labels = { call: 'Apel', email: 'Email', meeting: 'Intalnire', visit: 'Vizita', task: 'Sarcina', note: 'Nota' };
const priorityClasses = { low: 'bg-slate-100 text-slate-600', normal: 'bg-blue-100 text-blue-700', high: 'bg-red-100 text-red-700' };
function toggle(activity) { router.patch(route('sales.activities.status', activity.id)); }
function destroy(activity) { if (confirm(`Stergi activitatea "${activity.title}"?`)) router.delete(route('sales.activities.destroy', activity.id)); }
</script>

<template>
    <Head title="Activitati" />
    <AuthenticatedLayout>
        <template #header><div class="flex items-center justify-between"><h2 class="text-xl font-semibold leading-tight text-gray-800">Activitati si follow-up</h2><Link :href="route('sales.activities.create')" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-500">Activitate noua</Link></div></template>
        <div class="py-8"><div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div v-if="page.props.flash.success" class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">{{ page.props.flash.success }}</div>
            <div class="mb-4 flex gap-3"><select v-model="form.status" class="rounded-md border-slate-300 text-sm"><option value="">Toate statusurile</option><option value="pending">In asteptare</option><option value="completed">Finalizate</option><option value="cancelled">Anulate</option></select><select v-model="form.assigned_to" class="rounded-md border-slate-300 text-sm"><option value="">Toti responsabilii</option><option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option></select></div>
            <div class="overflow-x-auto rounded-lg bg-white shadow-sm"><table class="min-w-full divide-y divide-slate-200"><thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Activitate</th><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Client</th><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Termen</th><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Prioritate</th><th class="px-4 py-3"></th></tr></thead><tbody class="divide-y divide-slate-100"><tr v-for="activity in activities.data" :key="activity.id" class="hover:bg-slate-50"><td class="px-4 py-3"><div class="font-medium text-slate-900">{{ activity.title }}</div><div class="text-xs text-slate-400">{{ labels[activity.type] }} · {{ activity.assigned_to?.name ?? 'Neasignat' }}</div></td><td class="px-4 py-3 text-sm"><Link :href="route('sales.clients.show', activity.client.id)" class="text-blue-600 hover:text-blue-500">{{ activity.client.name }}</Link></td><td class="px-4 py-3 text-sm text-slate-600">{{ activity.due_at ? new Date(activity.due_at).toLocaleString('ro-RO') : '-' }}</td><td class="px-4 py-3"><span class="rounded-full px-2 py-1 text-xs font-medium" :class="priorityClasses[activity.priority]">{{ activity.priority }}</span></td><td class="px-4 py-3 text-right text-sm"><button class="text-green-600 hover:text-green-500" @click="toggle(activity)">{{ activity.status === 'completed' ? 'Redeschide' : 'Finalizeaza' }}</button><button class="ml-3 text-red-500 hover:text-red-700" @click="destroy(activity)">Sterge</button></td></tr><tr v-if="!activities.data.length"><td colspan="5" class="px-4 py-8 text-center text-sm text-slate-400">Nicio activitate.</td></tr></tbody></table></div>
        </div></div>
    </AuthenticatedLayout>
</template>
