<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Extractor;

final class WordCounter
{
    public function count(string $plainText): int
    {
        return (int) preg_match_all("/[\p{L}\p{N}]+(?:(?:['’-]|(?<=\p{N})[.,](?=\p{N}))[\p{L}\p{N}]+)*/u", $plainText);
    }
}
