<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({ page: Object });
</script>

<template>
    <Head :title="`Preview: ${page.title}`" />
    <PublicLayout>
        <section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mb-6 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">Preview administrator - aceasta pagina poate fi inca nepublicata.</div>
            <h1 class="font-display text-4xl font-bold text-slate-900">{{ page.content?.hero_title || page.title }}</h1>
            <p v-if="page.subtitle" class="mt-2 text-slate-500">{{ page.subtitle }}</p>
            <p v-if="page.content?.intro" class="mt-6 text-lg text-slate-600">{{ page.content.intro }}</p>
            <div v-if="page.content?.body_html" class="prose mt-6 max-w-none" v-html="page.content.body_html"></div>
            <a v-if="page.content?.cta_label" :href="page.content.cta_url || '#'" class="mt-8 inline-block rounded-md bg-blue-600 px-5 py-3 font-semibold text-white">{{ page.content.cta_label }}</a>
            <div v-for="section in page.sections" :key="section.id" class="mt-8 rounded-lg border border-slate-200 p-5">
                <h2 class="font-semibold text-slate-900">{{ section.section_key }}</h2>
                <pre class="mt-3 overflow-auto text-xs text-slate-600">{{ JSON.stringify(section.content, null, 2) }}</pre>
            </div>
        </section>
    </PublicLayout>
</template>
