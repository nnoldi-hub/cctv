<script setup>
import SeoHead from '@/Components/SeoHead.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>

<template>
    <SeoHead
        title="Despre noi"
        description="Firma specializata in proiectare, instalare si mentenanta de sisteme CCTV pentru rezidential si comercial, cu peste 10 ani de experienta."
    />

    <PublicLayout>
        <section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
            <h1 class="font-display text-3xl font-bold text-slate-900">Despre noi</h1>
            <p class="mt-6 text-slate-600">
                Suntem o echipa de tehnicieni si consultanti specializati in sisteme de
                supraveghere video, cu peste 10 ani de experienta in proiecte rezidentiale
                si comerciale. Am instalat peste 500 de sisteme CCTV, de la locuinte
                individuale la spatii comerciale si industriale.
            </p>
            <p class="mt-4 text-slate-600">
                Misiunea noastra este sa oferim solutii de securitate accesibile si
                fiabile, adaptate fiecarui client in parte. Lucram doar cu echipamente
                verificate si oferim garantie extinsa pentru toate instalatiile noastre.
            </p>

            <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-200 p-6 text-center">
                    <div class="text-3xl font-bold text-blue-600">10+</div>
                    <div class="mt-2 text-sm text-slate-500">Ani de experienta</div>
                </div>
                <div class="rounded-xl border border-slate-200 p-6 text-center">
                    <div class="text-3xl font-bold text-blue-600">500+</div>
                    <div class="mt-2 text-sm text-slate-500">Instalari finalizate</div>
                </div>
                <div class="rounded-xl border border-slate-200 p-6 text-center">
                    <div class="text-3xl font-bold text-blue-600">24-48h</div>
                    <div class="mt-2 text-sm text-slate-500">Timp de interventie</div>
                </div>
            </div>

            <h2 class="mt-12 text-xl font-semibold text-slate-900">Valorile noastre</h2>
            <ul class="mt-4 space-y-2 text-slate-600">
                <li>&bull; Transparenta in oferte si costuri, fara costuri ascunse.</li>
                <li>&bull; Echipamente de calitate, testate inainte de instalare.</li>
                <li>&bull; Suport tehnic rapid dupa finalizarea instalarii.</li>
                <li>&bull; Consultanta personalizata pentru fiecare tip de proprietate.</li>
            </ul>
        </section>
    </PublicLayout>
</template>
