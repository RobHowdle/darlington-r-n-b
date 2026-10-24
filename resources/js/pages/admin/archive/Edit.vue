<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
type ArchiveYear = { id: number; year: number; description: string | null; bands: string[]; is_published: boolean };
const props = defineProps<{ archiveYear?: ArchiveYear }>();
const form = useForm({
    year: props.archiveYear?.year ?? new Date().getFullYear(),
    description: props.archiveYear?.description ?? '',
    entries: (props.archiveYear?.bands ?? []).join('\n'),
    is_published: props.archiveYear?.is_published ?? false,
});
const saved = ref(false);
function save() {
    saved.value = false;
    form.transform(({ entries, ...data }) => ({
        ...data, bands: entries.split(/\r?\n/).map(entry => entry.trim()).filter(Boolean),
    }));
    const options = { onSuccess: () => { form.defaults(); saved.value = true; } };
    if (props.archiveYear) form.put(`/admin/archive/${props.archiveYear.id}`, options);
    else form.post('/admin/archive', options);
}
</script>

<template>
    <Head :title="archiveYear ? `Archive ${archiveYear.year}` : 'New archive year'" />
    <div class="max-w-3xl space-y-6 p-6">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold">{{ archiveYear ? 'Edit archive year' : 'New archive year' }}</h1>
            <Link href="/admin/archive" class="underline">All years</Link>
        </div>
        <form class="space-y-6" @submit.prevent="save">
            <div v-if="Object.keys(form.errors).length" role="alert" class="rounded border border-red-700 p-4 text-red-700">
                <p v-for="(error, key) in form.errors" :key="key">{{ error }}</p>
            </div>
            <label class="block">Year<input v-model.number="form.year" type="number" min="1900" max="2200" required class="block w-full rounded border p-2" /></label>
            <label class="block">Introduction<textarea v-model="form.description" rows="4" class="block w-full rounded border p-2" /></label>
            <label class="block">Gig entries — one per line<textarea v-model="form.entries" rows="18" class="block w-full rounded border p-2" /></label>
            <p class="text-sm text-neutral-500">Keep the date, band and any sold-out note together on each line. Lines appear in this order.</p>
            <label class="flex items-center gap-2"><input v-model="form.is_published" type="checkbox" />Published on the website</label>
            <div class="flex items-center gap-4">
                <button type="submit" :disabled="form.processing" class="rounded bg-neutral-900 px-4 py-2 text-white disabled:opacity-50">Save archive year</button>
                <span v-if="saved && !form.isDirty" role="status">Saved.</span>
            </div>
        </form>
    </div>
</template>
