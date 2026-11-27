<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Marks;

use Odiseo\SyliusBlogPlugin\Content\Document\Mark;

final class CodeMark extends AbstractContentMark
{
    public function getType(): string
    {
        return 'code';
    }

    public function toMarkdown(Mark $mark, string $text): string
    {
        return '`' . $text . '`';
    }
}
