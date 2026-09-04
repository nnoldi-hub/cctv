<script setup>
import SeoHead from '@/Components/SeoHead.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    cameras: Array,
    nvrs: Array,
    storage: Array,
    laborPerCamera: Number,
});

const cameraId = ref(props.cameras[0]?.id ?? null);
const cameraQty = ref(4);
const nvrId = ref(props.nvrs[0]?.id ?? null);
const storageId = ref(props.storage[0]?.id ?? null);

const selectedCamera = computed(() => props.cameras.find((c) => c.id === cameraId.value));
const selectedNvr = computed(() => props.nvrs.find((n) => n.id === nvrId.value));
const selectedStorage = computed(() => props.storage.find((s) => s.id === storageId.value));

const camerasTotal = computed(() => (selectedCamera.value ? selectedCamera.value.unit_price * cameraQty.value : 0));
const laborTotal = computed(() => props.laborPerCamera * cameraQty.value);
const nvrPrice = computed(() => selectedNvr.value?.unit_price ?? 0);
const storagePrice = computed(() => selectedStorage.value?.unit_price ?? 0);

const estimatedTotal = computed(() =>
    camerasTotal.value + laborTotal.value + Number(nvrPrice.value) + Number(storagePrice.value)
);

function money(value) {
    return Number(value).toLocaleString('ro-RO', { maximumFractionDigits: 0 });
}

const summary = computed(() =>
    `Configurator: ${cameraQty.value}x ${selectedCamera.value?.name ?? ''}, `
    + `${selectedNvr.value?.name ?? ''}, ${selectedStorage.value?.name ?? ''}. `
    + `Estimare: ${money(estimatedTotal.value)} lei.`
);
</script>

<template>
    <SeoHead
        title="Configurator camere de supraveghere"
        description="Configureaza numarul de camere, tipul de NVR si capacitatea de stocare pentru a obtine o estimare de pret pentru sistemul tau CCTV."
    />

    <PublicLayout>
        <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="font-display text-3xl font-bold text-slate-900">Configurator sistem CCTV</h1>
                <p class="mt-3 text-slate-500">
                    Alege echipamentele dorite pentru a obtine o estimare rapida. Preturile sunt orientative.
                </p>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-2">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Tip camera</label>
                        <select v-model.number="cameraId" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option v-for="c in cameras" :key="c.id" :value="c.id">{{ c.name }} - {{ money(c.unit_price) }} lei</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            Numar camere: {{ cameraQty }}
                        </label>
                        <input v-model.number="cameraQty" type="range" min="1" max="32" class="mt-2 w-full accent-blue-600" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">NVR / DVR</label>
                        <select v-model.number="nvrId" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option v-for="n in nvrs" :key="n.id" :value="n.id">{{ n.name }} - {{ money(n.unit_price) }} lei</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Capacitate stocare</label>
                        <select v-model.number="storageId" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option v-for="s in storage" :key="s.id" :value="s.id">{{ s.name }} - {{ money(s.unit_price) }} lei</option>
                        </select>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-8">
                    <h2 class="text-lg font-semibold text-slate-900">Estimare</h2>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Camere ({{ cameraQty }}x)</dt>
                            <dd class="font-medium text-slate-900">{{ money(camerasTotal) }} lei</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">NVR / DVR</dt>
                            <dd class="font-medium text-slate-900">{{ money(nvrPrice) }} lei</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Stocare</dt>
                            <dd class="font-medium text-slate-900">{{ money(storagePrice) }} lei</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Manopera instalare</dt>
                            <dd class="font-medium text-slate-900">{{ money(laborTotal) }} lei</dd>
                        </div>
                    </dl>
                    <div class="mt-4 flex justify-between border-t border-slate-200 pt-4">
                        <span class="text-base font-semibold text-slate-900">Total estimat</span>
                        <span class="text-2xl font-bold text-blue-600">{{ money(estimatedTotal) }} lei</span>
                    </div>
                    <p class="mt-2 text-xs text-slate-400">
                        Nu include cablarea (vezi calculatorul de cablu) si poate varia in functie de conditiile de instalare.
                    </p>
                    <Link
                        :href="route('public.contact')"
                        :data="{ notes: summary }"
                        class="mt-6 block rounded-md bg-orange-500 px-4 py-2.5 text-center text-sm font-semibold text-white shadow-sm hover:bg-orange-400"
                    >
                        Cere oferta pentru aceasta configuratie
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
