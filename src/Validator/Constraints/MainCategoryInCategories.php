<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Validator\Constraints;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

/** The main category of an article is one of its categories. */
final class MainCategoryInCategories extends Constraint
{
    public string $message = 'odiseo_blog.article.main_category.not_in_categories';

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
