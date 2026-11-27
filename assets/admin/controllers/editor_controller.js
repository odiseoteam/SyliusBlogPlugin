import { Controller } from '@hotwired/stimulus';
import { Editor } from '@tiptap/core';
import TomSelect from 'tom-select';
import { createExtensions, serialize, textOf } from '../editor/extensions.js';
import { GALLERY_COLUMNS } from '../editor/gallery.js';
import { IMAGE_PATH, IMAGE_SIZES } from '../editor/image.js';
import { isAllowedHref, relOf, relTokens } from '../editor/links.js';
import { imageFiles, MediaLibrary } from '../editor/media.js';
import { PRODUCT_CODE } from '../editor/product.js';
import { ProductCatalog } from '../editor/products.js';
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

const DIALOGS = ['link', 'video', 'html', 'product', 'image', 'gallery'];

export default class extends Controller {
    static targets = [
        'input', 'toolbar', 'surface', 'stats', 'help', 'fallback',
        'modal', 'modalTitle', 'pane', 'modalError', 'removeLink',
        'href', 'newTab', 'nofollow', 'sponsored', 'videoSrc', 'html', 'product',
        'imageFile', 'imagePreview', 'imageAlt', 'imageCaption', 'imageSize',
        'galleryFiles', 'galleryList', 'galleryColumns', 'galleryCaption', 'notice',
    ];

    static values = {
        wordsPerMinute: { type: Number, default: 200 },
        placeholder: String,
        stats: String,
        labels: Object,
        productSearchUrl: String,
        mediaUploadUrl: String,
        mediaToken: String,
        mediaUrls: Object,
    };

    connect() {
        const content = this.readContent();
        if (content === undefined) {
            this.showFallback();

            return;
        }

        this.catalog = this.productSearchUrlValue ? new ProductCatalog(this.productSearchUrlValue) : null;
        this.media = this.mediaUploadUrlValue
            ? new MediaLibrary({ uploadUrl: this.mediaUploadUrlValue, token: this.mediaTokenValue, urls: this.mediaUrlsValue })
            : null;
        this.uploads = 0;
        const extensions = createExtensions({
            placeholder: this.placeholderValue,
            labels: this.labelsValue,
            describeProduct: this.catalog ? (code) => this.catalog.describe(code) : null,
            mediaUrl: (path, kind) => this.media?.url(path, kind) ?? null,
        });
        // Applications add their own nodes by listening to this event.
        const { detail } = this.dispatch('options', { detail: { extensions } });

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
                handlePaste: (view, event) => this.dropImages(imageFiles(event.clipboardData?.files), null, event),
                handleDrop: (view, event, slice, moved) => !moved
                    && this.dropImages(imageFiles(event.dataTransfer?.files), view.posAtCoords({ left: event.clientX, top: event.clientY })?.pos ?? null, event),
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
        this.productSelect?.destroy();
        this.productSelect = null;
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
        const applied = {
            link: () => this.applyLink(),
            video: () => this.applyVideo(),
            html: () => this.applyHtml(),
            product: () => this.applyProduct(),
            image: () => this.applyImage(),
            gallery: () => this.applyGallery(),
        }[this.dialog]?.();
        if (applied) {
            this.modal().hide();
        }
    }

    removeLink() {
        this.editor.chain().focus().extendMarkRange('link').unsetLink().run();
        this.modal().hide();
    }

    // Enter in a field of the dialog applies it instead of sending the form.
    submitOnEnter(event) {
        // Tom Select takes the Enter that picks an option.
        if (event.key === 'Enter' && !event.defaultPrevented && event.target.tagName !== 'TEXTAREA') {
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
        if (dialog === 'product') {
            this.selectProduct(node?.type.name === 'product' ? node.attrs.code : null);
        }
        if (dialog === 'image') {
            this.openImage(node?.type.name === 'image' ? node.attrs : null);
        }
        if (dialog === 'gallery') {
            this.openGallery(node?.type.name === 'gallery' ? node.attrs : null);
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

    applyProduct() {
        const code = this.productSelect?.getValue() ?? '';
        if (!PRODUCT_CODE.test(code)) {
            return this.fail('invalidProduct');
        }
        this.editor.chain().focus().setProduct(code).run();

        return true;
    }

    openImage(attrs) {
        this.draftImage = attrs ? { ...attrs } : null;
        this.imageFileTarget.value = '';
        this.imageAltTarget.value = attrs?.alt ?? '';
        this.imageCaptionTarget.value = attrs?.caption ?? '';
        this.imageSizeTarget.value = attrs?.size in IMAGE_SIZES ? attrs.size : 'full';
        this.showImagePreview();
    }

    /** Uploads the chosen file right away, so the dialog can show it. */
    async uploadImage() {
        const [file] = this.imageFileTarget.files ?? [];
        if (file) {
            const stored = await this.upload(file);
            if (stored) {
                this.draftImage = { ...this.draftImage, ...stored };
                this.showImagePreview();
            }
        }
    }

    showImagePreview() {
        const url = this.media?.url(this.draftImage?.path);
        this.imagePreviewTarget.hidden = !url;
        this.imagePreviewTarget.src = url ?? '';
    }

    applyImage() {
        if (this.uploads > 0) {
            return this.fail('uploading');
        }
        if (!IMAGE_PATH.test(this.draftImage?.path ?? '')) {
            return this.fail('invalidImage');
        }
        this.editor.chain().focus().setImage({
            ...this.draftImage,
            alt: this.imageAltTarget.value.trim(),
            caption: this.imageCaptionTarget.value.trim(),
            size: this.imageSizeTarget.value,
        }).run();

        return true;
    }

    openGallery(attrs) {
        this.draftGallery = (attrs?.images ?? []).map((image) => ({ ...image }));
        this.galleryFilesTarget.value = '';
        this.galleryColumnsTarget.value = String(GALLERY_COLUMNS.includes(attrs?.columns) ? attrs.columns : 3);
        this.galleryCaptionTarget.value = attrs?.caption ?? '';
        this.renderGallery();
    }

    async uploadGallery() {
        const files = [...(this.galleryFilesTarget.files ?? [])];
        this.galleryFilesTarget.value = '';
        for (const file of files) {
            const stored = await this.upload(file);
            if (stored) {
                this.draftGallery.push({ path: stored.path, alt: '', width: stored.width, height: stored.height });
                this.renderGallery();
            }
        }
    }

    /** One row per image: its thumbnail, its alt text and a button to take it out. */
    renderGallery() {
        this.galleryListTarget.replaceChildren(...this.draftGallery.map((image, index) => {
            const row = document.createElement('li');
            row.className = 'list-group-item d-flex gap-2 align-items-center';
            const thumbnail = row.appendChild(document.createElement('img'));
            thumbnail.src = this.media?.url(image.path, 'thumbnail') ?? '';
            thumbnail.alt = '';
            thumbnail.className = 'avatar avatar-md';
            const alt = row.appendChild(document.createElement('input'));
            alt.type = 'text';
            alt.className = 'form-control';
            alt.value = image.alt ?? '';
            alt.placeholder = this.labelsValue.alt ?? '';
            alt.setAttribute('aria-label', this.labelsValue.alt ?? '');
            alt.addEventListener('input', () => { this.draftGallery[index].alt = alt.value; });
            const remove = row.appendChild(document.createElement('button'));
            remove.type = 'button';
            remove.className = 'btn btn-ghost-danger';
            remove.textContent = this.labelsValue.remove ?? '×';
            remove.addEventListener('click', () => {
                this.draftGallery.splice(index, 1);
                this.renderGallery();
            });

            return row;
        }));
    }

    applyGallery() {
        if (this.uploads > 0) {
            return this.fail('uploading');
        }
        const images = this.draftGallery
            .filter((image) => IMAGE_PATH.test(image.path ?? ''))
            .map((image) => ({ ...image, alt: (image.alt ?? '').trim() }));
        if (images.length === 0) {
            return this.fail('invalidGallery');
        }
        this.editor.chain().focus().setGallery({
            images,
            columns: Number(this.galleryColumnsTarget.value),
            caption: this.galleryCaptionTarget.value.trim(),
        }).run();

        return true;
    }

    /** Stores a file, telling in the dialog that it is uploading and why it failed. */
    async upload(file) {
        this.uploads += 1;
        this.modalErrorTarget.textContent = this.labelsValue.uploading ?? '';
        try {
            const stored = await this.media.upload(file);
            this.modalErrorTarget.textContent = '';

            return stored;
        } catch (error) {
            this.modalErrorTarget.textContent = error.message || (this.labelsValue.uploadFailed ?? '');

            return null;
        } finally {
            this.uploads -= 1;
        }
    }

    /** Images pasted or dropped on the text are uploaded and added where they landed. */
    dropImages(files, pos, event) {
        if (!this.media || files.length === 0) {
            return false;
        }
        event.preventDefault();
        this.noticeTarget.hidden = true;
        // The first one goes where it landed and each next one after the previous one.
        files.reduce((previous, file, index) => previous.then(async () => {
            try {
                const stored = await this.media.upload(file);
                const content = { type: 'image', attrs: stored };
                if (index > 0) {
                    this.editor.chain().focus().insertContentAt(this.editor.state.selection.to, content).run();
                } else if (pos === null) {
                    this.editor.chain().focus().insertContent(content).run();
                } else {
                    this.editor.chain().focus().insertContentAt(pos, content).run();
                }
            } catch (error) {
                this.noticeTarget.textContent = error.message || (this.labelsValue.uploadFailed ?? '');
                this.noticeTarget.hidden = false;
            }
        }), Promise.resolve());

        return true;
    }

    /** The product field, searched with Tom Select like the autocompletes of Sylius. */
    selectProduct(code) {
        this.productSelect ??= new TomSelect(this.productTarget, {
            valueField: 'code',
            labelField: 'name',
            searchField: ['name', 'code'],
            maxItems: 1,
            load: (query, callback) => { this.catalog.search(query).then(callback, () => callback()); },
            render: { option: renderProduct, item: renderProduct },
        });
        this.productSelect.clear(true);
        if (code) {
            this.catalog.describe(code).then((product) => {
                this.productSelect.addOption(product ?? { code, name: code });
                this.productSelect.setValue(code, true);
            });
        }
    }

    fail(label) {
        this.modalErrorTarget.textContent = this.labelsValue[label] ?? '';

        return false;
    }

    selectedNode() {
        return this.editor.state.selection.node ?? null;
    }

    firstField() {
        if (this.dialog === 'product') {
            return this.productSelect?.control_input;
        }
        if (this.dialog === 'image' || this.dialog === 'gallery') {
            return this.paneTargets.find((pane) => !pane.hidden)?.querySelector('input[type="file"]');
        }

        return this.paneTargets.find((pane) => !pane.hidden)?.querySelector('input, textarea');
    }

    modal() {
        return window.bootstrap.Modal.getOrCreateInstance(this.modalTarget);
    }
}

function renderProduct(product, escape) {
    const image = product.image ? `<img src="${escape(product.image)}" alt="" class="avatar avatar-xs me-2">` : '';

    return `<div class="d-flex align-items-center">${image}<span>${escape(product.name)}</span><small class="text-secondary ms-2">${escape(product.code)}</small></div>`;
}
