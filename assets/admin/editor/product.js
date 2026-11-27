import { Node } from '@tiptap/core';
import { insertOrUpdate } from './blocks.js';

// Same rule as the product node of the server.
export const PRODUCT_CODE = /^[\w-]+$/;

/** A product of the catalog, saved by its code. The shop draws its card. */
export const Product = Node.create({
    name: 'product',
    group: 'block',
    atom: true,
    draggable: true,

    addOptions() {
        // describe(code) gives a promise of the product (code, name and image) to show in the card.
        return { label: 'Product', describe: null };
    },

    addAttributes() {
        return { code: { default: null, rendered: false } };
    },

    parseHTML() {
        return [{ tag: 'div[data-type="product"]', getAttrs: (element) => ({ code: element.getAttribute('data-code') }) }];
    },

    renderHTML({ node }) {
        return [
            'div',
            { 'data-type': 'product', 'data-code': node.attrs.code ?? '', class: 'odiseo-blog-editor__block' },
            ['span', { class: 'badge' }, this.options.label],
            ['span', { class: 'odiseo-blog-editor__block-detail' }, node.attrs.code ?? ''],
        ];
    },

    addNodeView() {
        return ({ node }) => {
            const dom = document.createElement('div');
            dom.className = 'odiseo-blog-editor__block';
            dom.dataset.type = 'product';
            dom.dataset.code = node.attrs.code ?? '';
            const badge = dom.appendChild(document.createElement('span'));
            badge.className = 'badge';
            badge.textContent = this.options.label;
            const detail = dom.appendChild(document.createElement('span'));
            detail.className = 'odiseo-blog-editor__block-detail';
            detail.textContent = node.attrs.code ?? '';

            this.options.describe?.(node.attrs.code)?.then((product) => {
                if (!product) {
                    return;
                }
                if (product.image) {
                    const image = document.createElement('img');
                    image.src = product.image;
                    image.alt = '';
                    image.className = 'avatar avatar-sm';
                    dom.insertBefore(image, detail);
                }
                detail.textContent = `${product.name} · ${product.code}`;
            });

            return { dom, update: (updated) => updated.type === node.type && updated.attrs.code === node.attrs.code };
        };
    },

    addCommands() {
        return {
            // Edits the selected one, or adds one.
            setProduct: (code) => ({ state, commands }) => insertOrUpdate(this.name, { code }, state, commands),
        };
    },
});
