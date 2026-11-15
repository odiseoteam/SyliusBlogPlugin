<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Validator\Constraints;

use Odiseo\SyliusBlogPlugin\Content\Validator\ContentValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ValidContentValidator extends ConstraintValidator
{
    public function __construct(private readonly ContentValidator $contentValidator)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidContent) {
            throw new UnexpectedTypeException($constraint, ValidContent::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        foreach ($this->contentValidator->validate($value) as $violation) {
            $this->context->buildViolation($violation->message)
                ->setParameters($violation->parameters)
                ->addViolation()
            ;
        }
    }
}
