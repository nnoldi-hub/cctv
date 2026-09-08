<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({ pages: Array });
const page = usePage();
</script>

<template>
    <Head title="Pagini CMS" />
    <AuthenticatedLayout>
        <template #header><h2 class="text-xl font-semibold text-gray-800">Pagini publice</h2></template>
        <div class="py-8"><div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
            <div v-if="page.props.flash.success" class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">{{ page.props.flash.success }}</div>
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Pagina</th><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Sectiuni</th><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Status</th><th></th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="item in pages" :key="item.id"><td class="px-4 py-3"><div class="font-medium">{{ item.title }}</div><div class="text-xs text-slate-500">/{{ item.slug }}</div></td><td class="px-4 py-3 text-sm">{{ item.sections_count }}</td><td class="px-4 py-3 text-sm">{{ item.status === 'published' ? 'Publicata' : 'Ciorna' }}</td><td class="px-4 py-3 text-right"><Link :href="route('admin.pages.edit', item.id)" class="text-blue-600 hover:text-blue-800">Editeaza</Link></td></tr>
                    </tbody>
                </table>
            </div>
        </div></div>
    </AuthenticatedLayout>
</template>
