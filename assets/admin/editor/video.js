import { Node } from '@tiptap/core';
import { insertOrUpdate } from './blocks.js';

// Same addresses as the video node of the server.
const PATTERNS = [
    [/^https:\/\/(?:www\.)?youtube(?:-nocookie)?\.com\/(?:watch\?(?:[^#]*&)?v=|embed\/)([\w-]{11})(?:[&?#]|$)/, 'https://www.youtube-nocookie.com/embed/'],
    [/^https:\/\/youtu\.be\/([\w-]{11})(?:[?#]|$)/, 'https://www.youtube-nocookie.com/embed/'],
    [/^https:\/\/(?:www\.)?vimeo\.com\/(\d+)(?:[/?#]|$)/, 'https://player.vimeo.com/video/'],
    [/^https:\/\/player\.vimeo\.com\/video\/(\d+)(?:[/?#]|$)/, 'https://player.vimeo.com/video/'],
];

/** The player address of a YouTube or Vimeo video, or null for anything else. */
export function embedUrl(url) {
    for (const [pattern, base] of PATTERNS) {
        const match = typeof url === 'string' ? url.match(pattern) : null;
        if (match) {
            return base + match[1];
        }
    }

    return null;
}

export const Video = Node.create({
    name: 'video',
    group: 'block',
    atom: true,
    draggable: true,

    addOptions() {
        return { label: 'Video' };
    },

    addAttributes() {
        return { src: { default: null, rendered: false } };
    },

    parseHTML() {
        return [
            { tag: 'div[data-type="video"]', getAttrs: (element) => ({ src: element.getAttribute('data-src') }) },
            // A pasted player of YouTube or Vimeo.
            { tag: 'iframe[src]', getAttrs: (element) => (embedUrl(element.getAttribute('src')) ? { src: element.getAttribute('src') } : false) },
        ];
    },

    renderHTML({ node }) {
        return [
            'div',
            { 'data-type': 'video', 'data-src': node.attrs.src ?? '', class: 'odiseo-blog-editor__block' },
            ['span', { class: 'badge' }, this.options.label],
            ['span', { class: 'odiseo-blog-editor__block-detail' }, node.attrs.src ?? ''],
        ];
    },

    addCommands() {
        return {
            // Edits the selected one, or adds one.
            setVideo: (src) => ({ state, commands }) => insertOrUpdate(this.name, { src }, state, commands),
        };
    },
});
