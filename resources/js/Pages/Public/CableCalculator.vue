<script setup>
import SeoHead from '@/Components/SeoHead.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    cablePricePerMeter: Number,
    connectorPrice: Number,
});

const cameraCount = ref(4);
const avgDistance = ref(20);
const slackPercent = ref(15);

const totalMeters = computed(() =>
    Math.ceil(cameraCount.value * avgDistance.value * (1 + slackPercent.value / 100))
);

const connectorsNeeded = computed(() => cameraCount.value * 2);

const cableCost = computed(() => totalMeters.value * props.cablePricePerMeter);
const connectorsCost = computed(() => connectorsNeeded.value * props.connectorPrice);
const totalCost = computed(() => cableCost.value + connectorsCost.value);

function money(value) {
    return Number(value).toLocaleString('ro-RO', { maximumFractionDigits: 0 });
}

const summary = computed(() =>
    `Calculator cablu: ${cameraCount.value} camere, ~${avgDistance.value}m/camera, `
    + `total ${totalMeters.value}m cablu. Estimare cablare: ${money(totalCost.value)} lei.`
);
</script>

<template>
    <SeoHead
        title="Calculator distanta si cablu CCTV"
        description="Calculeaza rapid cati metri de cablu UTP si cati conectori sunt necesari pentru instalarea sistemului tau de supraveghere video."
    />

    <PublicLayout>
        <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="font-display text-3xl font-bold text-slate-900">Calculator distanta si cablu</h1>
                <p class="mt-3 text-slate-500">
                    Estimeaza cantitatea de cablu necesara in functie de numarul de camere si distanta pana la NVR.
                </p>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-2">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Numar camere: {{ cameraCount }}
                        </label>
                        <input v-model.number="cameraCount" type="range" min="1" max="32" class="mt-2 w-full accent-blue-600" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Distanta medie pana la NVR (metri): {{ avgDistance }}
                        </label>
                        <input v-model.number="avgDistance" type="range" min="5" max="150" class="mt-2 w-full accent-blue-600" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Rezerva cablu pentru cotituri/inaltime: {{ slackPercent }}%
                        </label>
                        <input v-model.number="slackPercent" type="range" min="0" max="50" class="mt-2 w-full accent-blue-600" />
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-8">
                    <h2 class="text-lg font-semibold text-slate-900">Estimare cablare</h2>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Total cablu necesar</dt>
                            <dd class="font-medium text-slate-900">{{ totalMeters }} m</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Cost cablu</dt>
                            <dd class="font-medium text-slate-900">{{ money(cableCost) }} lei</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Conectori necesari</dt>
                            <dd class="font-medium text-slate-900">{{ connectorsNeeded }} buc</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Cost conectori</dt>
                            <dd class="font-medium text-slate-900">{{ money(connectorsCost) }} lei</dd>
                        </div>
                    </dl>
                    <div class="mt-4 flex justify-between border-t border-slate-200 pt-4">
                        <span class="text-base font-semibold text-slate-900">Total estimat cablare</span>
                        <span class="text-2xl font-bold text-blue-600">{{ money(totalCost) }} lei</span>
                    </div>
                    <p class="mt-2 text-xs text-slate-400">
                        Estimare orientativa pentru cablu UTP cat.6. Nu include manopera de tragere cablu.
                    </p>
                    <Link
                        :href="route('public.contact')"
                        :data="{ notes: summary }"
                        class="mt-6 block rounded-md bg-orange-500 px-4 py-2.5 text-center text-sm font-semibold text-white shadow-sm hover:bg-orange-400"
                    >
                        Cere oferta cu aceasta estimare
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
