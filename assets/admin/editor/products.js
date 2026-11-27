/** Searches the products of the catalog and describes them by code, asking for the codes of a moment at once. */
export class ProductCatalog {
    constructor(url, fetcher = (...args) => window.fetch(...args)) {
        this.url = url;
        this.fetcher = fetcher;
        this.described = new Map();
        this.pending = new Map();
        this.timer = null;
    }

    async search(query) {
        const products = await this.get({ query });
        products.forEach((product) => this.described.set(product.code, Promise.resolve(product)));

        return products;
    }

    /** The code, name and image of a product, or null when the catalog does not have it. */
    describe(code) {
        if (!this.described.has(code)) {
            this.described.set(code, new Promise((resolve) => { this.pending.set(code, resolve); }));
            this.timer ??= setTimeout(() => this.flush());
        }

        return this.described.get(code);
    }

    async flush() {
        this.timer = null;
        const pending = new Map(this.pending);
        this.pending.clear();
        const products = await this.get({ code: [...pending.keys()] });
        pending.forEach((resolve, code) => resolve(products.find((product) => product.code === code) ?? null));
    }

    async get(params) {
        const url = new URL(this.url, window.location.href);
        Object.entries(params).forEach(([name, value]) => {
            [value].flat().forEach((item) => url.searchParams.append(Array.isArray(value) ? `${name}[]` : name, item));
        });

        try {
            const response = await this.fetcher(url.toString(), { headers: { Accept: 'application/json' }, credentials: 'same-origin' });

            return response.ok ? await response.json() : [];
        } catch {
            return [];
        }
    }
}
