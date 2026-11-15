<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Marks;

use Odiseo\SyliusBlogPlugin\Content\Document\Mark;

/** Defaults for a mark: no attributes and the template named after its type. */
abstract class AbstractContentMark implements ContentMarkInterface
{
    public function getAttributes(): array
    {
        return [];
    }

    public function getConstraints(): array
    {
        return [];
    }

    public function getTemplate(): string
    {
        return sprintf('@OdiseoSyliusBlogPlugin/content/mark/%s.html.twig', $this->getType());
    }

    public function getVariables(Mark $mark): array
    {
        return [];
    }

    /** The value of an attribute of the mark, or its default. */
    protected function attribute(Mark $mark, string $name): mixed
    {
        return $mark->attributes[$name] ?? $this->getAttributes()[$name] ?? null;
    }
}
