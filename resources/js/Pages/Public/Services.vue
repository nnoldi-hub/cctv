<script setup>
import PackageCard from '@/Components/PackageCard.vue';
import SeoHead from '@/Components/SeoHead.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    packages: Array,
});

const services = [
    { title: 'Consultanta si proiectare', text: 'Analizam locatia si iti recomandam configuratia optima de camere si echipamente.' },
    { title: 'Instalare profesionala', text: 'Montaj, cablare si configurare completa a sistemului, cu testare la finalizare.' },
    { title: 'Mentenanta si suport', text: 'Verificari periodice, curatare camere si interventii rapide in caz de defectiune.' },
    { title: 'Extindere sisteme existente', text: 'Adaugam camere sau capacitate de stocare la instalatii deja functionale.' },
];
</script>

<template>
    <SeoHead
        title="Servicii si pachete CCTV"
        description="Servicii complete de supraveghere video: consultanta, instalare, mentenanta si pachete CCTV entry, medium si premium."
    />

    <PublicLayout>
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="font-display text-3xl font-bold text-slate-900">Servicii</h1>
                <p class="mt-3 text-slate-500">Acoperim tot ciclul de viata al unui sistem CCTV.</p>
            </div>
            <div class="mt-10 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div v-for="service in services" :key="service.title" class="rounded-xl border border-slate-200 p-6">
                    <h3 class="font-semibold text-slate-900">{{ service.title }}</h3>
                    <p class="mt-2 text-sm text-slate-500">{{ service.text }}</p>
                </div>
            </div>
        </section>

        <section class="bg-slate-50 py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="font-display text-3xl font-bold text-slate-900">Pachete CCTV</h2>
                    <p class="mt-3 text-slate-500">
                        Preturi orientative. Oferta finala se stabileste dupa o vizita tehnica gratuita.
                    </p>
                </div>
                <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <PackageCard v-for="pkg in packages" :key="pkg.key" :pkg="pkg" />
                </div>
                <div class="mt-10 text-center">
                    <Link :href="route('public.configurator')" class="text-sm font-semibold text-blue-600 hover:text-blue-500">
                        Vrei o estimare personalizata? Foloseste configuratorul &rarr;
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
