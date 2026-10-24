<script setup lang="ts">
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { watch } from 'vue';

const props = defineProps<{ modelValue: string }>();
const emit = defineEmits<{ 'update:modelValue': [value: string] }>();

const allowedLink = (url: string) =>
    /^(https?:\/\/|\/(?!\/))/i.test(url.trim());

const editor = useEditor({
    content: props.modelValue || '<p></p>',
    extensions: [
        StarterKit.configure({
            blockquote: false,
            code: false,
            codeBlock: false,
            heading: false,
            horizontalRule: false,
            strike: false,
            underline: false,
            link: {
                openOnClick: false,
                autolink: false,
                linkOnPaste: false,
                HTMLAttributes: { target: null, rel: null },
                isAllowedUri: allowedLink,
            },
        }),
    ],
    editorProps: {
        attributes: {
            'aria-label': 'Rich text body',
        },
    },
    onUpdate: ({ editor }) => emit('update:modelValue', editor.getHTML()),
});

defineExpose({ editor });

watch(
    () => props.modelValue,
    (value) => {
        if (editor.value && editor.value.getHTML() !== value) {
            editor.value.commands.setContent(value || '<p></p>', {
                emitUpdate: false,
            });
        }
    },
);

function setLink() {
    if (!editor.value) return;
    const current = editor.value.getAttributes('link').href as
        | string
        | undefined;
    const url = window.prompt('Link URL (https:// or /path)', current ?? '');
    if (url === null) return;
    if (url.trim() === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
        return;
    }
    if (!allowedLink(url)) {
        window.alert('Use an http(s) URL or a local path starting with /.');
        return;
    }
    editor.value
        .chain()
        .focus()
        .extendMarkRange('link')
        .setLink({ href: url.trim() })
        .run();
}
</script>

<template>
    <div class="overflow-hidden rounded border">
        <div
            v-if="editor"
            role="toolbar"
            aria-label="Rich text formatting"
            class="flex flex-wrap gap-1 border-b bg-neutral-50 p-2"
        >
            <button
                type="button"
                aria-label="Paragraph"
                :aria-pressed="editor.isActive('paragraph')"
                class="rounded px-2 py-1 hover:bg-neutral-200"
                :class="{ 'bg-neutral-200': editor.isActive('paragraph') }"
                @click="editor.chain().focus().setParagraph().run()"
            >
                Paragraph
            </button>
            <button
                type="button"
                aria-label="Bold"
                :aria-pressed="editor.isActive('bold')"
                class="rounded px-2 py-1 font-bold hover:bg-neutral-200"
                :class="{ 'bg-neutral-200': editor.isActive('bold') }"
                @click="editor.chain().focus().toggleBold().run()"
            >
                Bold
            </button>
            <button
                type="button"
                aria-label="Italic"
                :aria-pressed="editor.isActive('italic')"
                class="rounded px-2 py-1 italic hover:bg-neutral-200"
                :class="{ 'bg-neutral-200': editor.isActive('italic') }"
                @click="editor.chain().focus().toggleItalic().run()"
            >
                Italic
            </button>
            <button
                type="button"
                aria-label="Bulleted list"
                :aria-pressed="editor.isActive('bulletList')"
                class="rounded px-2 py-1 hover:bg-neutral-200"
                :class="{ 'bg-neutral-200': editor.isActive('bulletList') }"
                @click="editor.chain().focus().toggleBulletList().run()"
            >
                Bulleted list
            </button>
            <button
                type="button"
                aria-label="Numbered list"
                :aria-pressed="editor.isActive('orderedList')"
                class="rounded px-2 py-1 hover:bg-neutral-200"
                :class="{ 'bg-neutral-200': editor.isActive('orderedList') }"
                @click="editor.chain().focus().toggleOrderedList().run()"
            >
                Numbered list
            </button>
            <button
                type="button"
                aria-label="Link"
                :aria-pressed="editor.isActive('link')"
                class="rounded px-2 py-1 hover:bg-neutral-200"
                :class="{ 'bg-neutral-200': editor.isActive('link') }"
                @click="setLink"
            >
                Link
            </button>
        </div>
        <EditorContent
            :editor="editor"
            class="min-h-40 p-3 [&_.tiptap]:min-h-32 [&_.tiptap]:outline-none [&_.tiptap_a]:underline [&_.tiptap_ol]:list-decimal [&_.tiptap_ol]:pl-6 [&_.tiptap_ul]:list-disc [&_.tiptap_ul]:pl-6"
        />
    </div>
</template>
