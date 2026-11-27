/** Uploads images for the content and gives the address of a stored one. */
export class MediaLibrary {
    /**
     * @param urls Addresses with `placeholder` where the path goes: `image` for a preview, `thumbnail` for a square one.
     */
    constructor({ uploadUrl, token = '', urls = {} }, fetcher = (...args) => window.fetch(...args)) {
        this.uploadUrl = uploadUrl;
        this.token = token;
        this.urls = urls;
        this.fetcher = fetcher;
    }

    url(path, kind = 'image') {
        const template = this.urls[kind];
        if (!template || !path) {
            return null;
        }

        return template.replace(this.urls.placeholder ?? '__PATH__', path.split('/').map(encodeURIComponent).join('/'));
    }

    /** The path and size of the stored image. Fails with the reason the server gives, if any. */
    async upload(file) {
        const body = new FormData();
        body.append('file', file);
        body.append('_token', this.token);

        let response;
        try {
            response = await this.fetcher(this.uploadUrl, { method: 'POST', body, headers: { Accept: 'application/json' }, credentials: 'same-origin' });
        } catch {
            throw new Error('');
        }
        const data = await response.json().catch(() => ({}));
        if (!response.ok || typeof data.path !== 'string') {
            throw new Error(typeof data.error === 'string' ? data.error : '');
        }

        return { path: data.path, width: data.width ?? null, height: data.height ?? null };
    }
}

/** The image files of a paste or a drop. */
export function imageFiles(files) {
    return [...(files ?? [])].filter((file) => file.type.startsWith('image/'));
}
