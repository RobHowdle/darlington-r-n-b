<script setup lang="ts">
import type { HeroSection } from '@/types/page';

defineProps<{ data: HeroSection['data'] }>();
</script>

<template>
    <section class="py-12">
        <h1 class="text-4xl font-semibold tracking-tight">
            {{ data.heading }}
        </h1>
        <p
            v-if="data.text"
            class="mt-5 text-lg leading-relaxed text-neutral-600"
        >
            {{ data.text }}
        </p>
    </section>
</template>
