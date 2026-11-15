<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content;

use Odiseo\SyliusBlogPlugin\Content\Nodes\HtmlNode;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

/** The sanitizer of the HTML node: the safe elements of Symfony, plus iframes of the allowed hosts. */
final class HtmlSanitizerFactory
{
    /** @param list<string> $iframeHosts */
    public static function create(array $iframeHosts): HtmlSanitizerInterface
    {
        return new HtmlSanitizer((new HtmlSanitizerConfig())
            ->allowSafeElements()
            ->allowElement('iframe', ['src', 'width', 'height', 'title', 'allow', 'allowfullscreen', 'loading', 'referrerpolicy'])
            ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
            ->allowRelativeLinks()
            ->allowMediaSchemes(['https'])
            ->withAttributeSanitizer(new IframeSourceSanitizer(array_map('strtolower', $iframeHosts)))
            ->withMaxInputLength(HtmlNode::MAX_LENGTH))
        ;
    }
}
