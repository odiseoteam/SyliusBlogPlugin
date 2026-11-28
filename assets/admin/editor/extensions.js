import StarterKit from '@tiptap/starter-kit';
import { TableKit } from '@tiptap/extension-table';
import { TextAlign } from '@tiptap/extension-text-align';
import { Placeholder } from '@tiptap/extensions';
import { Gallery } from './gallery.js';
import { Html } from './html.js';
import { Image } from './image.js';
import { isAllowedHref } from './links.js';
import { Product } from './product.js';
import { Video } from './video.js';

export const ALIGNMENTS = ['left', 'center', 'right'];

/** The nodes and marks the blog can draw. */
export function createExtensions({ placeholder = '', labels = {}, describeProduct = null, mediaUrl = () => null } = {}) {
    return [
        StarterKit.configure({
            heading: { levels: [2, 3, 4] },
            link: {
                openOnClick: false,
                defaultProtocol: 'https',
                HTMLAttributes: { rel: null, target: null },
                isAllowedUri: (url) => isAllowedHref(url),
            },
        }),
        TableKit.configure({ table: { resizable: false } }),
        Placeholder.configure({ placeholder }),
        TextAlign.configure({ types: ['heading', 'paragraph'], alignments: ALIGNMENTS }),
        Video.configure({ label: labels.video ?? 'Video' }),
        Html.configure({ label: labels.html ?? 'HTML' }),
        Product.configure({ label: labels.product ?? 'Product', describe: describeProduct }),
        Image.configure({ label: labels.image ?? 'Image', missingAlt: labels.missingAlt ?? 'No alt text', url: mediaUrl }),
        Gallery.configure({ label: labels.gallery ?? 'Gallery', missingAlt: labels.missingAlt ?? 'No alt text', url: mediaUrl }),
    ];
}

/** The JSON to save: nothing for an empty document, and without the empty paragraphs at its end. */
export function serialize(editor) {
    if (editor.isEmpty) {
        return '';
    }

    const json = withoutDefaultAlignment(editor.getJSON());
    const content = [...(json.content ?? [])];
    while (content.length > 0 && content.at(-1).type === 'paragraph' && !content.at(-1).content) {
        content.pop();
    }

    return content.length > 0 ? JSON.stringify({ ...json, content }) : '';
}

/** Leaves out the alignment of the blocks that keep the one of the theme. */
function withoutDefaultAlignment(node) {
    const { attrs, content, ...rest } = node;
    const result = { ...rest };
    if (attrs) {
        const { textAlign, ...others } = attrs;
        const kept = textAlign === null ? others : attrs;
        if (Object.keys(kept).length > 0) {
            result.attrs = kept;
        }
    }
    if (content) {
        result.content = content.map(withoutDefaultAlignment);
    }

    return result;
}

/** The text of the document, with a blank line between blocks like the plain text of the server. */
export function textOf(editor) {
    return editor.getText({ blockSeparator: '\n\n' });
}
