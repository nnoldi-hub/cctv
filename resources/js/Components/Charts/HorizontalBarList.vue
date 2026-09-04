<script setup>
import { computed } from 'vue';

const props = defineProps({
    data: { type: Object, required: true }, // { label: count }
    labels: { type: Object, default: () => ({}) }, // { key: displayLabel }
});

const entries = computed(() => Object.entries(props.data));
const max = computed(() => Math.max(...Object.values(props.data), 1));

function widthPercent(value) {
    return Math.max((value / max.value) * 100, value > 0 ? 4 : 0);
}

function displayLabel(key) {
    return props.labels[key] ?? key;
}
</script>

<template>
    <div class="space-y-3">
        <div v-for="[key, value] in entries" :key="key" class="flex items-center gap-3">
            <span class="w-28 flex-shrink-0 text-xs text-slate-500">{{ displayLabel(key) }}</span>
            <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-blue-600" :style="{ width: widthPercent(value) + '%' }" />
            </div>
            <span class="w-8 flex-shrink-0 text-right text-xs font-medium text-slate-900">{{ value }}</span>
        </div>
        <p v-if="!entries.length" class="text-sm text-slate-400">Fara date.</p>
    </div>
</template>
