<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import RichTextEditor from '@/components/admin/RichTextEditor.vue';
import type { CmsPage, Section } from '@/types/page';

const props = defineProps<{ page?: CmsPage }>();
const form = useForm({
    title: props.page?.title ?? '',
    slug: props.page?.slug ?? '',
    status: props.page?.status ?? 'draft',
    sections: (props.page?.sections ?? []) as Section[],
});
const sectionKeys = ref(form.sections.map((_, index) => index));
let nextSectionKey = sectionKeys.value.length;

function addSection(type: Section['type']) {
    sectionKeys.value.push(nextSectionKey++);
    form.sections.push(
        type === 'hero'
            ? { type, data: { heading: '', text: '' } }
            : { type, data: { body: '' } },
    );
}

function move(index: number, offset: number) {
    const next = index + offset;
    if (next < 0 || next >= form.sections.length) return;
    const [section] = form.sections.splice(index, 1);
    form.sections.splice(next, 0, section);
    const [key] = sectionKeys.value.splice(index, 1);
    sectionKeys.value.splice(next, 0, key);
}

function remove(index: number) {
    form.sections.splice(index, 1);
    sectionKeys.value.splice(index, 1);
}

function save() {
    const options = { onSuccess: () => form.defaults() };
    if (props.page) form.put(`/admin/pages/${props.page.id}`, options);
    else form.post('/admin/pages', options);
}

function confirmPreview(event: MouseEvent) {
    if (
        form.isDirty &&
        !window.confirm(
            'Preview shows the last saved version. Your unsaved changes will not appear. Open it anyway?',
        )
    ) {
        event.preventDefault();
    }
}

function fieldError(index: number, field: string): string | undefined {
    return form.errors[
        `sections.${index}.data.${field}` as keyof typeof form.errors
    ];
}
</script>

<template>
    <Head :title="page ? `Edit ${page.title}` : 'New page'" />
    <div class="max-w-3xl space-y-6 p-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">
                {{ page ? 'Edit page' : 'New page' }}
            </h1>
            <Link href="/admin/pages" class="underline">All pages</Link>
        </div>
        <form class="space-y-6" @submit.prevent="save">
            <div>
                <label for="page-title" class="block font-medium">Title</label>
                <input
                    id="page-title"
                    v-model="form.title"
                    class="w-full rounded border p-2"
                    required
                />
                <p v-if="form.errors.title" class="text-red-700">
                    {{ form.errors.title }}
                </p>
            </div>
            <div>
                <label for="page-slug" class="block font-medium">Slug</label>
                <input
                    id="page-slug"
                    v-model="form.slug"
                    class="w-full rounded border p-2"
                    placeholder="about-us"
                    required
                />
                <p class="text-sm text-neutral-500">
                    A lowercase URL segment, such as about-us.
                </p>
                <p v-if="form.errors.slug" class="text-red-700">
                    {{ form.errors.slug }}
                </p>
            </div>
            <div>
                <label for="page-status" class="block font-medium"
                    >Status</label
                >
                <select
                    id="page-status"
                    v-model="form.status"
                    class="w-full rounded border p-2"
                >
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
                <p v-if="form.errors.status" class="text-red-700">
                    {{ form.errors.status }}
                </p>
            </div>
            <div class="space-y-4">
                <h2 class="text-xl font-semibold">Sections</h2>
                <p v-if="form.errors.sections" class="text-red-700">
                    {{ form.errors.sections }}
                </p>
                <div
                    v-for="(section, index) in form.sections"
                    :key="sectionKeys[index]"
                    class="space-y-3 rounded border p-4"
                >
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="font-medium">
                            {{ section.type === 'hero' ? 'Hero' : 'Rich text' }}
                            #{{ index + 1 }}
                        </h3>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                :disabled="index === 0"
                                class="underline disabled:opacity-40"
                                @click="move(index, -1)"
                            >
                                Up
                            </button>
                            <button
                                type="button"
                                :disabled="index === form.sections.length - 1"
                                class="underline disabled:opacity-40"
                                @click="move(index, 1)"
                            >
                                Down
                            </button>
                            <button
                                type="button"
                                class="text-red-700 underline"
                                @click="remove(index)"
                            >
                                Remove
                            </button>
                        </div>
                    </div>
                    <template v-if="section.type === 'hero'">
                        <label class="block"
                            >Heading
                            <input
                                v-model="section.data.heading"
                                class="w-full rounded border p-2"
                            />
                        </label>
                        <p
                            v-if="fieldError(index, 'heading')"
                            class="text-red-700"
                        >
                            {{ fieldError(index, 'heading') }}
                        </p>
                        <label class="block"
                            >Text
                            <textarea
                                v-model="section.data.text"
                                rows="3"
                                class="w-full rounded border p-2"
                            />
                        </label>
                        <p
                            v-if="fieldError(index, 'text')"
                            class="text-red-700"
                        >
                            {{ fieldError(index, 'text') }}
                        </p>
                    </template>
                    <template v-else>
                        <p class="font-medium">Rich text</p>
                        <RichTextEditor v-model="section.data.body" />
                        <p
                            v-if="fieldError(index, 'body')"
                            class="text-red-700"
                        >
                            {{ fieldError(index, 'body') }}
                        </p>
                    </template>
                </div>
                <div class="flex gap-3">
                    <button
                        type="button"
                        class="rounded border px-3 py-2"
                        @click="addSection('hero')"
                    >
                        Add hero
                    </button>
                    <button
                        type="button"
                        class="rounded border px-3 py-2"
                        @click="addSection('rich_text')"
                    >
                        Add rich text
                    </button>
                </div>
            </div>
            <p v-if="page" class="text-sm text-neutral-600">
                Preview shows the last saved version. Save changes before
                previewing.
            </p>
            <div class="flex items-center gap-4">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded bg-neutral-900 px-4 py-2 text-white disabled:opacity-50"
                >
                    Save page
                </button>
                <a
                    v-if="page"
                    :href="`/admin/pages/${page.id}/preview`"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="underline"
                    @click="confirmPreview"
                    >Preview saved page in a new tab</a
                >
            </div>
        </form>
    </div>
</template>
