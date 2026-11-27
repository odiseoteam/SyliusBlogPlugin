// Same rule as the WordCounter of the server, so the editor shows the count that is saved.
const WORD = /[\p{L}\p{N}]+(?:(?:['’-]|(?<=\p{N})[.,](?=\p{N}))[\p{L}\p{N}]+)*/gu;

export function countWords(text) {
    return (text.match(WORD) ?? []).length;
}

export function readingMinutes(words, wordsPerMinute = 200) {
    return words > 0 ? Math.ceil(words / wordsPerMinute) : 0;
}
