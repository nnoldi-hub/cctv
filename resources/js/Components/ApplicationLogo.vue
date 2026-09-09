<script setup>
const logoPath = '/branding/logo-cctv.png';
</script>

<template>
    <img v-bind="$attrs" :src="logoPath" alt="CCTV Security" class="h-auto w-auto object-contain" />
</template>
