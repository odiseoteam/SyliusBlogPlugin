<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;

/** Defaults for a node: no attributes, a block, and the template named after its type. */
abstract class AbstractContentNode implements ContentNodeInterface
{
    public function getAttributes(): array
    {
        return [];
    }

    public function getConstraints(): array
    {
        return [];
    }

    public function isInline(): bool
    {
        return false;
    }

    public function getTemplate(): string
    {
        $name = strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $this->getType()));

        return sprintf('@OdiseoSyliusBlogPlugin/content/node/%s.html.twig', $name);
    }

    public function getVariables(Node $node): array
    {
        return [];
    }

    /** The value of an attribute of the node, or its default. */
    protected function attribute(Node $node, string $name): mixed
    {
        return $node->attributes[$name] ?? $this->getAttributes()[$name] ?? null;
    }
}
