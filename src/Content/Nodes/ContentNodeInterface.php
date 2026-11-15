<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Symfony\Component\Validator\Constraint;

/**
 * A type of node the content can have. Tag the service with `odiseo_blog.content_node`
 * (implementing this interface is enough with autoconfigure).
 */
interface ContentNodeInterface
{
    /** The `type` of the node in the document, like `paragraph`. */
    public function getType(): string;

    /**
     * The attributes the node uses, with their defaults. Any other attribute is ignored.
     *
     * @return array<string, mixed>
     */
    public function getAttributes(): array;

    /**
     * Rules for the value of each attribute, checked when the content is saved.
     *
     * @return array<string, Constraint|list<Constraint>>
     */
    public function getConstraints(): array;

    /** Inline nodes flow inside a line of text; the rest are blocks. */
    public function isInline(): bool;

    /** Twig template of the node. It receives `node`, `attributes`, `children` and the variables below. */
    public function getTemplate(): string;

    /**
     * Extra variables for the template.
     *
     * @return array<string, mixed>
     */
    public function getVariables(Node $node): array;

    /** @param list<string> $children The Markdown of each child. */
    public function toMarkdown(Node $node, array $children): string;
}
