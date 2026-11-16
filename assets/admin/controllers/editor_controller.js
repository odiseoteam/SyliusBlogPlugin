import { Controller } from '@hotwired/stimulus';
import { Editor } from '@tiptap/core';
import { createExtensions, serialize, textOf } from '../editor/extensions.js';
import { isAllowedHref, relOf, relTokens } from '../editor/links.js';
import { embedUrl } from '../editor/video.js';
import { countWords, readingMinutes } from '../editor/words.js';

// Toolbar commands: what they run and when they show as active.
const COMMANDS = {
    paragraph: { run: (chain) => chain.setParagraph(), active: ['paragraph'] },
    heading: { run: (chain, { level }) => chain.toggleHeading({ level }), active: ['heading', (params) => ({ level: params.level })] },
    bold: { run: (chain) => chain.toggleBold(), active: ['bold'] },
    italic: { run: (chain) => chain.toggleItalic(), active: ['italic'] },
    underline: { run: (chain) => chain.toggleUnderline(), active: ['underline'] },
    strike: { run: (chain) => chain.toggleStrike(), active: ['strike'] },
    code: { run: (chain) => chain.toggleCode(), active: ['code'] },
    bulletList: { run: (chain) => chain.toggleBulletList(), active: ['bulletList'] },
    orderedList: { run: (chain) => chain.toggleOrderedList(), active: ['orderedList'] },
    blockquote: { run: (chain) => chain.toggleBlockquote(), active: ['blockquote'] },
    codeBlock: { run: (chain) => chain.toggleCodeBlock(), active: ['codeBlock'] },
    horizontalRule: { run: (chain) => chain.setHorizontalRule() },
    insertTable: { run: (chain) => chain.insertTable({ rows: 3, cols: 3, withHeaderRow: true }) },
    addRowAfter: { run: (chain) => chain.addRowAfter() },
    addColumnAfter: { run: (chain) => chain.addColumnAfter() },
    deleteRow: { run: (chain) => chain.deleteRow() },
    deleteColumn: { run: (chain) => chain.deleteColumn() },
    deleteTable: { run: (chain) => chain.deleteTable() },
    clearFormatting: { run: (chain) => chain.unsetAllMarks().clearNodes() },
    undo: { run: (chain) => chain.undo() },
    redo: { run: (chain) => chain.redo() },
};

const DIALOGS = ['link', 'video', 'html'];

export default class extends Controller {
    static targets = [
        'input', 'toolbar', 'surface', 'stats', 'help', 'fallback',
        'modal', 'modalTitle', 'pane', 'modalError', 'removeLink',
        'href', 'newTab', 'nofollow', 'sponsored', 'videoSrc', 'html',
    ];

    static values = {
        wordsPerMinute: { type: Number, default: 200 },
        placeholder: String,
        stats: String,
        labels: Object,
    };

    connect() {
        const content = this.readContent();
        if (content === undefined) {
            this.showFallback();

            return;
        }

        // Applications add their own nodes by listening to this event.
        const { detail } = this.dispatch('options', { detail: { extensions: createExtensions({ placeholder: this.placeholderValue, labels: this.labelsValue }) } });

        let invalid = false;
        this.editor = new Editor({
            element: this.surfaceTarget,
            extensions: detail.extensions,
            content,
            enableContentCheck: true,
            onContentError: () => { invalid = true; },
            editorProps: {
                attributes: { class: 'odiseo-blog-editor__content', 'aria-label': this.labelsValue.content ?? '' },
                handleDoubleClickOn: (view, pos, node) => this.editNode(pos, node),
                handleKeyDown: (view, event) => this.handleKeyDown(event),
            },
        });

        if (invalid) {
            this.editor.destroy();
            this.editor = null;
            this.showFallback();

            return;
        }

        this.editor.on('update', () => this.sync());
        this.editor.on('transaction', () => this.refreshToolbar());
        this.inputTarget.hidden = true;
        this.helpTargets.forEach((help) => { help.hidden = true; });
        [this.toolbarTarget, this.surfaceTarget, this.statsTarget].forEach((element) => { element.hidden = false; });
        this.refreshToolbar();
        this.refreshStats();
        this.dispatch('ready', { detail: { editor: this.editor } });
    }

    disconnect() {
        this.editor?.destroy();
        this.editor = null;
    }

    run({ params }) {
        const command = COMMANDS[params.command];
        if (this.editor && command) {
            command.run(this.editor.chain().focus(), params).run();
        }
    }

    open({ params }) {
        if (this.editor && DIALOGS.includes(params.dialog)) {
            this.openDialog(params.dialog);
        }
    }

    apply(event) {
        event?.preventDefault();
        const applied = { link: () => this.applyLink(), video: () => this.applyVideo(), html: () => this.applyHtml() }[this.dialog]?.();
        if (applied) {
            this.modal().hide();
        }
    }

    removeLink() {
        this.editor.chain().focus().extendMarkRange('link').unsetLink().run();
        this.modal().hide();
    }

    // Enter in a field of the dialog applies it instead of sending the article form.
    submitOnEnter(event) {
        if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') {
            this.apply(event);
        }
    }

    readContent() {
        const value = this.inputTarget.value.trim();
        if (value === '') {
            return null;
        }

        try {
            return JSON.parse(value);
        } catch {
            return undefined;
        }
    }

    showFallback() {
        this.fallbackTargets.forEach((fallback) => { fallback.hidden = false; });
    }

    sync() {
        this.inputTarget.value = serialize(this.editor);
        this.refreshStats();
    }

    refreshStats() {
        const words = countWords(textOf(this.editor));
        this.statsTarget.textContent = this.statsValue
            .replace('%words%', String(words))
            .replace('%minutes%', String(readingMinutes(words, this.wordsPerMinuteValue)));
    }

    refreshToolbar() {
        this.toolbarTarget.querySelectorAll('[data-odiseo-blog-editor-command-param]').forEach((button) => {
            const params = { command: button.dataset.odiseoBlogEditorCommandParam, level: Number(button.dataset.odiseoBlogEditorLevelParam) };
            const active = this.isActive(params);
            button.classList.toggle('active', active);
            button.setAttribute('aria-pressed', String(active));
            if (params.command === 'undo' || params.command === 'redo') {
                button.disabled = !this.editor.can()[params.command]();
            }
        });
        this.toolbarTarget.querySelectorAll('[data-odiseo-blog-editor-dialog-param]').forEach((button) => {
            const active = button.dataset.odiseoBlogEditorDialogParam === 'link' && this.editor.isActive('link');
            button.classList.toggle('active', active);
            button.setAttribute('aria-pressed', String(active));
        });
    }

    isActive(params) {
        const [name, attributes] = COMMANDS[params.command]?.active ?? [];

        return name !== undefined && this.editor.isActive(name, typeof attributes === 'function' ? attributes(params) : undefined);
    }

    editNode(pos, node) {
        if (!DIALOGS.includes(node.type.name)) {
            return false;
        }
        this.editor.chain().setNodeSelection(pos).run();
        this.openDialog(node.type.name);

        return true;
    }

    handleKeyDown(event) {
        if ((event.metaKey || event.ctrlKey) && event.key === 'k') {
            this.openDialog('link');

            return true;
        }

        return false;
    }

    openDialog(dialog) {
        this.dialog = dialog;
        const node = this.selectedNode();
        this.paneTargets.forEach((pane) => {
            pane.hidden = pane.dataset.dialog !== dialog;
            if (!pane.hidden) {
                this.modalTitleTarget.textContent = pane.dataset.title;
            }
        });
        this.modalErrorTarget.textContent = '';

        if (dialog === 'link') {
            const { href = '', target = null, rel = null } = this.editor.getAttributes('link');
            this.hrefTarget.value = href ?? '';
            this.newTabTarget.checked = target === '_blank';
            this.nofollowTarget.checked = relTokens(rel).includes('nofollow');
            this.sponsoredTarget.checked = relTokens(rel).includes('sponsored');
            this.removeLinkTarget.hidden = !this.editor.isActive('link');
        } else {
            this.removeLinkTarget.hidden = true;
        }
        if (dialog === 'video') {
            this.videoSrcTarget.value = node?.type.name === 'video' ? node.attrs.src ?? '' : '';
        }
        if (dialog === 'html') {
            this.htmlTarget.value = node?.type.name === 'html' ? node.attrs.html ?? '' : '';
        }

        this.modalTarget.addEventListener('shown.bs.modal', () => this.firstField()?.focus(), { once: true });
        this.modal().show();
    }

    applyLink() {
        const href = this.hrefTarget.value.trim();
        if (href === '') {
            this.editor.chain().focus().extendMarkRange('link').unsetLink().run();

            return true;
        }
        if (!isAllowedHref(href)) {
            return this.fail('invalidLink');
        }

        const attributes = {
            href,
            target: this.newTabTarget.checked ? '_blank' : null,
            rel: relOf({ nofollow: this.nofollowTarget.checked, sponsored: this.sponsoredTarget.checked }),
        };
        const chain = this.editor.chain().focus();
        if (this.editor.state.selection.empty && !this.editor.isActive('link')) {
            chain.insertContent({ type: 'text', text: href, marks: [{ type: 'link', attrs: attributes }] });
        } else {
            chain.extendMarkRange('link').setLink(attributes);
        }
        chain.run();

        return true;
    }

    applyVideo() {
        const src = this.videoSrcTarget.value.trim();
        if (embedUrl(src) === null) {
            return this.fail('invalidVideo');
        }
        this.editor.chain().focus().setVideo(src).run();

        return true;
    }

    applyHtml() {
        const html = this.htmlTarget.value.trim();
        if (html === '') {
            if (this.selectedNode()?.type.name === 'html') {
                this.editor.chain().focus().deleteSelection().run();
            }

            return true;
        }
        this.editor.chain().focus().setHtml(html).run();

        return true;
    }

    fail(label) {
        this.modalErrorTarget.textContent = this.labelsValue[label] ?? '';

        return false;
    }

    selectedNode() {
        return this.editor.state.selection.node ?? null;
    }

    firstField() {
        return this.paneTargets.find((pane) => !pane.hidden)?.querySelector('input, textarea');
    }

    modal() {
        return window.bootstrap.Modal.getOrCreateInstance(this.modalTarget);
    }
}
