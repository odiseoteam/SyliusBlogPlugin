<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Document;

/** A format applied to a text node, like bold or a link. */
final readonly class Mark
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public string $type,
        public array $attributes = [],
    ) {
    }
}
