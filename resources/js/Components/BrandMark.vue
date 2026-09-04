<script setup>
const props = defineProps({
    variant: { type: String, default: 'color' }, // color | white | black
    class: { type: String, default: 'h-8 w-8' },
});

const tone = {
    color: { body: '#2563EB', accent: '#F97316' },
    white: { body: '#FFFFFF', accent: '#FFFFFF' },
    black: { body: '#0B1320', accent: '#0B1320' },
}[props.variant] ?? { body: '#2563EB', accent: '#F97316' };
</script>

<template>
    <svg :class="$props.class" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
        <!-- mount -->
        <rect x="4" y="30" width="7" height="9" rx="2" :fill="tone.body" />
        <rect x="2" y="37" width="11" height="4" rx="2" :fill="tone.body" />
        <!-- camera body -->
        <rect x="8" y="15" width="27" height="17" rx="8.5" :fill="tone.body" />
        <!-- lens -->
        <circle cx="34.5" cy="23.5" r="9.5" :fill="tone.body" />
        <template v-if="variant === 'color'">
            <circle cx="34.5" cy="23.5" r="9.5" fill="none" :stroke="tone.accent" stroke-width="2.6" />
            <circle cx="34.5" cy="23.5" r="3.4" :fill="tone.accent" />
        </template>
    </svg>
</template>
