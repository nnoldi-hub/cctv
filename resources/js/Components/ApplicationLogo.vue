<script setup>
const logoPath = '/branding/mark-color-512.png';
</script>

<template>
    <img v-bind="$attrs" :src="logoPath" alt="CCTV Security" class="h-auto w-auto object-contain" />
</template>
