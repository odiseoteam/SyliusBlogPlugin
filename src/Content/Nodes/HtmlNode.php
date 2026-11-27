<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Validator\Constraints as Assert;

/** Raw HTML for what has no node of its own. It is sanitized every time it is drawn. */
final class HtmlNode extends AbstractContentNode
{
    /** In bytes, as the sanitizer counts. */
    public const MAX_LENGTH = 100000;

    public function __construct(private readonly HtmlSanitizerInterface $sanitizer)
    {
    }

    public function getType(): string
    {
        return 'html';
    }

    public function getAttributes(): array
    {
        return ['html' => ''];
    }

    public function getConstraints(): array
    {
        return ['html' => [new Assert\Type('string'), new Assert\Length(max: self::MAX_LENGTH, countUnit: Assert\Length::COUNT_BYTES)]];
    }

    public function getVariables(Node $node): array
    {
        $html = $this->attribute($node, 'html');

        if (!is_string($html)) {
            return ['html' => ''];
        }

        // An iframe left without source (its host is not allowed) is removed.
        return ['html' => (string) preg_replace('~<iframe(?:(?!\ssrc=)[^>])*></iframe>~i', '', $this->sanitizer->sanitize($html))];
    }

    public function toMarkdown(Node $node, array $children): string
    {
        return '';
    }
}
