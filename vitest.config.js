import { defineConfig } from 'vitest/config';

export default defineConfig({
    test: {
        environment: 'jsdom',
        // Only the tests of the plugin: the test application has its own node_modules.
        include: ['tests/JavaScript/**/*.test.js'],
    },
});
