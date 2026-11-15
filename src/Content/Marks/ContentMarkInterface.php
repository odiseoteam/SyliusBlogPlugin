<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Marks;

use Odiseo\SyliusBlogPlugin\Content\Document\Mark;
use Symfony\Component\Validator\Constraint;

/**
 * A format that can be applied to text. Tag the service with `odiseo_blog.content_mark`
 * (implementing this interface is enough with autoconfigure).
 */
interface ContentMarkInterface
{
    /** The `type` of the mark in the document, like `bold`. */
    public function getType(): string;

    /**
     * The attributes the mark uses, with their defaults. Any other attribute is ignored.
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

    /** Twig template of the mark. It receives `mark`, `attributes`, `content` and the variables below. */
    public function getTemplate(): string;

    /**
     * Extra variables for the template.
     *
     * @return array<string, mixed>
     */
    public function getVariables(Mark $mark): array;

    public function toMarkdown(Mark $mark, string $text): string;
}
