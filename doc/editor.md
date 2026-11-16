[Docs index](README.md)

# Editor

The content of an article is written with a [Tiptap](https://tiptap.dev) editor. It saves the Tiptap JSON
that the server validates and draws (see [content](content.md)). Install its assets as shown in
[installation](installation.md#5-install-the-editor).

## The toolbar

| Tool | Does |
| --- | --- |
| Text style | Paragraph, or a heading of level 2, 3 or 4. The title of the article is the level 1. |
| Bold, italic, underline, strikethrough, code | Format the selected text. |
| Link | Opens a dialog with the address, opening it in a new tab, and `nofollow` or `sponsored`. With no text selected, the address is added as the text of the link. `Ctrl+K` opens it too. |
| Clear formatting | Removes the formats and turns the blocks back into paragraphs. |
| Lists, quote, code block, divider | Blocks. |
| Table | Inserts a table with a header row, and adds or deletes rows and columns. |
| Video | A YouTube or Vimeo address. |
| HTML block | HTML for what has no tool of its own. The server sanitizes it when drawing it. |
| Undo, redo | |

Videos and HTML blocks show as cards. A double click, or the same tool with the card selected, edits them.
Below the editor are the words and the reading time, counted as the server counts them.

Pasted text keeps what the blog can draw and drops the rest: images, colors, a level 1 heading (it becomes a
paragraph), links to anything that is not a web page, an email, a phone, a path of the store or an anchor.
A pasted YouTube or Vimeo player becomes a video.

## Without the editor

Without JavaScript, or until the assets are built, the content is a field for the Tiptap JSON. The editor also
leaves that field when it cannot open the content: when it is not JSON, or when it has nodes the editor does
not know (a node of your own without its Tiptap extension, for instance). Opening it would lose them.

## In your own forms

`Odiseo\SyliusBlogPlugin\Form\Type\ContentEditorType` is the editor as a form field. It saves a string with the
JSON, or an empty one for an empty document; validate it with `Odiseo\SyliusBlogPlugin\Validator\Constraints\ValidContent`.

```php
$builder->add('content', ContentEditorType::class, [
    'placeholder' => 'app.ui.write_the_page', // translation key; null for none
]);
```

## Your own node

A node of your own (see [content](content.md#your-own-node)) needs a Tiptap extension to be written in the
editor. Add it when the editor asks for its extensions, with the `odiseo-blog-editor:options` event:

```js
// assets/admin/entrypoint.js
import { Node } from '@tiptap/core';

const Callout = Node.create({
    name: 'callout',
    group: 'block',
    content: 'paragraph+',
    addAttributes: () => ({ tone: { default: 'info' } }),
    parseHTML: () => [{ tag: 'aside[data-callout]' }],
    renderHTML: ({ HTMLAttributes }) => ['aside', { 'data-callout': '', ...HTMLAttributes }, 0],
});

document.addEventListener('odiseo-blog-editor:options', (event) => {
    event.detail.extensions.push(Callout);
});
```

Once the editor starts, `odiseo-blog-editor:ready` gives its Tiptap instance in `event.detail.editor`, to add
your own buttons. The controller is `odiseo-blog-editor`.
