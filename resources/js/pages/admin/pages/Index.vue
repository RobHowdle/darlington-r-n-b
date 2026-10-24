<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import type { CmsPage } from '@/types/page';

defineProps<{ pages: Pick<CmsPage, 'id' | 'title' | 'slug' | 'status'>[] }>();
</script>

<template>
    <Head title="Pages" />
    <div class="p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Pages</h1>
            <Link
                href="/admin/pages/create"
                class="rounded bg-neutral-900 px-4 py-2 text-white"
                >New page</Link
            >
        </div>
        <p v-if="pages.length === 0">No pages yet.</p>
        <ul v-else class="divide-y rounded border">
            <li
                v-for="page in pages"
                :key="page.id"
                class="flex items-center justify-between gap-4 p-4"
            >
                <div>
                    <Link
                        :href="`/admin/pages/${page.id}/edit`"
                        class="font-medium underline"
                        >{{ page.title }}</Link
                    >
                    <p class="text-sm text-neutral-500">
                        /{{ page.slug }} · {{ page.status }}
                    </p>
                </div>
                <Link
                    :href="`/admin/pages/${page.id}/preview`"
                    class="text-sm underline"
                    >Preview</Link
                >
            </li>
        </ul>
    </div>
</template>
