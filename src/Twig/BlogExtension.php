<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class BlogExtension extends AbstractExtension
{
    /** @var string|null */
    private $disqusShortname;

    public function __construct(
        ?string $disqusShortname
    ) {
        $this->disqusShortname = $disqusShortname;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('disqus_shortname', [$this, 'getDisqusShortname'])
        ];
    }

    /**
     * @return string|null
     */
    public function getDisqusShortname(): ?string
    {
        return $this->disqusShortname;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'blog';
    }
}
