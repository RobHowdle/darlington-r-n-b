<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import HeroSection from '@/components/public/HeroSection.vue';
import RichTextSection from '@/components/public/RichTextSection.vue';
import type { CmsPage } from '@/types/page';

defineProps<{ page: CmsPage; preview: boolean }>();
</script>

<template>
    <Head :title="page.title">
        <meta name="description" :content="page.title" />
        <meta v-if="preview" name="robots" content="noindex,nofollow" />
    </Head>
    <div
        v-if="preview"
        class="mb-8 rounded bg-amber-100 px-4 py-3 text-sm text-amber-900"
    >
        Preview of the last saved version — only authenticated editors can see
        this page.
    </div>
    <template v-for="(section, index) in page.sections" :key="index">
        <HeroSection v-if="section.type === 'hero'" :data="section.data" />
        <RichTextSection
            v-else-if="section.type === 'rich_text'"
            :data="section.data"
        />
    </template>
</template>
