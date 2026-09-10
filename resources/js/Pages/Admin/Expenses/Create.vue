<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ suppliers: Array, installations: Array });
const form = useForm({
    supplier_id: '', installation_id: '', description: '', category: 'material',
    amount: 0, expense_date: new Date().toISOString().slice(0, 10), document_number: '', notes: '',
});
function submit() { form.post(route('admin.expenses.store')); }
</script>

<template>
    <Head title="Cheltuiala noua" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Cheltuiala noua</h2>
                <Link :href="route('admin.expenses.index')" class="text-sm text-blue-600">Inapoi la cheltuieli</Link>
            </div>
        </template>
        <div class="py-8">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <form class="space-y-6 rounded-lg bg-white p-6 shadow-sm" @submit.prevent="submit">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2"><label class="block text-sm font-medium text-slate-700">Descriere *</label><input v-model="form.description" required class="mt-1 block w-full rounded-md border-slate-300" placeholder="Ex: Cablu UTP si conectori" /></div>
                        <div><label class="block text-sm font-medium text-slate-700">Categorie *</label><select v-model="form.category" class="mt-1 block w-full rounded-md border-slate-300"><option value="material">Material</option><option value="transport">Transport</option><option value="manopera">Manopera</option><option value="other">Altele</option></select></div>
                        <div><label class="block text-sm font-medium text-slate-700">Suma (lei) *</label><input v-model.number="form.amount" required type="number" min="0.01" step="0.01" class="mt-1 block w-full rounded-md border-slate-300" /></div>
                        <div><label class="block text-sm font-medium text-slate-700">Furnizor</label><select v-model="form.supplier_id" class="mt-1 block w-full rounded-md border-slate-300"><option value="">Fara furnizor</option><option v-for="supplier in props.suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option></select></div>
                        <div><label class="block text-sm font-medium text-slate-700">Lucrare / instalare</label><select v-model="form.installation_id" class="mt-1 block w-full rounded-md border-slate-300"><option value="">Cheltuiala generala</option><option v-for="installation in props.installations" :key="installation.id" :value="installation.id">{{ installation.report_number || `Instalare #${installation.id}` }} - {{ installation.client?.name }}</option></select></div>
                        <div><label class="block text-sm font-medium text-slate-700">Data *</label><input v-model="form.expense_date" required type="date" class="mt-1 block w-full rounded-md border-slate-300" /></div>
                        <div><label class="block text-sm font-medium text-slate-700">Nr. document</label><input v-model="form.document_number" class="mt-1 block w-full rounded-md border-slate-300" placeholder="Factura furnizor" /></div>
                        <div class="sm:col-span-2"><label class="block text-sm font-medium text-slate-700">Note</label><textarea v-model="form.notes" rows="3" class="mt-1 block w-full rounded-md border-slate-300" /></div>
                    </div>
                    <button :disabled="form.processing" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">Salveaza cheltuiala</button>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
