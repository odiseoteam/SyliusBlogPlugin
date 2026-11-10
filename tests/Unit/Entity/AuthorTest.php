<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Entity;

use Odiseo\SyliusBlogPlugin\Entity\Author;
use Odiseo\SyliusBlogPlugin\Entity\AuthorImage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Author::class)]
final class AuthorTest extends TestCase
{
    public function testTheAvatarBelongsToTheAuthor(): void
    {
        $author = new Author();
        $image = new AuthorImage();

        $author->setImage($image);

        self::assertSame($author, $image->getOwner());
    }

    public function testTheSocialLinksKeepTheirOrderWithoutGaps(): void
    {
        $author = new Author();

        $author->setSocialLinks([
            2 => ['network' => 'github', 'url' => 'https://github.com/jane'],
            5 => ['network' => 'linkedin', 'url' => 'https://www.linkedin.com/in/jane'],
        ]);

        self::assertSame(['github', 'linkedin'], array_column($author->getSocialLinks(), 'network'));
        self::assertSame([0, 1], array_keys($author->getSocialLinks()));
    }
}
