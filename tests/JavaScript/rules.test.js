import { describe, expect, it } from 'vitest';
import { isAllowedHref, relOf, relTokens } from '../../assets/admin/editor/links.js';
import { embedUrl } from '../../assets/admin/editor/video.js';
import { countWords, readingMinutes } from '../../assets/admin/editor/words.js';
import links from '../Fixtures/Content/links.json';
import videos from '../Fixtures/Content/videos.json';
import words from '../Fixtures/Content/words.json';

// The cases are shared with the PHPUnit tests: the editor and the server follow the same rules.
describe('words', () => {
    it.each(words)('counts $case', ({ text, words: count }) => {
        expect(countWords(text)).toBe(count);
    });

    it('rounds the reading time up', () => {
        expect([0, 12, 200, 201].map((count) => readingMinutes(count))).toEqual([0, 1, 1, 2]);
        expect(readingMinutes(150, 100)).toBe(2);
    });
});

describe('videos', () => {
    it.each(videos)('embeds $case', ({ url, embed }) => {
        expect(embedUrl(url)).toBe(embed);
    });
});

describe('links', () => {
    it.each(links)('checks $case', ({ href, allowed }) => {
        expect(isAllowedHref(href)).toBe(allowed);
    });

    it('builds the relation from its options', () => {
        expect(relOf()).toBeNull();
        expect(relOf({ nofollow: true })).toBe('nofollow');
        expect(relOf({ nofollow: true, sponsored: true })).toBe('nofollow sponsored');
        expect(relTokens('nofollow  sponsored')).toEqual(['nofollow', 'sponsored']);
        expect(relTokens(null)).toEqual([]);
    });
});
