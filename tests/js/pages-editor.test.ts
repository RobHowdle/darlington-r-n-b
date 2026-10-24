import { flushPromises, mount } from '@vue/test-utils';
import { router } from '@inertiajs/vue3';
import { Editor } from '@tiptap/vue-3';
import { afterEach, describe, expect, it, vi } from 'vitest';
import { nextTick } from 'vue';

vi.mock('@inertiajs/vue3', async (importOriginal) => {
    const actual = await importOriginal<typeof import('@inertiajs/vue3')>();
    const { defineComponent, h } = await import('vue');
    return {
        ...actual,
        Head: defineComponent({ render: () => null }),
        Link: defineComponent({
            props: ['href'],
            render() {
                return h('a', { href: this.href }, this.$slots.default?.());
            },
        }),
    };
});
import RichTextEditor from '../../resources/js/components/admin/RichTextEditor.vue';
import Edit from '../../resources/js/pages/admin/pages/Edit.vue';

const mounted: Array<{ unmount: () => void }> = [];
afterEach(() => {
    mounted.forEach((wrapper) => wrapper.unmount());
    mounted.length = 0;
    vi.restoreAllMocks();
});

async function richEditor(html: string) {
    const wrapper = mount(RichTextEditor, { props: { modelValue: html } });
    mounted.push(wrapper);
    await nextTick();
    const editor = wrapper.vm.editor as Editor;
    expect(editor).toBeTruthy();
    return { wrapper, editor };
}

describe('Pages rich text editor', () => {
    it('reopens saved formatting and syncs new saved content', async () => {
        const { wrapper, editor } = await richEditor(
            '<p>Hello <strong>there</strong> <em>friend</em>.</p>',
        );
        expect(editor.getHTML()).toContain('<strong>there</strong>');
        expect(editor.getHTML()).toContain('<em>friend</em>');
        await wrapper.setProps({
            modelValue: '<p>Reopened <strong>copy</strong>.</p>',
        });
        expect(editor.getHTML()).toBe('<p>Reopened <strong>copy</strong>.</p>');
    });

    it('formats paragraphs, bold, italic, both lists and links through the toolbar', async () => {
        const { wrapper, editor } = await richEditor('<p>Hello world</p>');
        editor.commands.setTextSelection({ from: 1, to: 6 });
        await wrapper.get('button[aria-label="Bold"]').trigger('click');
        expect(editor.getHTML()).toContain('<strong>Hello</strong>');
        editor.commands.setTextSelection({ from: 7, to: 12 });
        await wrapper.get('button[aria-label="Italic"]').trigger('click');
        expect(editor.getHTML()).toContain('<em>world</em>');
        expect(wrapper.emitted('update:modelValue')).toBeTruthy();

        editor.commands.setContent('<p>One</p>');
        await wrapper
            .get('button[aria-label="Bulleted list"]')
            .trigger('click');
        expect(editor.getHTML()).toContain('<ul><li><p>One</p></li></ul>');
        await wrapper.get('button[aria-label="Paragraph"]').trigger('click');
        expect(editor.getHTML()).toContain('<p>One</p>');
        await wrapper
            .get('button[aria-label="Numbered list"]')
            .trigger('click');
        expect(editor.getHTML()).toContain('<ol><li><p>One</p></li></ol>');

        editor.commands.setContent('<p>Link</p>');
        editor.commands.setTextSelection({ from: 1, to: 5 });
        vi.spyOn(window, 'prompt').mockReturnValue('/about');
        await wrapper.get('button[aria-label="Link"]').trigger('click');
        expect(editor.getHTML()).toContain('href="/about"');
        expect(editor.getHTML()).toContain('>Link</a>');
        editor.commands.setTextSelection({ from: 1, to: 5 });
        vi.spyOn(window, 'prompt').mockReturnValue('javascript:alert(1)');
        const alert = vi.spyOn(window, 'alert').mockImplementation(() => {});
        await wrapper.get('button[aria-label="Link"]').trigger('click');
        expect(alert).toHaveBeenCalled();
        expect(editor.getHTML()).not.toContain('javascript:');
    });
});

describe('saved preview', () => {
    const page = {
        id: 7,
        title: 'About us',
        slug: 'about-us',
        status: 'draft' as const,
        sections: [
            { type: 'rich_text' as const, data: { body: '<p>Saved</p>' } },
        ],
    };

    async function editPage() {
        const wrapper = mount(Edit, {
            props: { page },
            global: { stubs: { RichTextEditor: true } },
        });
        mounted.push(wrapper);
        await flushPromises();
        return wrapper;
    }

    it('keeps a clean editor available while preview opens in another tab', async () => {
        const wrapper = await editPage();
        const anchor = wrapper.get('a[href="/admin/pages/7/preview"]');
        expect(anchor.attributes('target')).toBe('_blank');
        expect(anchor.attributes('rel')).toContain('noopener');
        expect(anchor.text()).toContain('saved');
        const confirm = vi.spyOn(window, 'confirm').mockReturnValue(false);
        const click = new MouseEvent('click', {
            bubbles: true,
            cancelable: true,
        });
        anchor.element.dispatchEvent(click);
        expect(click.defaultPrevented).toBe(false);
        expect(confirm).not.toHaveBeenCalled();
    });

    it('warns before opening an old preview when edits are unsaved', async () => {
        const wrapper = await editPage();
        await wrapper.get('#page-title').setValue('Changed title');
        await nextTick();
        const anchor = wrapper.get('a[href="/admin/pages/7/preview"]');
        const confirm = vi.spyOn(window, 'confirm').mockReturnValue(false);
        const cancelled = new MouseEvent('click', {
            bubbles: true,
            cancelable: true,
        });
        anchor.element.dispatchEvent(cancelled);
        expect(cancelled.defaultPrevented).toBe(true);
        expect(confirm).toHaveBeenCalledWith(
            expect.stringContaining('unsaved changes'),
        );

        confirm.mockReturnValue(true);
        const accepted = new MouseEvent('click', {
            bubbles: true,
            cancelable: true,
        });
        anchor.element.dispatchEvent(accepted);
        expect(accepted.defaultPrevented).toBe(false);
    });

    it('clears the unsaved warning after a successful save', async () => {
        const put = vi
            .spyOn(router, 'put')
            .mockImplementation((_url, _data, options) => {
                options?.onSuccess?.({} as never);
            });
        const wrapper = await editPage();
        await wrapper.get('#page-title').setValue('Saved title');
        await wrapper.get('form').trigger('submit');
        await flushPromises();
        expect(put).toHaveBeenCalledWith(
            '/admin/pages/7',
            expect.objectContaining({ title: 'Saved title' }),
            expect.any(Object),
        );
        const confirm = vi.spyOn(window, 'confirm').mockReturnValue(false);
        wrapper
            .get('a[href="/admin/pages/7/preview"]')
            .element.dispatchEvent(
                new MouseEvent('click', { bubbles: true, cancelable: true }),
            );
        expect(confirm).not.toHaveBeenCalled();
    });
});
