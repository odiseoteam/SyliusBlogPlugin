import { Node } from '@tiptap/core';
import { insertOrUpdate } from './blocks.js';

// Same rule as the image node of the server.
export const IMAGE_PATH = /^[\w-]+(?:\/[\w-]+)*\.[A-Za-z0-9]+$/;

/** Share of the column of the text each size takes, and a narrower one when it floats, as on the server. */
export const IMAGE_SIZES = { small: 50, medium: 75, full: 100 };
const FLOATING_SIZES = { small: 33, medium: 50 };

/** Whether the image floats to a side, with the text around it. */
export function floats(attrs) {
    return attrs.size in FLOATING_SIZES && (attrs.align === 'left' || attrs.align === 'right');
}

/** Attributes as data, so copying and pasting a block keeps them. */
export function attrsFrom(element) {
    try {
        const attrs = JSON.parse(element.getAttribute('data-attrs') ?? '');

        return attrs && typeof attrs === 'object' ? attrs : false;
    } catch {
        return false;
    }
}

export function missingAltBadge(label) {
    const badge = document.createElement('span');
    badge.className = 'badge bg-warning-lt';
    badge.textContent = label;

    return badge;
}

/** An uploaded image, saved by its path in the storage, with its alt text, caption and size. */
export const Image = Node.create({
    name: 'image',
    group: 'block',
    atom: true,
    draggable: true,

    addOptions() {
        // url(path) gives the address of a preview.
        return { label: 'Image', missingAlt: 'No alt text', url: () => null };
    },

    addAttributes() {
        return {
            path: { default: null, rendered: false },
            alt: { default: '', rendered: false },
            caption: { default: '', rendered: false },
            size: { default: 'full', rendered: false },
            align: { default: 'center', rendered: false },
            width: { default: null, rendered: false },
            height: { default: null, rendered: false },
        };
    },

    parseHTML() {
        return [{ tag: 'figure[data-type="image"]', getAttrs: attrsFrom }];
    },

    renderHTML({ node }) {
        return ['figure', { 'data-type': 'image', 'data-attrs': JSON.stringify(node.attrs), class: 'odiseo-blog-editor__block' }, ['span', { class: 'badge' }, this.options.label]];
    },

    // The caption counts as text, as on the server.
    renderText({ node }) {
        return node.attrs.caption ?? '';
    },

    addNodeView() {
        return ({ node }) => {
            const dom = document.createElement('figure');
            dom.className = 'odiseo-blog-editor__block odiseo-blog-editor__image';
            dom.dataset.type = 'image';
            const floating = floats(node.attrs);
            dom.dataset.align = floating ? node.attrs.align : 'center';
            dom.style.setProperty('--odiseo-blog-image-width', `${(floating ? FLOATING_SIZES : IMAGE_SIZES)[node.attrs.size] ?? 100}%`);

            const url = this.options.url(node.attrs.path);
            if (url) {
                const image = dom.appendChild(document.createElement('img'));
                image.src = url;
                image.alt = node.attrs.alt ?? '';
            }
            const caption = dom.appendChild(document.createElement('figcaption'));
            caption.className = 'odiseo-blog-editor__block-detail';
            caption.textContent = node.attrs.caption || node.attrs.alt || this.options.label;
            if (!node.attrs.alt) {
                dom.appendChild(missingAltBadge(this.options.missingAlt));
            }

            return { dom, update: () => false };
        };
    },

    addCommands() {
        return {
            // Edits the selected one, or adds one.
            setImage: (attrs) => ({ state, commands }) => insertOrUpdate(this.name, attrs, state, commands),
        };
    },
});
