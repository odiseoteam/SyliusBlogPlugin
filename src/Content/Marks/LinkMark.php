<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Marks;

use Odiseo\SyliusBlogPlugin\Content\Document\Mark;
use Symfony\Component\Validator\Constraints as Assert;

final class LinkMark extends AbstractContentMark
{
    private const HREF = '~^(?:https?://|mailto:|tel:|/(?!/)|#)\S*$~i';

    private const REL = '~^(?:nofollow|sponsored|ugc)(?: (?:nofollow|sponsored|ugc))*$~';

    public function getType(): string
    {
        return 'link';
    }

    public function getAttributes(): array
    {
        return ['href' => null, 'rel' => null, 'target' => null];
    }

    public function getConstraints(): array
    {
        return [
            'href' => [new Assert\NotBlank(), new Assert\Regex(pattern: self::HREF, message: 'odiseo_blog.content.link.invalid_href')],
            'rel' => new Assert\Regex(pattern: self::REL, message: 'odiseo_blog.content.link.invalid_rel'),
            'target' => new Assert\Choice(choices: ['_blank', '_self']),
        ];
    }

    /** A link to anything but a web page, an email, a phone or a path of the site is drawn as plain text. */
    public function getVariables(Mark $mark): array
    {
        $rel = $this->attribute($mark, 'rel');
        $target = $this->attribute($mark, 'target');

        $tokens = is_string($rel) && 1 === preg_match(self::REL, $rel) ? explode(' ', $rel) : [];
        if ('_blank' === $target) {
            $tokens[] = 'noopener';
        }

        return [
            'href' => $this->href($mark),
            'rel' => [] === $tokens ? null : implode(' ', array_unique($tokens)),
            'target' => '_blank' === $target ? '_blank' : null,
        ];
    }

    public function toMarkdown(Mark $mark, string $text): string
    {
        $href = $this->href($mark);

        return null === $href ? $text : sprintf('[%s](%s)', $text, str_replace([' ', '(', ')'], ['%20', '%28', '%29'], $href));
    }

    private function href(Mark $mark): ?string
    {
        $href = $this->attribute($mark, 'href');

        return is_string($href) && 1 === preg_match(self::HREF, $href) ? $href : null;
    }
}
