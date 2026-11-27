import StarterKit from '@tiptap/starter-kit';
import { TableKit } from '@tiptap/extension-table';
import { Placeholder } from '@tiptap/extensions';
import { Gallery } from './gallery.js';
import { Html } from './html.js';
import { Image } from './image.js';
import { isAllowedHref } from './links.js';
import { Product } from './product.js';
import { Video } from './video.js';

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

    const json = editor.getJSON();
    const content = [...(json.content ?? [])];
    while (content.length > 0 && content.at(-1).type === 'paragraph' && !content.at(-1).content) {
        content.pop();
    }

    return content.length > 0 ? JSON.stringify({ ...json, content }) : '';
}

/** The text of the document, with a blank line between blocks like the plain text of the server. */
export function textOf(editor) {
    return editor.getText({ blockSeparator: '\n\n' });
}
