<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ suppliers: Array });
const supplierForm = useForm({ name: '', contact_name: '', email: '', phone: '', tax_id: '', notes: '' });
const importForm = useForm({
    supplier_id: '', file: null, markup_percent: 30,
    name_column: 'name', cost_column: 'cost_price', sku_column: 'sku',
    category_column: 'category', unit_column: 'unit', stock_column: 'stock', description_column: 'description',
});
function addSupplier() { supplierForm.post(route('technical.suppliers.store'), { onSuccess: () => supplierForm.reset() }); }
function importFile() { importForm.post(route('technical.suppliers.import'), { forceFormData: true }); }
</script>

<template>
    <Head title="Furnizori si import materiale" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Furnizori si import materiale</h2>
                <Link :href="route('technical.equipment.index')" class="text-sm text-blue-600">Catalog echipamente</Link>
            </div>
        </template>
        <div class="py-8">
            <div class="mx-auto grid max-w-7xl gap-6 sm:px-6 lg:grid-cols-2 lg:px-8">
                <form class="rounded-lg bg-white p-6 shadow-sm" @submit.prevent="addSupplier">
                    <h3 class="mb-4 text-lg font-semibold text-slate-800">Furnizor nou</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <input v-model="supplierForm.name" required placeholder="Denumire furnizor *" class="rounded-md border-slate-300 sm:col-span-2" />
                        <input v-model="supplierForm.contact_name" placeholder="Persoana contact" class="rounded-md border-slate-300" />
                        <input v-model="supplierForm.tax_id" placeholder="CUI" class="rounded-md border-slate-300" />
                        <input v-model="supplierForm.email" type="email" placeholder="Email" class="rounded-md border-slate-300" />
                        <input v-model="supplierForm.phone" placeholder="Telefon" class="rounded-md border-slate-300" />
                        <textarea v-model="supplierForm.notes" placeholder="Note" class="rounded-md border-slate-300 sm:col-span-2" />
                    </div>
                    <button class="mt-4 rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Salveaza furnizor</button>
                </form>
                <form class="rounded-lg bg-white p-6 shadow-sm" @submit.prevent="importFile">
                    <h3 class="mb-2 text-lg font-semibold text-slate-800">Import CSV / Excel</h3>
                    <p class="mb-4 text-sm text-slate-500">Scrie exact numele coloanelor din primul rand al fisierului.</p>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <select v-model="importForm.supplier_id" required class="rounded-md border-slate-300 sm:col-span-2">
                            <option value="">Alege furnizorul *</option>
                            <option v-for="supplier in props.suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                        </select>
                        <input v-model="importForm.name_column" required placeholder="Coloana denumire" class="rounded-md border-slate-300" />
                        <input v-model="importForm.cost_column" required placeholder="Coloana cost achizitie" class="rounded-md border-slate-300" />
                        <input v-model="importForm.sku_column" placeholder="Coloana SKU" class="rounded-md border-slate-300" />
                        <input v-model="importForm.category_column" placeholder="Coloana categorie" class="rounded-md border-slate-300" />
                        <input v-model="importForm.unit_column" placeholder="Coloana unitate" class="rounded-md border-slate-300" />
                        <input v-model="importForm.stock_column" placeholder="Coloana stoc" class="rounded-md border-slate-300" />
                        <input v-model.number="importForm.markup_percent" type="number" min="0" step="0.01" placeholder="Adaos %" class="rounded-md border-slate-300" />
                        <input type="file" accept=".csv,.txt,.xlsx,.xls" required class="rounded-md border border-slate-300 p-2 text-sm sm:col-span-2" @input="importForm.file = $event.target.files[0]" />
                    </div>
                    <button :disabled="importForm.processing" class="mt-4 rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">Importa si calculeaza preturile</button>
                </form>
                <div class="rounded-lg bg-white shadow-sm lg:col-span-2">
                    <div class="border-b px-6 py-4 font-semibold text-slate-800">Furnizori existenti</div>
                    <div class="divide-y">
                        <div v-for="supplier in props.suppliers" :key="supplier.id" class="flex justify-between px-6 py-3 text-sm">
                            <span>{{ supplier.name }} <span class="text-slate-400">({{ supplier.email || 'fara email' }})</span></span>
                            <span class="text-slate-500">{{ supplier.equipment_count }} materiale</span>
                        </div>
                        <div v-if="!props.suppliers.length" class="px-6 py-6 text-sm text-slate-400">Nu exista furnizori.</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
