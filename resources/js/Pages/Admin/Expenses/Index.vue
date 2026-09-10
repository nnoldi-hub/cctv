<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';
const props = defineProps({ expenses: Object, summary: Object, filters: Object });
const filters = reactive({ category: props.filters?.category ?? '' });
let filterTimer = null;
watch(() => filters.category, () => {
    clearTimeout(filterTimer);
    filterTimer = setTimeout(() => router.get(route('admin.expenses.index'), filters, { preserveState: true, replace: true }), 250);
});
function money(value) { return Number(value).toLocaleString('ro-RO', { minimumFractionDigits: 2 }); }
function destroy(item) { if (confirm(`Stergi cheltuiala "${item.description}"?`)) router.delete(route('admin.expenses.destroy', item.id)); }
</script>
<template>
    <Head title="Cheltuieli si achizitii" />
    <AuthenticatedLayout>
        <template #header><div class="flex items-center justify-between"><h2 class="text-xl font-semibold text-gray-800">Cheltuieli si achizitii</h2><Link :href="route('admin.expenses.create')" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white">Cheltuiala noua</Link></div></template>
        <div class="py-8"><div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2"><div class="rounded-lg bg-white p-5 shadow-sm"><div class="text-sm text-slate-500">Total cheltuieli</div><div class="mt-1 text-2xl font-bold text-slate-900">{{ money(props.summary.total) }} lei</div></div><div class="rounded-lg bg-white p-5 shadow-sm"><div class="text-sm text-slate-500">Luna curenta</div><div class="mt-1 text-2xl font-bold text-orange-600">{{ money(props.summary.this_month) }} lei</div></div></div>
            <div class="rounded-lg bg-white p-4 shadow-sm"><label class="text-sm font-medium text-slate-700">Filtreaza categoria</label><select v-model="filters.category" class="ml-3 rounded-md border-slate-300 text-sm"><option value="">Toate</option><option value="material">Material</option><option value="transport">Transport</option><option value="manopera">Manopera</option><option value="other">Altele</option></select></div>
            <div class="overflow-x-auto rounded-lg bg-white shadow-sm"><table class="min-w-full divide-y divide-slate-200"><thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Data / Descriere</th><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Categorie</th><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Furnizor / Lucrare</th><th class="px-4 py-3 text-right text-xs uppercase text-slate-500">Suma</th><th></th></tr></thead><tbody class="divide-y divide-slate-100"><tr v-for="item in props.expenses.data" :key="item.id"><td class="px-4 py-3"><div class="font-medium text-slate-900">{{ item.description }}</div><div class="text-xs text-slate-400">{{ item.expense_date }}</div></td><td class="px-4 py-3 text-sm capitalize text-slate-600">{{ item.category }}</td><td class="px-4 py-3 text-sm text-slate-600">{{ item.supplier?.name || 'Fara furnizor' }}<div v-if="item.installation" class="text-xs text-blue-600">{{ item.installation.report_number || `Instalare #${item.installation.id}` }}</div></td><td class="px-4 py-3 text-right font-semibold text-slate-900">{{ money(item.amount) }} lei</td>                        <td class="px-4 py-3 text-right"><a v-if="item.document_url" :href="item.document_url" target="_blank" class="mr-3 text-slate-600 hover:text-slate-800">Document</a><Link :href="route('admin.expenses.edit', item.id)" class="mr-3 text-blue-600 hover:text-blue-800">Editeaza</Link><button class="text-red-500 hover:text-red-700" @click="destroy(item)">Sterge</button></td></tr><tr v-if="!props.expenses.data.length"><td colspan="5" class="px-4 py-8 text-center text-sm text-slate-400">Nu exista cheltuieli.</td></tr></tbody></table></div>
            <div v-if="props.expenses.links.length > 3" class="flex flex-wrap gap-2"><Link v-for="link in props.expenses.links" :key="link.label" :href="link.url ?? '#'" :class="['rounded-md px-3 py-1.5 text-sm', link.active ? 'bg-blue-600 text-white' : 'bg-white text-slate-500', !link.url ? 'pointer-events-none opacity-40' : '']" v-html="link.label" /></div>
        </div></div>
    </AuthenticatedLayout>
</template>
