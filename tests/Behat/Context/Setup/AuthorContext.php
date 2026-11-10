<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Doctrine\Persistence\ObjectManager;
use Odiseo\SyliusBlogPlugin\Entity\ArticleAuthor;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Odiseo\SyliusBlogPlugin\Entity\AuthorImage;
use Odiseo\SyliusBlogPlugin\Entity\AuthorInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\Assert\Assert;

final class AuthorContext implements Context
{
    /**
     * @param FactoryInterface<AuthorInterface> $authorFactory
     * @param FactoryInterface<ArticleInterface> $articleFactory
     * @param array<string, mixed>|\ArrayAccess<string, mixed> $minkParameters
     */
    public function __construct(
        private readonly FactoryInterface $authorFactory,
        private readonly FactoryInterface $articleFactory,
        private readonly ObjectManager $manager,
        private readonly SlugGeneratorInterface $slugGenerator,
        private readonly ImageUploaderInterface $imageUploader,
        private readonly array|\ArrayAccess $minkParameters,
        private readonly SharedStorageInterface $sharedStorage,
    ) {
    }

    #[Given('the blog has an author :name')]
    public function theBlogHasAnAuthor(string $name): void
    {
        $this->save($this->author($name));
    }

    #[Given('the blog has an author :name linked to my account')]
    public function theBlogHasAnAuthorLinkedToMyAccount(string $name): void
    {
        $administrator = $this->sharedStorage->get('administrator');
        Assert::isInstanceOf($administrator, AdminUserInterface::class);

        $author = $this->author($name);
        $author->setAdminUser($administrator);

        $this->save($author);
    }

    #[Given('the blog has an author :name with the links :first and :second')]
    #[Given('the blog has an author :name with the link :first')]
    public function theBlogHasAnAuthorWithTheLinks(string $name, string ...$links): void
    {
        $author = $this->author($name);
        $author->setSocialLinks(array_map(static function (string $link): array {
            [$network, $url] = explode(': ', $link, 2);

            return ['network' => $network, 'url' => $url];
        }, array_values($links)));

        $this->save($author);
    }

    #[Given('the blog has an author :name with the :file avatar')]
    public function theBlogHasAnAuthorWithTheAvatar(string $name, string $file): void
    {
        $author = $this->author($name);

        $filesPath = $this->minkParameters['files_path'];
        Assert::string($filesPath);
        $image = new AuthorImage();
        $image->setFile(new UploadedFile($filesPath . $file, $file, null, null, true));
        $this->imageUploader->upload($image);
        $author->setImage($image);

        $this->save($author);
    }

    #[Given('the author :author signs the article :title')]
    public function theAuthorSignsTheArticle(AuthorInterface $author, string $title): void
    {
        $article = $this->articleFactory->createNew();
        $slug = $this->slugGenerator->generate($title);
        $article->setCode(strtoupper(str_replace('-', '_', $slug)));
        $article->setTitle($title);
        $article->setSlug($slug);

        $byline = new ArticleAuthor();
        $byline->setAuthor($author);
        $byline->setArticle($article);
        $article->addArticleAuthor($byline);

        $this->manager->persist($article);
        $this->manager->flush();
    }

    private function author(string $name): AuthorInterface
    {
        $slug = $this->slugGenerator->generate($name);
        $author = $this->authorFactory->createNew();
        $author->setCode(strtoupper(str_replace('-', '_', $slug)));
        $author->setName($name);
        $author->setSlug($slug);

        return $author;
    }

    private function save(AuthorInterface $author): void
    {
        $this->manager->persist($author);
        $this->manager->flush();
    }
}
