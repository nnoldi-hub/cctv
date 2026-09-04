<script setup>
import Icon from '@/Components/Icon.vue';
import PackageCard from '@/Components/PackageCard.vue';
import SeoHead from '@/Components/SeoHead.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    packages: Array,
    latestPosts: Array,
});

function formatDate(value) {
    return new Date(value).toLocaleDateString('ro-RO', { day: 'numeric', month: 'long', year: 'numeric' });
}

const stats = [
    { icon: 'clock', value: '10+', label: 'Ani experienta' },
    { icon: 'home', value: '500+', label: 'Instalari finalizate' },
    { icon: 'shield', value: '36 luni', label: 'Garantie' },
    { icon: 'bolt', value: '24-48h', label: 'Interventie' },
];

const steps = [
    { icon: 'clipboard', title: 'Cerere oferta', text: 'Ne trimiti detalii despre proprietate si nevoile tale.' },
    { icon: 'truck', title: 'Vizita tehnica', text: 'Evaluam locatia si stabilim configuratia optima.' },
    { icon: 'wrench', title: 'Instalare', text: 'Montam si configuram sistemul complet.' },
    { icon: 'lifebuoy', title: 'Suport', text: 'Oferim mentenanta si interventie rapida.' },
];
</script>

<template>
    <SeoHead
        title="Sisteme de supraveghere video CCTV"
        description="Instalam sisteme de supraveghere video (CCTV) pentru locuinte si firme: camere IP/analogice, NVR, mentenanta si suport tehnic."
    />

    <PublicLayout>
        <section class="relative overflow-hidden bg-brand-navy">
            <div class="pointer-events-none absolute -left-32 -top-32 h-96 w-96 rounded-full bg-orange-500/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-24 top-1/3 h-80 w-80 rounded-full bg-blue-500/20 blur-3xl"></div>
            <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.04)_1px,transparent_1px)] bg-[size:44px_44px] [mask-image:radial-gradient(ellipse_80%_60%_at_50%_0%,black,transparent)]"></div>

            <div class="relative mx-auto grid max-w-7xl grid-cols-1 items-center gap-10 px-4 py-24 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-32">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full border border-orange-400/30 bg-orange-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-orange-300">
                        <Icon name="sparkles" class="h-3.5 w-3.5" />
                        Consultanta &amp; instalare in toata tara
                    </span>
                    <h1 class="mt-6 font-display text-4xl font-extrabold tracking-tight text-white sm:text-6xl">
                        Siguranta incepe cu
                        <span class="bg-gradient-to-r from-orange-400 to-blue-400 bg-clip-text text-transparent">vizibilitate.</span>
                    </h1>
                    <p class="mt-6 max-w-xl text-lg text-slate-300">
                        Proiectam si instalam sisteme complete de supraveghere video pentru case,
                        firme si spatii comerciale. Consultanta gratuita, echipamente de calitate
                        si suport tehnic dupa instalare.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <Link
                            :href="route('public.contact')"
                            class="rounded-md bg-orange-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-500/25 transition hover:bg-orange-400"
                        >
                            Cere oferta gratuita
                        </Link>
                        <Link
                            :href="route('public.configurator')"
                            class="rounded-md border border-slate-600 px-6 py-3 text-sm font-semibold text-white transition hover:border-slate-400 hover:bg-white/5"
                        >
                            Configureaza-ti sistemul
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div
                        v-for="stat in stats"
                        :key="stat.label"
                        class="group rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur transition hover:border-orange-400/40 hover:bg-white/10"
                    >
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-orange-500 to-blue-600 text-white shadow-lg">
                            <Icon :name="stat.icon" class="h-5 w-5" />
                        </div>
                        <dd class="mt-4 text-2xl font-bold text-white">{{ stat.value }}</dd>
                        <dt class="text-sm text-slate-400">{{ stat.label }}</dt>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="font-display text-3xl font-bold text-slate-900">Pachete CCTV</h2>
                <p class="mt-3 text-slate-500">Alege un pachet orientativ sau cere o oferta personalizata.</p>
            </div>
            <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-3">
                <PackageCard v-for="pkg in packages" :key="pkg.key" :pkg="pkg" />
            </div>
        </section>

        <section class="relative overflow-hidden bg-slate-50 py-16">
            <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,rgba(15,23,42,0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgba(15,23,42,0.05)_1px,transparent_1px)] bg-[size:40px_40px]"></div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="font-display text-3xl font-bold text-slate-900">Cum lucram</h2>
                </div>
                <div class="relative mt-12">
                    <div class="absolute left-0 right-0 top-6 hidden h-0.5 bg-gradient-to-r from-orange-300 via-slate-300 to-blue-300 sm:block"></div>
                    <div class="grid grid-cols-1 gap-10 sm:grid-cols-4">
                        <div v-for="step in steps" :key="step.title" class="relative text-center">
                            <div class="relative mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white text-blue-600 shadow-md ring-4 ring-slate-50">
                                <Icon :name="step.icon" class="h-5 w-5" />
                            </div>
                            <h3 class="mt-4 font-display font-semibold text-slate-900">{{ step.title }}</h3>
                            <p class="mt-2 text-sm text-slate-500">{{ step.text }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section v-if="latestPosts.length" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-3xl font-bold text-slate-900">Din blog</h2>
                <Link :href="route('public.blog.index')" class="text-sm font-semibold text-blue-600 hover:text-blue-500">
                    Vezi toate articolele &rarr;
                </Link>
            </div>
            <div class="mt-8 grid grid-cols-1 gap-8 sm:grid-cols-3">
                <Link
                    v-for="post in latestPosts"
                    :key="post.slug"
                    :href="route('public.blog.show', post.slug)"
                    class="group block overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
                >
                    <div class="flex h-28 items-center justify-center bg-gradient-to-br from-brand-navy via-blue-900 to-brand-navy">
                        <Icon name="camera" class="h-9 w-9 text-orange-400/80" />
                    </div>
                    <div class="p-6">
                        <time class="text-xs font-medium uppercase tracking-wide text-orange-600">{{ formatDate(post.published_at) }}</time>
                        <h3 class="mt-2 font-display font-semibold text-slate-900 group-hover:text-blue-600">{{ post.title }}</h3>
                        <p class="mt-2 text-sm text-slate-500">{{ post.excerpt }}</p>
                    </div>
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>
