<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
defineProps({ posts: Array });
const page = usePage();
function destroy(post) { if (confirm(`Stergi articolul "${post.title}"?`)) router.delete(route('admin.blog.destroy', post.id)); }
</script>

<template>
    <Head title="Blog" />
    <AuthenticatedLayout>
        <template #header><div class="flex items-center justify-between"><h2 class="text-xl font-semibold text-gray-800">Articole blog</h2><Link :href="route('admin.blog.create')" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white">Articol nou</Link></div></template>
        <div class="py-8"><div class="mx-auto max-w-6xl sm:px-6 lg:px-8"><div v-if="page.props.flash.success" class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">{{ page.props.flash.success }}</div><div class="overflow-x-auto rounded-lg bg-white shadow-sm"><table class="min-w-full divide-y divide-slate-200"><thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Titlu</th><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Status</th><th class="px-4 py-3 text-left text-xs uppercase text-slate-500">Publicat</th><th></th></tr></thead><tbody class="divide-y divide-slate-100"><tr v-for="post in posts" :key="post.id"><td class="px-4 py-3 font-medium">{{ post.title }}</td><td class="px-4 py-3 text-sm">{{ post.status === 'published' ? 'Publicat' : 'Ciorna' }}</td><td class="px-4 py-3 text-sm text-slate-500">{{ post.published_at ? new Date(post.published_at).toLocaleDateString('ro-RO') : '-' }}</td><td class="px-4 py-3 text-right text-sm"><Link :href="route('admin.blog.edit', post.id)" class="text-blue-600">Editeaza</Link><button class="ml-3 text-red-600" @click="destroy(post)">Sterge</button></td></tr></tbody></table></div></div></div>
    </AuthenticatedLayout>
</template>
