<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

/** A category is neither its own parent nor the child of one of its descendants. */
final class CategoryParentRelation extends Constraint
{
    public string $message = 'odiseo_blog.article_category.parent.invalid_relation';

    /** @param string[]|null $groups */
    #[HasNamedArguments]
    public function __construct(?string $message = null, ?array $groups = null, mixed $payload = null)
    {
        parent::__construct(null, $groups, $payload);

        $this->message = $message ?? $this->message;
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
