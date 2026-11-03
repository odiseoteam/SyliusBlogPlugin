<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Validator\Constraints;

use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class MainCategoryInCategoriesValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof MainCategoryInCategories) {
            throw new UnexpectedTypeException($constraint, MainCategoryInCategories::class);
        }

        if (!$value instanceof ArticleInterface) {
            throw new UnexpectedValueException($value, ArticleInterface::class);
        }

        $mainCategory = $value->getMainCategory();
        if (null === $mainCategory || $value->hasCategory($mainCategory)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath('mainCategory')
            ->addViolation()
        ;
    }
}
