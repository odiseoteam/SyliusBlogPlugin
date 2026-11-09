<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Validator\Constraints;

use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class CategoryParentRelationValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CategoryParentRelation) {
            throw new UnexpectedTypeException($constraint, CategoryParentRelation::class);
        }

        if (!$value instanceof ArticleCategoryInterface) {
            throw new UnexpectedValueException($value, ArticleCategoryInterface::class);
        }

        for ($ancestor = $value->getParent(); null !== $ancestor; $ancestor = $ancestor->getParent()) {
            if ($ancestor === $value || (null !== $value->getId() && $ancestor->getId() === $value->getId())) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('parent')
                    ->addViolation()
                ;

                return;
            }
        }
    }
}
