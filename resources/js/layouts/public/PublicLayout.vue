<script setup lang="ts">
import { home } from '@/routes';
</script>

<template>
    <div class="min-h-screen bg-white text-neutral-900">
        <header class="mx-auto max-w-4xl border-b border-neutral-200 px-6 py-6">
            <a :href="home.url()" class="text-lg font-semibold">Howdle</a>
        </header>
        <main class="mx-auto max-w-4xl px-6 py-20">
            <slot />
        </main>
        <footer
            class="mx-auto max-w-4xl border-t border-neutral-200 px-6 py-6 text-sm text-neutral-600"
        >
            Built with Howdle CMS.
        </footer>
    </div>
</template>
