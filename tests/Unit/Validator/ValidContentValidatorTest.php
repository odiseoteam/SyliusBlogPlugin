<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Validator;

use Odiseo\SyliusBlogPlugin\Content\Validator\ContentValidator;
use Odiseo\SyliusBlogPlugin\Validator\Constraints\ValidContent;
use Odiseo\SyliusBlogPlugin\Validator\Constraints\ValidContentValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Symfony\Component\Validator\Validation;
use Tests\Odiseo\SyliusBlogPlugin\Unit\Content\ContentPipeline;

/** @extends ConstraintValidatorTestCase<ValidContentValidator> */
#[CoversClass(ValidContentValidator::class)]
final class ValidContentValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ValidContentValidator
    {
        return new ValidContentValidator(
            new ContentValidator((new ContentPipeline())->registry, Validation::createValidator(), 1048576, 20),
        );
    }

    public function testNoContentIsValid(): void
    {
        $this->validator->validate(null, new ValidContent());
        $this->validator->validate('', new ValidContent());

        $this->assertNoViolation();
    }

    public function testADocumentIsValid(): void
    {
        $this->validator->validate('{"type":"doc","content":[{"type":"paragraph"}]}', new ValidContent());

        $this->assertNoViolation();
    }

    public function testEachProblemIsAViolationWithItsParameters(): void
    {
        $this->validator->validate('{"type":"doc","content":[{"type":"mystery"}]}', new ValidContent());

        $this->buildViolation('odiseo_blog.content.unknown_node')
            ->setParameter('{{ type }}', 'mystery')
            ->assertRaised()
        ;
    }

    public function testItNeedsTheRightConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('{}', new \Symfony\Component\Validator\Constraints\Json());
    }

    public function testItNeedsAString(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate(['type' => 'doc'], new ValidContent());
    }
}
