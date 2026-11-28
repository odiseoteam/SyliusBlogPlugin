import { Node } from '@tiptap/core';
import { insertOrUpdate } from './blocks.js';
import { attrsFrom, missingAltBadge } from './image.js';

export const GALLERY_COLUMNS = [2, 3, 4];

/** A grid of uploaded images, each with its alt text, and a caption for all of them. */
export const Gallery = Node.create({
    name: 'gallery',
    group: 'block',
    atom: true,
    draggable: true,

    addOptions() {
        // url(path, 'thumbnail') gives the address of a square preview.
        return { label: 'Gallery', missingAlt: 'No alt text', url: () => null };
    },

    addAttributes() {
        return {
            images: { default: [], rendered: false },
            columns: { default: 3, rendered: false },
            caption: { default: '', rendered: false },
        };
    },

    parseHTML() {
        return [{ tag: 'figure[data-type="gallery"]', getAttrs: attrsFrom }];
    },

    renderHTML({ node }) {
        return ['figure', { 'data-type': 'gallery', 'data-attrs': JSON.stringify(node.attrs), class: 'odiseo-blog-editor__block' }, ['span', { class: 'badge' }, this.options.label]];
    },

    renderText({ node }) {
        return node.attrs.caption ?? '';
    },

    addNodeView() {
        return ({ node }) => {
            const images = node.attrs.images ?? [];
            const dom = document.createElement('figure');
            dom.className = 'odiseo-blog-editor__block odiseo-blog-editor__gallery';
            dom.dataset.type = 'gallery';

            const grid = dom.appendChild(document.createElement('div'));
            grid.className = 'odiseo-blog-editor__gallery-grid';
            grid.style.setProperty('--odiseo-blog-gallery-columns', String(node.attrs.columns ?? 3));
            images.forEach((image) => {
                const thumbnail = grid.appendChild(document.createElement('img'));
                thumbnail.src = this.options.url(image.path, 'thumbnail') ?? '';
                thumbnail.alt = image.alt ?? '';
            });
            const caption = dom.appendChild(document.createElement('figcaption'));
            caption.className = 'odiseo-blog-editor__block-detail';
            caption.textContent = node.attrs.caption || `${this.options.label} · ${images.length}`;
            if (images.some((image) => !image.alt)) {
                dom.appendChild(missingAltBadge(this.options.missingAlt));
            }

            return { dom, update: () => false };
        };
    },

    addCommands() {
        return {
            setGallery: (attrs) => ({ state, commands }) => insertOrUpdate(this.name, attrs, state, commands),
        };
    },
});
