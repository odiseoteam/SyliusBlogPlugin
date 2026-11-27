import { describe, expect, it, vi } from 'vitest';
import { ProductCatalog } from '../../assets/admin/editor/products.js';

const MUG = { code: 'MUG', name: 'Mug', image: null };
const BEANS = { code: 'BEANS', name: 'Beans', image: '/media/beans.jpg' };

function catalog(products) {
    const fetcher = vi.fn(async () => ({ ok: true, json: async () => products }));

    return [new ProductCatalog('/admin/blog/products/search', fetcher), fetcher];
}

describe('the product catalog', () => {
    it('searches by name or code', async () => {
        const [products, fetcher] = catalog([MUG]);

        expect(await products.search('mu g')).toEqual([MUG]);
        expect(fetcher.mock.calls[0][0]).toBe('http://localhost:3000/admin/blog/products/search?query=mu+g');
    });

    it('describes the products of a moment with one request', async () => {
        const [products, fetcher] = catalog([BEANS, MUG]);

        const described = await Promise.all([products.describe('MUG'), products.describe('BEANS'), products.describe('GONE')]);

        expect(described).toEqual([MUG, BEANS, null]);
        expect(fetcher).toHaveBeenCalledTimes(1);
        expect(fetcher.mock.calls[0][0]).toBe('http://localhost:3000/admin/blog/products/search?code%5B%5D=MUG&code%5B%5D=BEANS&code%5B%5D=GONE');
        expect(await products.describe('MUG')).toEqual(MUG);
        expect(fetcher).toHaveBeenCalledTimes(1);
    });

    it('knows the products it found', async () => {
        const [products, fetcher] = catalog([MUG]);
        await products.search('mug');

        expect(await products.describe('MUG')).toEqual(MUG);
        expect(fetcher).toHaveBeenCalledTimes(1);
    });

    it('finds nothing when the request fails', async () => {
        const products = new ProductCatalog('/search', async () => { throw new Error('offline'); });

        expect(await products.search('mug')).toEqual([]);
        expect(await products.describe('MUG')).toBeNull();
    });
});
