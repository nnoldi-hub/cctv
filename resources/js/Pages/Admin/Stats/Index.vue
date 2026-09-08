<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({ stats: Array });
const page = usePage();
const form = useForm({ key: '', value: '', description: '', icon: '', is_dynamic: false });
function add() { form.post(route('admin.stats.store'), { onSuccess: () => form.reset() }); }
function update(stat) { router.put(route('admin.stats.update', stat.id), stat); }
function destroy(stat) { if (confirm(`Stergi statistica "${stat.description}"?`)) router.delete(route('admin.stats.destroy', stat.id)); }
</script>

<template>
    <Head title="Statistici" />
    <AuthenticatedLayout>
        <template #header><h2 class="text-xl font-semibold text-gray-800">Statistici publice</h2></template>
        <div class="py-8"><div class="mx-auto max-w-6xl space-y-6 sm:px-6 lg:px-8">
            <div v-if="page.props.flash.success" class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ page.props.flash.success }}</div>
            <form class="grid gap-3 rounded-lg bg-white p-5 shadow-sm sm:grid-cols-5" @submit.prevent="add"><input v-model="form.key" placeholder="instalari_finalizate" class="rounded-md border-slate-300" /><input v-model="form.value" placeholder="500+" class="rounded-md border-slate-300" /><input v-model="form.description" placeholder="Descriere" class="rounded-md border-slate-300" /><input v-model="form.icon" placeholder="shield" class="rounded-md border-slate-300" /><button class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white">Adauga</button></form>
            <div class="overflow-x-auto rounded-lg bg-white shadow-sm"><table class="min-w-full divide-y divide-slate-200"><tbody class="divide-y divide-slate-100"><tr v-for="stat in stats" :key="stat.id"><td class="px-3 py-3"><input v-model="stat.key" class="w-full rounded border-slate-300 text-sm" /></td><td class="px-3 py-3"><input v-model="stat.value" class="w-full rounded border-slate-300 text-sm" /></td><td class="px-3 py-3"><input v-model="stat.description" class="w-full rounded border-slate-300 text-sm" /></td><td class="px-3 py-3"><button class="text-blue-600" @click="update(stat)">Salveaza</button><button class="ml-3 text-red-600" @click="destroy(stat)">Sterge</button></td></tr><tr v-if="!stats.length"><td class="px-4 py-6 text-center text-sm text-slate-400" colspan="4">Nicio statistica.</td></tr></tbody></table></div>
        </div></div>
    </AuthenticatedLayout>
</template>
