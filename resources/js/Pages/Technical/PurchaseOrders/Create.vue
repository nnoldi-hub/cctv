<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
const props = defineProps({ suppliers: Array, equipment: Array });
const form = useForm({ supplier_id: '', ordered_at: new Date().toISOString().slice(0, 10), notes: '', items: [{ equipment_id: '', quantity: 1, unit_cost: 0 }] });
const total = computed(() => form.items.reduce((sum, item) => sum + Number(item.quantity || 0) * Number(item.unit_cost || 0), 0));
function addItem() { form.items.push({ equipment_id: '', quantity: 1, unit_cost: 0 }); }
function updateCost(item) { const product = props.equipment.find((entry) => entry.id == item.equipment_id); if (product) item.unit_cost = Number(product.cost_price); }
function submit() { form.post(route('technical.purchase-orders.store')); }
</script>
<template>
    <Head title="Comanda furnizor" /><AuthenticatedLayout>
        <template #header><div class="flex items-center justify-between"><h2 class="text-xl font-semibold text-gray-800">Comanda furnizor</h2><Link :href="route('technical.purchase-orders.index')" class="text-sm text-blue-600">Inapoi</Link></div></template>
        <div class="py-8"><div class="mx-auto max-w-5xl sm:px-6 lg:px-8"><form class="space-y-6 rounded-lg bg-white p-6 shadow-sm" @submit.prevent="submit">
            <div class="grid gap-4 sm:grid-cols-2"><div><label class="text-sm font-medium">Furnizor *</label><select v-model="form.supplier_id" required class="mt-1 w-full rounded-md border-slate-300"><option value="">Alege furnizorul</option><option v-for="supplier in props.suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option></select></div><div><label class="text-sm font-medium">Data comenzii</label><input v-model="form.ordered_at" type="date" class="mt-1 w-full rounded-md border-slate-300" /></div></div>
            <div><div class="mb-2 flex items-center justify-between"><h3 class="font-semibold">Materiale</h3><button type="button" class="text-sm text-blue-600" @click="addItem">+ Adauga material</button></div><div v-for="(item, index) in form.items" :key="index" class="mb-3 grid gap-3 sm:grid-cols-12"><select v-model="item.equipment_id" required class="rounded-md border-slate-300 sm:col-span-6" @change="updateCost(item)"><option value="">Alege material</option><option v-for="product in props.equipment" :key="product.id" :value="product.id">{{ product.name }} ({{ product.unit }})</option></select><input v-model.number="item.quantity" type="number" min="0.01" step="0.01" class="rounded-md border-slate-300 sm:col-span-2" placeholder="Cant." /><input v-model.number="item.unit_cost" type="number" min="0" step="0.01" class="rounded-md border-slate-300 sm:col-span-3" placeholder="Cost unitar" /><button v-if="form.items.length > 1" type="button" class="text-red-500" @click="form.items.splice(index, 1)">Sterge</button></div></div>
            <textarea v-model="form.notes" rows="3" class="w-full rounded-md border-slate-300" placeholder="Note"></textarea><div class="flex items-center justify-between border-t pt-4"><strong>Total: {{ total.toLocaleString('ro-RO', { minimumFractionDigits: 2 }) }} lei</strong><button :disabled="form.processing" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Creeaza comanda</button></div>
        </form></div></div>
    </AuthenticatedLayout>
</template>
