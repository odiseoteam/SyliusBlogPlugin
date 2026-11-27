<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

/** Helpers to nest blocks in Markdown. */
final class MarkdownLines
{
    /** Puts `$first` before the first line and `$rest` before the others. */
    public static function prefix(string $text, string $first, string $rest): string
    {
        $lines = explode("\n", $text);

        foreach ($lines as $index => $line) {
            $lines[$index] = rtrim((0 === $index ? $first : $rest) . $line);
        }

        return implode("\n", $lines);
    }

    /** Escapes what would turn a line of text into a heading, a list item or a rule. */
    public static function escapeLineStarts(string $text): string
    {
        $text = (string) preg_replace('/^(\s*)(#{1,6}|[+=-]+)(?=\s|$)/m', '$1\\\\$2', $text);

        return (string) preg_replace('/^(\s*\d+)([.)])(?=\s|$)/m', '$1\\\\$2', $text);
    }
}
