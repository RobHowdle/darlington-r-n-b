<script setup lang="ts">
import type { RichTextSection } from '@/types/page';

// The server stores only allowlisted HTML from RichText::clean.
defineProps<{ data: RichTextSection['data'] }>();
</script>

<template>
    <section
        class="space-y-4 leading-relaxed [&_a]:underline [&_ol]:list-decimal [&_ol]:pl-6 [&_ul]:list-disc [&_ul]:pl-6"
        v-html="data.body"
    />
</template>
