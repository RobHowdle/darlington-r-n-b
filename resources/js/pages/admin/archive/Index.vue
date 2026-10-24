<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
type Year = { id: number; year: number; is_published: boolean; deleted_at: string | null };
defineProps<{ years: Year[] }>();
function remove(year: Year) {
    if (window.confirm(`Remove ${year.year} from the website? You can restore it from this list.`)) {
        router.delete(`/admin/archive/${year.id}`);
    }
}
</script>

<template>
    <Head title="Gig archive" />
    <div class="max-w-4xl space-y-6 p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold">Gig archive</h1>
            <div class="flex gap-4">
                <Link href="/admin" class="underline">Dashboard</Link>
                <Link href="/admin/archive/create" class="rounded border px-3 py-2">Add year</Link>
            </div>
        </div>
        <p v-if="!years.length">No archive years yet.</p>
        <div v-for="year in years" :key="year.id" class="flex flex-wrap items-center justify-between gap-4 rounded border p-4">
            <div><strong>{{ year.year }}</strong> — {{ year.deleted_at ? 'Removed' : year.is_published ? 'Published' : 'Draft' }}</div>
            <button v-if="year.deleted_at" type="button" class="underline" @click="router.post(`/admin/archive/${year.id}/restore`)">Restore as draft</button>
            <div v-else class="flex gap-4">
                <Link :href="`/admin/archive/${year.id}/edit`" class="underline">Edit</Link>
                <button type="button" class="text-red-700 underline" @click="remove(year)">Remove</button>
            </div>
        </div>
        <a href="/archive" target="_blank" rel="noopener noreferrer" class="inline-block underline">View public archive</a>
    </div>
</template>
