import { Node } from '@tiptap/core';
import { insertOrUpdate } from './blocks.js';

/** HTML for what has no node of its own. The server sanitizes it; the editor only shows a card. */
export const Html = Node.create({
    name: 'html',
    group: 'block',
    atom: true,
    draggable: true,

    addOptions() {
        return { label: 'HTML' };
    },

    addAttributes() {
        return { html: { default: '', rendered: false } };
    },

    parseHTML() {
        return [{ tag: 'div[data-type="html"]', getAttrs: (element) => ({ html: element.getAttribute('data-html') ?? '' }) }];
    },

    renderHTML({ node }) {
        const html = node.attrs.html ?? '';

        return [
            'div',
            { 'data-type': 'html', 'data-html': html, class: 'odiseo-blog-editor__block' },
            ['span', { class: 'badge' }, this.options.label],
            ['code', { class: 'odiseo-blog-editor__block-detail' }, html.length > 120 ? `${html.slice(0, 120)}…` : html],
        ];
    },

    addCommands() {
        return {
            // Edits the selected one, or adds one.
            setHtml: (html) => ({ state, commands }) => insertOrUpdate(this.name, { html }, state, commands),
        };
    },
});
