<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Registry;

use Odiseo\SyliusBlogPlugin\Content\Marks\ContentMarkInterface;
use Odiseo\SyliusBlogPlugin\Content\Nodes\ContentNodeInterface;

/** The nodes and marks the content can have. For a repeated type, the first one wins (the one with the highest priority). */
final class ContentRegistry
{
    /** @var array<string, ContentNodeInterface> */
    private array $nodes = [];

    /** @var array<string, ContentMarkInterface> */
    private array $marks = [];

    /**
     * @param iterable<ContentNodeInterface> $nodes
     * @param iterable<ContentMarkInterface> $marks
     */
    public function __construct(iterable $nodes, iterable $marks)
    {
        foreach ($nodes as $node) {
            $this->nodes[$node->getType()] ??= $node;
        }

        foreach ($marks as $mark) {
            $this->marks[$mark->getType()] ??= $mark;
        }
    }

    public function getNode(string $type): ?ContentNodeInterface
    {
        return $this->nodes[$type] ?? null;
    }

    public function getMark(string $type): ?ContentMarkInterface
    {
        return $this->marks[$type] ?? null;
    }
}
