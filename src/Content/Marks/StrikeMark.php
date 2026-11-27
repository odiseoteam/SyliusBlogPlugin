<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Marks;

use Odiseo\SyliusBlogPlugin\Content\Document\Mark;

final class StrikeMark extends AbstractContentMark
{
    public function getType(): string
    {
        return 'strike';
    }

    public function toMarkdown(Mark $mark, string $text): string
    {
        return '~~' . $text . '~~';
    }
}
