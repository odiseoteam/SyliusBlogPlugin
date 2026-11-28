import { Application } from '@hotwired/stimulus';
import { Node } from '@tiptap/core';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import EditorController from '../../assets/admin/controllers/editor_controller.js';

const STATS = 'Words: %words% · Reading time: %minutes% min';

let application;
let modal;

// The markup of the form theme, reduced to what the controller uses.
function mount(value) {
    document.body.innerHTML = `
        <div data-controller="odiseo-blog-editor"
             data-odiseo-blog-editor-words-per-minute-value="2"
             data-odiseo-blog-editor-stats-value="${STATS}"
             data-odiseo-blog-editor-labels-value='{"invalidLink":"Bad link","invalidVideo":"Bad video","invalidProduct":"Bad product","invalidImage":"Bad image","invalidGallery":"No images","missingAlt":"No alt","uploading":"Uploading","uploadFailed":"Upload failed","remove":"Remove","alt":"Alt"}'
             data-odiseo-blog-editor-product-search-url-value="/admin/blog/products/search"
             data-odiseo-blog-editor-media-upload-url-value="/admin/blog/media"
             data-odiseo-blog-editor-media-token-value="token"
             data-odiseo-blog-editor-media-urls-value='{"placeholder":"__PATH__","image":"/media/cache/resolve/odiseo_blog_content_640/__PATH__","thumbnail":"/media/cache/resolve/odiseo_blog_admin_thumbnail/__PATH__"}'>
            <div data-odiseo-blog-editor-target="toolbar" hidden>
                <button type="button" data-action="odiseo-blog-editor#run" data-odiseo-blog-editor-command-param="bold">Bold</button>
                <button type="button" data-action="odiseo-blog-editor#run" data-odiseo-blog-editor-command-param="heading" data-odiseo-blog-editor-level-param="3">H3</button>
                <button type="button" data-action="odiseo-blog-editor#run" data-odiseo-blog-editor-command-param="undo">Undo</button>
                <button type="button" data-action="odiseo-blog-editor#open" data-odiseo-blog-editor-dialog-param="link">Link</button>
                <button type="button" data-action="odiseo-blog-editor#open" data-odiseo-blog-editor-dialog-param="video">Video</button>
                <button type="button" data-action="odiseo-blog-editor#open" data-odiseo-blog-editor-dialog-param="product">Product</button>
                <button type="button" data-action="odiseo-blog-editor#align" data-odiseo-blog-editor-align-param="left">Left</button>
                <button type="button" data-action="odiseo-blog-editor#align" data-odiseo-blog-editor-align-param="center">Center</button>
                <button type="button" data-action="odiseo-blog-editor#align" data-odiseo-blog-editor-align-param="right">Right</button>
                <button type="button" data-action="odiseo-blog-editor#open" data-odiseo-blog-editor-dialog-param="image">Image</button>
                <button type="button" data-action="odiseo-blog-editor#open" data-odiseo-blog-editor-dialog-param="gallery">Gallery</button>
            </div>
            <div data-odiseo-blog-editor-target="surface" hidden></div>
            <textarea data-odiseo-blog-editor-target="input"></textarea>
            <div data-odiseo-blog-editor-target="fallback" hidden></div>
            <div data-odiseo-blog-editor-target="notice" hidden></div>
            <div data-odiseo-blog-editor-target="stats" hidden></div>
            <div data-odiseo-blog-editor-target="help">Tiptap JSON</div>
            <div data-odiseo-blog-editor-target="modal" data-action="keydown->odiseo-blog-editor#submitOnEnter">
                <h2 data-odiseo-blog-editor-target="modalTitle"></h2>
                <div data-odiseo-blog-editor-target="pane" data-dialog="link" data-title="Link">
                    <input data-odiseo-blog-editor-target="href">
                    <input type="checkbox" data-odiseo-blog-editor-target="newTab">
                    <input type="checkbox" data-odiseo-blog-editor-target="nofollow">
                    <input type="checkbox" data-odiseo-blog-editor-target="sponsored">
                </div>
                <div data-odiseo-blog-editor-target="pane" data-dialog="video" data-title="Video">
                    <input data-odiseo-blog-editor-target="videoSrc">
                </div>
                <div data-odiseo-blog-editor-target="pane" data-dialog="html" data-title="HTML">
                    <textarea data-odiseo-blog-editor-target="html"></textarea>
                </div>
                <div data-odiseo-blog-editor-target="pane" data-dialog="product" data-title="Product">
                    <select data-odiseo-blog-editor-target="product"></select>
                </div>
                <div data-odiseo-blog-editor-target="pane" data-dialog="image" data-title="Image">
                    <img data-odiseo-blog-editor-target="imagePreview" hidden>
                    <input type="file" data-odiseo-blog-editor-target="imageFile" data-action="change->odiseo-blog-editor#uploadImage">
                    <input data-odiseo-blog-editor-target="imageAlt">
                    <input data-odiseo-blog-editor-target="imageCaption">
                    <select data-odiseo-blog-editor-target="imageSize"><option value="small">S</option><option value="medium">M</option><option value="full">F</option></select>
                </div>
                <div data-odiseo-blog-editor-target="pane" data-dialog="gallery" data-title="Gallery">
                    <ul data-odiseo-blog-editor-target="galleryList"></ul>
                    <input type="file" multiple data-odiseo-blog-editor-target="galleryFiles" data-action="change->odiseo-blog-editor#uploadGallery">
                    <input data-odiseo-blog-editor-target="galleryCaption">
                    <select data-odiseo-blog-editor-target="galleryColumns"><option value="2">2</option><option value="3">3</option><option value="4">4</option></select>
                </div>
                <div data-odiseo-blog-editor-target="modalError"></div>
                <button type="button" data-odiseo-blog-editor-target="removeLink" data-action="odiseo-blog-editor#removeLink"></button>
                <button type="button" id="apply" data-action="odiseo-blog-editor#apply"></button>
            </div>
        </div>`;
    document.querySelector('textarea').value = value;
}

async function start(value = '') {
    mount(value);
    application = Application.start();
    application.register('odiseo-blog-editor', EditorController);
    await new Promise((resolve) => setTimeout(resolve));

    return application.getControllerForElementAndIdentifier(document.querySelector('[data-controller]'), 'odiseo-blog-editor');
}

const $ = (target) => document.querySelector(`[data-odiseo-blog-editor-target="${target}"]`);
const click = (selector) => document.querySelector(selector).click();
const saved = () => JSON.parse($('input').value);

const MUG = { code: 'MUG', name: 'Coffee mug', image: '/media/mug.jpg' };

beforeEach(() => {
    modal = { show: vi.fn(), hide: vi.fn() };
    window.bootstrap = { Modal: { getOrCreateInstance: () => modal } };
    window.fetch = vi.fn(async () => ({ ok: true, json: async () => [MUG] }));
});

afterEach(() => {
    application?.stop();
    document.body.innerHTML = '';
});

describe('the editor', () => {
    it('replaces the JSON field and counts the words', async () => {
        await start('{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Three little words"}]}]}');

        expect($('input').hidden).toBe(true);
        expect($('help').hidden).toBe(true);
        expect($('surface').hidden).toBe(false);
        expect($('toolbar').hidden).toBe(false);
        expect($('surface').textContent).toBe('Three little words');
        expect($('stats').textContent).toBe('Words: 3 · Reading time: 2 min');
    });

    it('saves what is written', async () => {
        const controller = await start();
        controller.editor.commands.insertContent('Hello world');

        expect(saved().content).toEqual([{ type: 'paragraph', content: [{ type: 'text', text: 'Hello world' }] }]);
        expect($('stats').textContent).toBe('Words: 2 · Reading time: 1 min');

        controller.editor.commands.clearContent(true);
        expect($('input').value).toBe('');
    });

    it('runs the commands of the toolbar and shows which are active', async () => {
        const controller = await start('{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Hi"}]}]}');
        controller.editor.commands.selectAll();

        click('[data-odiseo-blog-editor-command-param="bold"]');
        click('[data-odiseo-blog-editor-command-param="heading"]');

        expect(saved().content).toEqual([{ type: 'heading', attrs: { level: 3 }, content: [{ type: 'text', text: 'Hi', marks: [{ type: 'bold' }] }] }]);
        expect(document.querySelector('[data-odiseo-blog-editor-command-param="bold"]').getAttribute('aria-pressed')).toBe('true');
        expect(document.querySelector('[data-odiseo-blog-editor-command-param="undo"]').disabled).toBe(false);
    });

    it('links the selected text from the dialog', async () => {
        const controller = await start('{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Hi"}]}]}');
        controller.editor.commands.selectAll();
        click('[data-odiseo-blog-editor-dialog-param="link"]');
        expect(modal.show).toHaveBeenCalled();
        expect($('modalTitle').textContent).toBe('Link');

        $('href').value = 'javascript:alert(1)';
        click('#apply');
        expect($('modalError').textContent).toBe('Bad link');
        expect(modal.hide).not.toHaveBeenCalled();

        $('href').value = 'https://example.com';
        $('newTab').checked = true;
        $('sponsored').checked = true;
        $('href').dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true }));

        expect(modal.hide).toHaveBeenCalled();
        expect(saved().content[0].content[0].marks[0].attrs).toMatchObject({ href: 'https://example.com', target: '_blank', rel: 'sponsored' });
    });

    it('adds YouTube and Vimeo videos only', async () => {
        await start();
        click('[data-odiseo-blog-editor-dialog-param="video"]');

        $('videoSrc').value = 'https://example.com/video';
        click('#apply');
        expect($('modalError').textContent).toBe('Bad video');

        $('videoSrc').value = 'https://youtu.be/dQw4w9WgXcQ';
        click('#apply');
        expect(saved().content).toEqual([{ type: 'video', attrs: { src: 'https://youtu.be/dQw4w9WgXcQ' } }]);
    });
});

const alignButton = (align) => document.querySelector(`[data-odiseo-blog-editor-align-param="${align}"]`);

describe('alignment', () => {
    it('aligns the text and shows the alignment of the cursor', async () => {
        const controller = await start('{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Hi"}]}]}');
        controller.editor.commands.focus('end');
        expect(alignButton('left').getAttribute('aria-pressed')).toBe('true');

        alignButton('center').click();

        expect(saved().content).toEqual([{ type: 'paragraph', attrs: { textAlign: 'center' }, content: [{ type: 'text', text: 'Hi' }] }]);
        expect(alignButton('center').getAttribute('aria-pressed')).toBe('true');
        expect(alignButton('left').getAttribute('aria-pressed')).toBe('false');
    });

    it('aligns the selected image when it is not of the full width', async () => {
        const controller = await start('{"type":"doc","content":[{"type":"image","attrs":{"path":"ab/cd/a.jpg","size":"small"}},{"type":"image","attrs":{"path":"ab/cd/b.jpg","size":"full"}}]}');
        controller.editor.commands.setNodeSelection(0);

        alignButton('right').click();
        expect(saved().content[0].attrs.align).toBe('right');
        expect($('surface').querySelector('figure').dataset.align).toBe('right');

        controller.editor.commands.setNodeSelection(1);
        expect(alignButton('right').disabled).toBe(true);
        alignButton('right').click();
        expect(saved().content[1].attrs.align).toBe('center');
    });

    it('leaves out the alignment of the theme', async () => {
        const controller = await start();
        controller.editor.commands.insertContent('<h2>Title</h2><p>Text</p>');

        expect(saved().content.map((node) => node.attrs ?? null)).toEqual([{ level: 2 }, null]);
    });
});

describe('products', () => {
    it('are chosen in the dialog', async () => {
        const controller = await start();
        click('[data-odiseo-blog-editor-dialog-param="product"]');

        click('#apply');
        expect($('modalError').textContent).toBe('Bad product');

        controller.productSelect.addOption(MUG);
        controller.productSelect.setValue('MUG');
        click('#apply');

        expect(saved().content).toEqual([{ type: 'product', attrs: { code: 'MUG' } }]);
        await vi.waitFor(() => expect($('surface').textContent).toContain('Coffee mug'));
    });

    it('show their name and image in the editor', async () => {
        await start('{"type":"doc","content":[{"type":"product","attrs":{"code":"MUG"}}]}');
        await vi.waitFor(() => expect($('surface').textContent).toContain('Coffee mug · MUG'));

        expect($('surface').querySelector('img').getAttribute('src')).toBe('/media/mug.jpg');
        expect(window.fetch).toHaveBeenCalledTimes(1);
    });

    it('are edited from their card', async () => {
        const controller = await start('{"type":"doc","content":[{"type":"product","attrs":{"code":"MUG"}}]}');
        controller.editor.commands.setNodeSelection(0);
        click('[data-odiseo-blog-editor-dialog-param="product"]');

        await vi.waitFor(() => expect(controller.productSelect.getValue()).toBe('MUG'));
    });
});

/** Puts files in a file field and lets the controller see the change. */
function choose(target, files) {
    Object.defineProperty($(target), 'files', { value: files, configurable: true });
    $(target).dispatchEvent(new Event('change'));
}

const photo = (name) => new File(['x'], name, { type: 'image/jpeg' });

function uploads(...stored) {
    const answers = [...stored];
    window.fetch = vi.fn(async (url, options) => {
        if (options?.method !== 'POST') {
            return { ok: true, json: async () => [] };
        }
        const answer = answers.shift();

        return answer instanceof Error
            ? { ok: false, json: async () => ({ error: answer.message }) }
            : { ok: true, json: async () => answer };
    });
}

describe('images', () => {
    it('are uploaded, described and sized in the dialog', async () => {
        uploads({ path: 'ab/cd/mugs.jpg', width: 1600, height: 1200 });
        await start();
        click('[data-odiseo-blog-editor-dialog-param="image"]');

        click('#apply');
        expect($('modalError').textContent).toBe('Bad image');

        choose('imageFile', [photo('mugs.jpg')]);
        await vi.waitFor(() => expect($('imagePreview').hidden).toBe(false));
        expect($('imagePreview').getAttribute('src')).toBe('/media/cache/resolve/odiseo_blog_content_640/ab/cd/mugs.jpg');
        const [, request] = window.fetch.mock.calls.find(([url]) => url === '/admin/blog/media');
        expect(request.body.get('_token')).toBe('token');

        $('imageAlt').value = ' Two mugs ';
        $('imageCaption').value = 'Fresh';
        $('imageSize').value = 'medium';
        click('#apply');

        expect(saved().content).toEqual([{ type: 'image', attrs: { path: 'ab/cd/mugs.jpg', alt: 'Two mugs', caption: 'Fresh', size: 'medium', align: 'center', width: 1600, height: 1200 } }]);
        expect($('stats').textContent).toBe('Words: 1 · Reading time: 1 min');
    });

    it('tell why the upload failed', async () => {
        uploads(new Error('The file is too large.'));
        await start();
        click('[data-odiseo-blog-editor-dialog-param="image"]');

        choose('imageFile', [photo('huge.jpg')]);

        await vi.waitFor(() => expect($('modalError').textContent).toBe('The file is too large.'));
        expect($('imagePreview').hidden).toBe(true);
    });

    it('warn in the editor when they have no alt text', async () => {
        await start('{"type":"doc","content":[{"type":"image","attrs":{"path":"ab/cd/mugs.jpg","alt":"","size":"small"}}]}');

        expect($('surface').querySelector('img').getAttribute('src')).toBe('/media/cache/resolve/odiseo_blog_content_640/ab/cd/mugs.jpg');
        expect($('surface').textContent).toContain('No alt');
    });

    it('are added where they are pasted', async () => {
        uploads({ path: 'ab/cd/one.jpg', width: 10, height: 10 }, { path: 'ab/cd/two.jpg', width: 20, height: 20 });
        const controller = await start('{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Hi"}]}]}');
        controller.editor.commands.focus('end');

        const event = new Event('paste', { bubbles: true, cancelable: true });
        event.clipboardData = { files: [photo('one.jpg'), new File(['x'], 'notes.txt', { type: 'text/plain' }), photo('two.jpg')], getData: () => '' };
        controller.editor.view.dom.dispatchEvent(event);

        await vi.waitFor(() => expect(saved().content.filter((node) => node.type === 'image').map((node) => node.attrs.path)).toEqual(['ab/cd/one.jpg', 'ab/cd/two.jpg']));
        expect(event.defaultPrevented).toBe(true);
    });

    it('tell under the editor when a pasted one fails', async () => {
        uploads(new Error(''));
        const controller = await start();

        const event = new Event('paste', { bubbles: true, cancelable: true });
        event.clipboardData = { files: [photo('one.jpg')], getData: () => '' };
        controller.editor.view.dom.dispatchEvent(event);

        await vi.waitFor(() => expect($('notice').hidden).toBe(false));
        expect($('notice').textContent).toBe('Upload failed');
    });
});

describe('galleries', () => {
    it('are made of uploaded images, each with its alt text', async () => {
        uploads({ path: 'ab/cd/one.jpg', width: 800, height: 600 }, { path: 'ab/cd/two.jpg', width: 400, height: 400 }, { path: 'ab/cd/three.jpg', width: 1, height: 1 });
        await start();
        click('[data-odiseo-blog-editor-dialog-param="gallery"]');

        click('#apply');
        expect($('modalError').textContent).toBe('No images');

        choose('galleryFiles', [photo('one.jpg'), photo('two.jpg'), photo('three.jpg')]);
        await vi.waitFor(() => expect($('galleryList').querySelectorAll('li')).toHaveLength(3));
        expect($('galleryList').querySelector('img').getAttribute('src')).toBe('/media/cache/resolve/odiseo_blog_admin_thumbnail/ab/cd/one.jpg');

        const first = $('galleryList').querySelector('input');
        first.value = 'First';
        first.dispatchEvent(new Event('input'));
        $('galleryList').querySelectorAll('button')[1].click();
        $('galleryColumns').value = '2';
        $('galleryCaption').value = 'The set';
        click('#apply');

        expect(saved().content).toEqual([{ type: 'gallery', attrs: {
            images: [{ path: 'ab/cd/one.jpg', alt: 'First', width: 800, height: 600 }, { path: 'ab/cd/three.jpg', alt: '', width: 1, height: 1 }],
            columns: 2,
            caption: 'The set',
        } }]);
    });

    it('are edited from their card', async () => {
        const controller = await start('{"type":"doc","content":[{"type":"gallery","attrs":{"images":[{"path":"ab/cd/one.jpg","alt":"One"}],"columns":4,"caption":"Set"}}]}');
        expect($('surface').querySelectorAll('img')).toHaveLength(1);
        controller.editor.commands.setNodeSelection(0);
        click('[data-odiseo-blog-editor-dialog-param="gallery"]');

        expect($('galleryList').querySelector('input').value).toBe('One');
        expect($('galleryColumns').value).toBe('4');
        expect($('galleryCaption').value).toBe('Set');
    });
});

describe('without the editor', () => {
    it('keeps the JSON field when it is not JSON', async () => {
        const controller = await start('{"type":');

        expect(controller.editor).toBeUndefined();
        expect($('input').hidden).toBe(false);
        expect($('fallback').hidden).toBe(false);
        expect($('surface').hidden).toBe(true);
    });

    it('keeps the JSON field when the content has nodes the editor does not know', async () => {
        const value = '{"type":"doc","content":[{"type":"mystery"}]}';
        const controller = await start(value);

        expect(controller.editor).toBeNull();
        expect($('input').value).toBe(value);
        expect($('fallback').hidden).toBe(false);
    });

    it('opens nodes that an application adds', async () => {
        const Mystery = Node.create({ name: 'mystery', group: 'block', atom: true, renderHTML: () => ['div'] });
        document.addEventListener('odiseo-blog-editor:options', (event) => event.detail.extensions.push(Mystery), { once: true });

        const controller = await start('{"type":"doc","content":[{"type":"mystery"}]}');

        expect(controller.editor.getJSON().content[0].type).toBe('mystery');
        expect($('fallback').hidden).toBe(true);
    });
});
