<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({ post: Object });
const form = useForm({
    title: props.post?.title || '', slug: props.post?.slug || '', excerpt: props.post?.excerpt || '',
    body: props.post?.body || '', meta_title: props.post?.meta_title || '', meta_description: props.post?.meta_description || '',
    status: props.post?.status || 'draft', published_at: props.post?.published_at ? props.post.published_at.substring(0, 16) : '', cover_image: null,
});
function submit() {
    if (props.post) form.transform((data) => ({ ...data, _method: 'put' })).post(route('admin.blog.update', props.post.id));
    else form.post(route('admin.blog.store'));
}
</script>

<template>
    <Head :title="post ? 'Editeaza articol' : 'Articol nou'" />
    <AuthenticatedLayout>
        <template #header><h2 class="text-xl font-semibold text-gray-800">{{ post ? 'Editeaza articol' : 'Articol blog nou' }}</h2></template>
        <div class="py-8"><div class="mx-auto max-w-5xl sm:px-6 lg:px-8"><form class="space-y-5 rounded-lg bg-white p-6 shadow-sm" @submit.prevent="submit">
            <div class="grid gap-5 sm:grid-cols-2"><label class="text-sm font-medium">Titlu<input v-model="form.title" class="mt-1 block w-full rounded-md border-slate-300" /></label><label class="text-sm font-medium">Slug<input v-model="form.slug" placeholder="generat automat daca ramane gol" class="mt-1 block w-full rounded-md border-slate-300" /></label></div>
            <label class="block text-sm font-medium">Rezumat<textarea v-model="form.excerpt" rows="2" class="mt-1 block w-full rounded-md border-slate-300" /></label>
            <label class="block text-sm font-medium">Continut articol<textarea v-model="form.body" rows="16" class="mt-1 block w-full rounded-md border-slate-300" /></label>
            <div class="grid gap-5 sm:grid-cols-3"><label class="text-sm font-medium">Status<select v-model="form.status" class="mt-1 block w-full rounded-md border-slate-300"><option value="draft">Ciorna</option><option value="published">Publicat</option></select></label><label class="text-sm font-medium">Data publicarii<input v-model="form.published_at" type="datetime-local" class="mt-1 block w-full rounded-md border-slate-300" /></label><label class="text-sm font-medium">Imagine coperta<input type="file" accept="image/*" class="mt-1 block w-full rounded-md border-slate-300" @input="form.cover_image = $event.target.files[0]" /></label></div>
            <div class="grid gap-5 sm:grid-cols-2"><label class="text-sm font-medium">SEO title<input v-model="form.meta_title" class="mt-1 block w-full rounded-md border-slate-300" /></label><label class="text-sm font-medium">SEO description<input v-model="form.meta_description" class="mt-1 block w-full rounded-md border-slate-300" /></label></div>
            <div class="flex justify-end"><button :disabled="form.processing" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">Salveaza articol</button></div>
        </form></div></div>
    </AuthenticatedLayout>
</template>
