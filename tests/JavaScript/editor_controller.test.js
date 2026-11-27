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
             data-odiseo-blog-editor-labels-value='{"invalidLink":"Bad link","invalidVideo":"Bad video","invalidProduct":"Bad product"}'
             data-odiseo-blog-editor-product-search-url-value="/admin/blog/products/search">
            <div data-odiseo-blog-editor-target="toolbar" hidden>
                <button type="button" data-action="odiseo-blog-editor#run" data-odiseo-blog-editor-command-param="bold">Bold</button>
                <button type="button" data-action="odiseo-blog-editor#run" data-odiseo-blog-editor-command-param="heading" data-odiseo-blog-editor-level-param="3">H3</button>
                <button type="button" data-action="odiseo-blog-editor#run" data-odiseo-blog-editor-command-param="undo">Undo</button>
                <button type="button" data-action="odiseo-blog-editor#open" data-odiseo-blog-editor-dialog-param="link">Link</button>
                <button type="button" data-action="odiseo-blog-editor#open" data-odiseo-blog-editor-dialog-param="video">Video</button>
                <button type="button" data-action="odiseo-blog-editor#open" data-odiseo-blog-editor-dialog-param="product">Product</button>
            </div>
            <div data-odiseo-blog-editor-target="surface" hidden></div>
            <textarea data-odiseo-blog-editor-target="input"></textarea>
            <div data-odiseo-blog-editor-target="fallback" hidden></div>
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
