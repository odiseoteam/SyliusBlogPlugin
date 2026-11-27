import { describe, expect, it, vi } from 'vitest';
import { imageFiles, MediaLibrary } from '../../assets/admin/editor/media.js';

const URLS = { placeholder: '__PATH__', image: '/media/cache/resolve/odiseo_blog_content_640/__PATH__' };

function library(response) {
    const fetcher = vi.fn(async () => {
        if (response instanceof Error) {
            throw response;
        }

        return response;
    });

    return [new MediaLibrary({ uploadUrl: '/admin/blog/media', token: 'token', urls: URLS }, fetcher), fetcher];
}

describe('the media library', () => {
    it('gives the address of a stored image', () => {
        const [media] = library();

        expect(media.url('ab/cd/a b.jpg')).toBe('/media/cache/resolve/odiseo_blog_content_640/ab/cd/a%20b.jpg');
        expect(media.url('ab/cd/a.jpg', 'thumbnail')).toBeNull();
        expect(media.url(null)).toBeNull();
    });

    it('uploads a file with the token of the editor', async () => {
        const [media, fetcher] = library({ ok: true, json: async () => ({ path: 'ab/cd/a.jpg', width: 10, height: 20 }) });

        expect(await media.upload(new File(['x'], 'a.jpg'))).toEqual({ path: 'ab/cd/a.jpg', width: 10, height: 20 });
        const [url, request] = fetcher.mock.calls[0];
        expect(url).toBe('/admin/blog/media');
        expect(request.method).toBe('POST');
        expect(request.body.get('_token')).toBe('token');
        expect(request.body.get('file').name).toBe('a.jpg');
    });

    it('fails with the reason of the server', async () => {
        const [media] = library({ ok: false, json: async () => ({ error: 'Too large.' }) });

        await expect(media.upload(new File(['x'], 'a.jpg'))).rejects.toThrow('Too large.');
    });

    it('fails without reason when the server cannot be reached', async () => {
        const [media] = library(new TypeError('offline'));

        await expect(media.upload(new File(['x'], 'a.jpg'))).rejects.toThrow(/^$/);
    });
});

describe('the files of a paste or a drop', () => {
    it('are only the images', () => {
        const image = new File(['x'], 'a.png', { type: 'image/png' });

        expect(imageFiles([image, new File(['x'], 'a.txt', { type: 'text/plain' })])).toEqual([image]);
        expect(imageFiles(undefined)).toEqual([]);
    });
});
