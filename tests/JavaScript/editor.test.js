import { Editor } from '@tiptap/core';
import { afterEach, describe, expect, it } from 'vitest';
import { createExtensions, serialize, textOf } from '../../assets/admin/editor/extensions.js';
import { countWords } from '../../assets/admin/editor/words.js';

let editor;

function createEditor(content = null) {
    editor = new Editor({ element: document.createElement('div'), extensions: createExtensions(), content });

    return editor;
}

/**
 * What the editor saves for each kind of content. PHPUnit checks that the server accepts and draws
 * these files and counts the same words, so a change in the editor that the server would reject fails.
 */
async function expectFixture(name) {
    const fixture = { content: serialize(editor), words: countWords(textOf(editor)) };

    await expect(`${JSON.stringify(fixture, null, 4)}\n`).toMatchFileSnapshot(`../Fixtures/Content/Editor/${name}.json`);
}

afterEach(() => editor?.destroy());

describe('the content the editor saves', () => {
    it('holds text and its formats', async () => {
        createEditor(
            '<h2>Brewing coffee</h2>'
            + '<p><strong>Bold</strong>, <em>italic</em>, <u>underlined</u>, <s>struck</s>, <code>code</code> and '
            + '<a href="https://example.com" target="_blank" rel="nofollow sponsored">a link</a>.<br>Next line</p>'
            + '<h1>A pasted title</h1>'
            + '<h4>Notes</h4>',
        );

        await expectFixture('text');
    });

    it('holds blocks', async () => {
        createEditor(
            '<ul><li><p>Grind</p><ul><li><p>Fine</p></li></ul></li><li><p>Pour</p></li></ul>'
            + '<ol start="3"><li><p>Wait</p></li></ol>'
            + '<blockquote><p>Coffee first.</p></blockquote>'
            + '<pre><code class="language-php">echo 1;</code></pre>'
            + '<hr>',
        );

        await expectFixture('blocks');
    });

    it('holds tables', async () => {
        createEditor();
        editor.chain().insertTable({ rows: 2, cols: 2, withHeaderRow: true }).insertContent('Origin').run();

        await expectFixture('table');
    });

    it('holds videos and HTML', async () => {
        createEditor('<p>Watch it:</p><iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ"></iframe><p>And this one:</p>');
        editor.chain().focus('end').setVideo('https://vimeo.com/76979871').setHtml('<p>Hi <iframe src="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ"></iframe></p>').run();

        await expectFixture('embeds');
    });
});

describe('videos and HTML', () => {
    it('are edited when selected', () => {
        createEditor('<div data-type="video" data-src="https://vimeo.com/1"></div><div data-type="html" data-html="<b>A</b>"></div>');
        editor.chain().setNodeSelection(0).setVideo('https://vimeo.com/2').run();
        editor.chain().setNodeSelection(editor.state.doc.child(0).nodeSize).setHtml('<b>B</b>').run();

        expect(JSON.parse(serialize(editor)).content).toEqual([
            { type: 'video', attrs: { src: 'https://vimeo.com/2' } },
            { type: 'html', attrs: { html: '<b>B</b>' } },
        ]);
    });

    it('are added after the selected block', () => {
        createEditor('<div data-type="html" data-html="<b>A</b>"></div>');
        editor.chain().setNodeSelection(0).setVideo('https://vimeo.com/2').run();

        expect(JSON.parse(serialize(editor)).content.map((node) => node.type)).toEqual(['html', 'video']);
    });
});

describe('what is saved', () => {
    it('is nothing for an empty document', () => {
        createEditor('<p></p>');

        expect(serialize(editor)).toBe('');
    });

    it('leaves out the empty paragraphs at the end', () => {
        createEditor('<p>Hi</p><p></p><p></p>');

        expect(JSON.parse(serialize(editor)).content).toEqual([{ type: 'paragraph', content: [{ type: 'text', text: 'Hi' }] }]);
    });

    it('does not keep what the blog cannot draw', () => {
        createEditor('<h1>Title</h1><p><img src="https://example.com/a.png"><span style="color: red">Red</span></p><iframe src="https://example.com"></iframe>');

        expect(JSON.parse(serialize(editor)).content).toEqual([
            { type: 'paragraph', content: [{ type: 'text', text: 'Title' }] },
            { type: 'paragraph', content: [{ type: 'text', text: 'Red' }] },
        ]);
    });

    it('does not link to unsafe addresses', () => {
        createEditor('<p><a href="javascript:alert(1)">Click</a></p>');

        expect(JSON.parse(serialize(editor)).content[0].content[0].marks).toBeUndefined();
    });
});
