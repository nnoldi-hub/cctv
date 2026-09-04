<script setup>
import { computed } from 'vue';

const props = defineProps({
    data: { type: Array, required: true }, // [{ month, amount }]
    unit: { type: String, default: 'lei' },
});

const max = computed(() => Math.max(...props.data.map((d) => d.amount), 1));

function heightPercent(amount) {
    return Math.max((amount / max.value) * 100, amount > 0 ? 4 : 0);
}

function money(value) {
    return Number(value).toLocaleString('ro-RO', { maximumFractionDigits: 0 });
}
</script>

<template>
    <div class="flex h-48 gap-3">
        <div v-for="item in data" :key="item.month" class="flex flex-1 flex-col items-center">
            <span class="mb-1 text-xs font-medium text-slate-600">{{ item.amount > 0 ? money(item.amount) : '' }}</span>
            <div class="flex w-full flex-1 items-end">
                <div
                    class="w-full rounded-t bg-blue-600 transition-all"
                    :style="{ height: heightPercent(item.amount) + '%' }"
                    :title="`${item.month}: ${money(item.amount)} ${unit}`"
                />
            </div>
            <span class="mt-1 text-xs text-slate-400">{{ item.month }}</span>
        </div>
    </div>
</template>
