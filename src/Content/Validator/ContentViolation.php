<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Validator;

/** What is wrong with a content: a translation key and its parameters. */
final readonly class ContentViolation
{
    /** @param array<string, string> $parameters */
    public function __construct(
        public string $message,
        public array $parameters = [],
    ) {
    }
}
