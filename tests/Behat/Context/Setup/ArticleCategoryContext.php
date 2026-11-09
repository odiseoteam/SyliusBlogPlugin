<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Doctrine\Persistence\ObjectManager;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryImage;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryInterface;
use Odiseo\SyliusBlogPlugin\Factory\ArticleCategoryFactoryInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\Assert\Assert;

final class ArticleCategoryContext implements Context
{
    /** @param array<string, mixed>|\ArrayAccess<string, mixed> $minkParameters */
    public function __construct(
        private readonly ArticleCategoryFactoryInterface $categoryFactory,
        private readonly ObjectManager $manager,
        private readonly SlugGeneratorInterface $slugGenerator,
        private readonly ImageUploaderInterface $imageUploader,
        private readonly array|\ArrayAccess $minkParameters,
    ) {
    }

    #[Given('the blog has a category :name')]
    public function theBlogHasACategory(string $name): void
    {
        $this->save($this->category($this->categoryFactory->createNew(), $name));
    }

    #[Given('the blog has a category :name with the :file image')]
    public function theBlogHasACategoryWithTheImage(string $name, string $file): void
    {
        $category = $this->category($this->categoryFactory->createNew(), $name);

        $filesPath = $this->minkParameters['files_path'];
        Assert::string($filesPath);
        $path = $filesPath . $file;
        $image = new ArticleCategoryImage();
        $image->setFile(new UploadedFile($path, $file, null, null, true));
        $this->imageUploader->upload($image);
        $category->setImage($image);

        $this->save($category);
    }

    #[Given('the :category category has a subcategory :name')]
    public function theCategoryHasASubcategory(ArticleCategoryInterface $category, string $name): void
    {
        $this->save($this->category($this->categoryFactory->createForParent($category), $name));
    }

    private function category(ArticleCategoryInterface $category, string $name): ArticleCategoryInterface
    {
        $slug = $this->slugGenerator->generate($name);
        $category->setCode(strtoupper(str_replace('-', '_', $slug)));
        $category->setName($name);
        $category->setSlug($slug);

        return $category;
    }

    private function save(ArticleCategoryInterface $category): void
    {
        $this->manager->persist($category);
        $this->manager->flush();
    }
}
