<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface;

/** An iframe can only load an https page of an allowed host (or of one of its subdomains). */
final class IframeSourceSanitizer implements AttributeSanitizerInterface
{
    /** @param list<string> $allowedHosts */
    public function __construct(private readonly array $allowedHosts)
    {
    }

    /** @return list<string> */
    public function getSupportedElements(): array
    {
        return ['iframe'];
    }

    /** @return list<string> */
    public function getSupportedAttributes(): array
    {
        return ['src'];
    }

    public function sanitizeAttribute(string $element, string $attribute, string $value, HtmlSanitizerConfig $config): string
    {
        $parts = parse_url($value);
        $host = is_array($parts) && 'https' === ($parts['scheme'] ?? null) ? strtolower($parts['host'] ?? '') : '';

        foreach ($this->allowedHosts as $allowedHost) {
            if ('' !== $host && ($host === $allowedHost || str_ends_with($host, '.' . $allowedHost))) {
                return $value;
            }
        }

        // Empty instead of null: Symfony 6.4 passes null on to the URL sanitizer, which then drops the empty value.
        return '';
    }
}
