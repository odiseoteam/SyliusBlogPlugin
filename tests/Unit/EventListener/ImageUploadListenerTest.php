<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\EventListener;

use Odiseo\SyliusBlogPlugin\Entity\Article;
use Odiseo\SyliusBlogPlugin\Entity\ArticleImage;
use Odiseo\SyliusBlogPlugin\EventListener\ImageUploadListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

#[CoversClass(ImageUploadListener::class)]
final class ImageUploadListenerTest extends TestCase
{
    public function testItUploadsTheImage(): void
    {
        $article = new Article();
        $image = new ArticleImage();
        $article->setImage($image);

        $uploader = $this->createMock(ImageUploaderInterface::class);
        $uploader->expects(self::once())->method('upload')->with($image)
            ->willReturnCallback(static fn (ImageInterface $image) => $image->setPath('ab/cd/image.jpg'));

        (new ImageUploadListener($uploader))->uploadImage(new GenericEvent($article));

        self::assertSame($image, $article->getImage());
    }

    public function testItKeepsAStoredImageWithoutANewFile(): void
    {
        $article = new Article();
        $image = new ArticleImage();
        $image->setPath('ab/cd/image.jpg');
        $article->setImage($image);

        (new ImageUploadListener($this->createStub(ImageUploaderInterface::class)))->uploadImage(new GenericEvent($article));

        self::assertSame($image, $article->getImage());
    }

    public function testItDropsAnImageWithNothingToShow(): void
    {
        $article = new Article();
        $article->setImage(new ArticleImage());

        (new ImageUploadListener($this->createStub(ImageUploaderInterface::class)))->uploadImage(new GenericEvent($article));

        self::assertNull($article->getImage());
    }

    public function testItIgnoresASubjectWithoutImage(): void
    {
        $uploader = $this->createMock(ImageUploaderInterface::class);
        $uploader->expects(self::never())->method('upload');

        (new ImageUploadListener($uploader))->uploadImage(new GenericEvent(new Article()));
    }
}
