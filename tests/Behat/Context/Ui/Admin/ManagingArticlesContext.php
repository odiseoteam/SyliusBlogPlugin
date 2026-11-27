<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Step\Then;
use Behat\Step\When;
use Doctrine\Persistence\ObjectManager;
use Facebook\WebDriver\WebDriverKeys;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryInterface;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Odiseo\SyliusBlogPlugin\Entity\ArticleTagInterface;
use Odiseo\SyliusBlogPlugin\Entity\AuthorInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Article\ArticleFormInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Article\CreatePageInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Article\IndexPageInterface;
use Tests\Odiseo\SyliusBlogPlugin\Behat\Page\Admin\Article\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingArticlesContext implements Context
{
    public function __construct(
        private readonly CurrentPageResolverInterface $currentPageResolver,
        private readonly IndexPageInterface $indexPage,
        private readonly CreatePageInterface $createPage,
        private readonly UpdatePageInterface $updatePage,
        private readonly ObjectManager $manager,
    ) {
    }

    #[When('I want to write a new article')]
    public function iWantToWriteANewArticle(): void
    {
        $this->createPage->open();
    }

    #[When('I want to modify the :article article')]
    public function iWantToModifyTheArticle(ArticleInterface $article): void
    {
        $this->updatePage->open(['id' => $article->getId()]);
    }

    #[When('I browse articles')]
    public function iBrowseArticles(): void
    {
        $this->indexPage->open();
    }

    #[When('I specify its code as :code')]
    public function iSpecifyItsCodeAs(string $code): void
    {
        $this->createPage->specifyCode($code);
    }

    #[When('I title it :title in :localeCode')]
    public function iTitleItIn(string $title, string $localeCode): void
    {
        $this->resolveCurrentPage()->titleIt($title, $localeCode);
    }

    #[When('I set its slug to :slug in :localeCode')]
    public function iSetItsSlugToIn(string $slug, string $localeCode): void
    {
        $this->resolveCurrentPage()->setSlug($slug, $localeCode);
    }

    #[When('I generate its slug in :localeCode')]
    public function iGenerateItsSlugIn(string $localeCode): void
    {
        $this->resolveCurrentPage()->generateSlug($localeCode);
    }

    #[When('I set its excerpt to :excerpt in :localeCode')]
    public function iSetItsExcerptToIn(string $excerpt, string $localeCode): void
    {
        $this->resolveCurrentPage()->setExcerpt($excerpt, $localeCode);
    }

    #[When('I set its content in :localeCode to:')]
    public function iSetItsContentInTo(string $localeCode, PyStringNode $content): void
    {
        $this->resolveCurrentPage()->setContent($content->getRaw(), $localeCode);
    }

    #[When('I write :text in its content in :localeCode')]
    public function iWriteInItsContentIn(string $text, string $localeCode): void
    {
        $this->resolveCurrentPage()->writeInContentEditor($text, $localeCode);
    }

    #[When('I start a new paragraph in its content in :localeCode')]
    public function iStartANewParagraphInItsContentIn(string $localeCode): void
    {
        $this->resolveCurrentPage()->writeInContentEditor(WebDriverKeys::ENTER, $localeCode);
    }

    #[When('I use :tool in its content in :localeCode')]
    public function iUseInItsContentIn(string $tool, string $localeCode): void
    {
        $this->resolveCurrentPage()->useContentEditorTool($tool, $localeCode);
    }

    #[When('I add the video :url to its content in :localeCode')]
    public function iAddTheVideoToItsContentIn(string $url, string $localeCode): void
    {
        $this->resolveCurrentPage()->addVideoToContent($url, $localeCode);
    }

    #[When('I add the image :file described as :alt to its content in :localeCode')]
    public function iAddTheImageDescribedAsToItsContentIn(string $file, string $alt, string $localeCode): void
    {
        $this->resolveCurrentPage()->addImageToContent($file, $alt, $localeCode);
    }

    #[When('I add a gallery of :first and :second to its content in :localeCode')]
    public function iAddAGalleryOfToItsContentIn(string $first, string $second, string $localeCode): void
    {
        $this->resolveCurrentPage()->addGalleryToContent([$first, $second], $localeCode);
    }

    #[When('I add the product :product to its content in :localeCode')]
    public function iAddTheProductToItsContentIn(ProductInterface $product, string $localeCode): void
    {
        $this->resolveCurrentPage()->addProductToContent((string) $product->getName(), $localeCode);
    }

    #[When('I mark it as featured')]
    public function iMarkItAsFeatured(): void
    {
        $this->resolveCurrentPage()->markAsFeatured();
    }

    #[When('I make it unavailable in the :channel channel')]
    public function iMakeItUnavailableInTheChannel(ChannelInterface $channel): void
    {
        $this->resolveCurrentPage()->makeUnavailableInChannel((string) $channel->getName());
    }

    #[When('I set its main category to :category')]
    public function iSetItsMainCategoryTo(ArticleCategoryInterface $category): void
    {
        $this->resolveCurrentPage()->setMainCategory((string) $category->getName());
    }

    #[When('I add it to the :category category')]
    public function iAddItToTheCategory(ArticleCategoryInterface $category): void
    {
        $this->resolveCurrentPage()->addCategory((string) $category->getName());
    }

    #[When('I tag it with :tag')]
    public function iTagItWith(ArticleTagInterface $tag): void
    {
        $this->resolveCurrentPage()->addTag((string) $tag->getName());
    }

    #[When('I attach the :file image')]
    public function iAttachTheImage(string $file): void
    {
        $this->resolveCurrentPage()->attachImage($file);
    }

    #[When('I describe the image as :alt in :localeCode')]
    public function iDescribeTheImageAsIn(string $alt, string $localeCode): void
    {
        $this->resolveCurrentPage()->describeImage($alt, $localeCode);
    }

    #[When('I remove its image')]
    public function iRemoveItsImage(): void
    {
        $this->updatePage->removeImage();
    }

    #[When('I add the author :name')]
    public function iAddTheAuthor(string $name): void
    {
        $this->resolveCurrentPage()->addAuthor($name);
    }

    #[When('I remove the author :name')]
    public function iRemoveTheAuthor(string $name): void
    {
        $this->resolveCurrentPage()->removeAuthor($name);
    }

    #[When('I choose to see articles of the author :name')]
    public function iChooseToSeeArticlesOfTheAuthor(string $name): void
    {
        $this->indexPage->chooseAuthorFilter($name);
    }

    #[When('I add it')]
    #[When('I try to add it')]
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    #[When('I save my changes')]
    #[When('I try to save my changes')]
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    #[When('I delete the :article article')]
    public function iDeleteTheArticle(ArticleInterface $article): void
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['title' => $article->getTitle()]);
    }

    #[When('I search for :phrase')]
    public function iSearchFor(string $phrase): void
    {
        $this->indexPage->search($phrase);
    }

    #[When('I choose to see published articles')]
    public function iChooseToSeePublishedArticles(): void
    {
        $this->indexPage->chooseStateFilter('Published');
    }

    #[When('I choose to see articles of the :channel channel')]
    public function iChooseToSeeArticlesOfTheChannel(ChannelInterface $channel): void
    {
        $this->indexPage->chooseChannelFilter((string) $channel->getName());
    }

    #[When('I choose to see articles published from :from to :to')]
    public function iChooseToSeeArticlesPublishedFromTo(string $from, string $to): void
    {
        $this->indexPage->choosePublishedBetween($from, $to);
    }

    #[When('I choose to see articles of the :category category')]
    public function iChooseToSeeArticlesOfTheCategory(ArticleCategoryInterface $category): void
    {
        $this->indexPage->chooseCategoryFilter((string) $category->getName());
    }

    #[When('I filter')]
    public function iFilter(): void
    {
        $this->indexPage->filter();
    }

    #[Then('the article :title should appear in the list')]
    #[Then('I should see the article :title in the list')]
    public function theArticleShouldAppearInTheList(string $title): void
    {
        if (!$this->indexPage->isOpen()) {
            $this->indexPage->open();
        }

        Assert::true($this->indexPage->isSingleResourceOnPage(['title' => $title]));
    }

    #[Then('I should see the article :title signed by :author in the list')]
    public function iShouldSeeTheArticleSignedByInTheList(string $title, string $author): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['title' => $title, 'authors' => $author]));
    }

    #[Then('the article :title should not appear in the list')]
    public function theArticleShouldNotAppearInTheList(string $title): void
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage(['title' => $title]));
    }

    #[Then('/^I should see (\d+) articles? in the list$/')]
    public function iShouldSeeArticlesInTheList(int $count): void
    {
        Assert::same($this->indexPage->countItems(), $count);
    }

    #[Then('it should be available in the :channel channel')]
    public function itShouldBeAvailableInTheChannel(ChannelInterface $channel): void
    {
        Assert::true($this->resolveCurrentPage()->isAvailableInChannel((string) $channel->getName()));
    }

    #[Then('the :article article should be a draft')]
    public function theArticleShouldBeADraft(ArticleInterface $article): void
    {
        $article = $this->reload($article);

        Assert::same($article->getState(), ArticleInterface::STATE_DRAFT);
    }

    #[Then('the :article article should be featured')]
    public function theArticleShouldBeFeatured(ArticleInterface $article): void
    {
        $article = $this->reload($article);

        Assert::true($article->isFeatured());
    }

    #[Then('the :article article should have the slug :slug in :localeCode')]
    public function theArticleShouldHaveTheSlugIn(ArticleInterface $article, string $slug, string $localeCode): void
    {
        $article = $this->reload($article);

        Assert::same($article->getTranslation($localeCode)->getSlug(), $slug);
    }

    #[Then('the :article article should have the excerpt :excerpt in :localeCode')]
    public function theArticleShouldHaveTheExcerptIn(ArticleInterface $article, string $excerpt, string $localeCode): void
    {
        $article = $this->reload($article);

        Assert::same($article->getTranslation($localeCode)->getExcerpt(), $excerpt);
    }

    #[Then('the :article article should not be available in any channel')]
    public function theArticleShouldNotBeAvailableInAnyChannel(ArticleInterface $article): void
    {
        $article = $this->reload($article);

        Assert::true($article->getChannels()->isEmpty());
    }

    #[Then('the main category of the :article article should be :category')]
    public function theMainCategoryOfTheArticleShouldBe(ArticleInterface $article, ArticleCategoryInterface $category): void
    {
        $article = $this->reload($article);

        Assert::same($article->getMainCategory()?->getCode(), $category->getCode());
    }

    #[Then('the :article article should be in the :first and :second categories')]
    public function theArticleShouldBeInTheCategories(ArticleInterface $article, string $first, string $second): void
    {
        $article = $this->reload($article);

        $categories = array_map(static fn (ArticleCategoryInterface $category): ?string => $category->getName(), $article->getCategories()->toArray());
        sort($categories);
        $names = [$first, $second];
        sort($names);

        Assert::same($categories, $names);
    }

    #[Then('the :article article should be tagged with :tag')]
    public function theArticleShouldBeTaggedWith(ArticleInterface $article, ArticleTagInterface $tag): void
    {
        $article = $this->reload($article);

        $codes = array_map(static fn (ArticleTagInterface $tag): ?string => $tag->getCode(), $article->getTags()->toArray());

        Assert::inArray($tag->getCode(), $codes);
    }

    #[Then('the :article article should be signed by :first and :second')]
    #[Then('the :article article should be signed by :first')]
    public function theArticleShouldBeSignedBy(ArticleInterface $article, string ...$names): void
    {
        $article = $this->reload($article);

        $authors = array_map(static fn (AuthorInterface $author): ?string => $author->getName(), $article->getAuthors());

        Assert::same($authors, array_values($names), sprintf('The article is signed by "%s".', implode('", "', $authors)));
    }

    #[Then('the :article article should have an image')]
    public function theArticleShouldHaveAnImage(ArticleInterface $article): void
    {
        Assert::notNull($this->reload($article)->getImage()?->getPath());
    }

    #[Then('the :article article should have no image')]
    public function theArticleShouldHaveNoImage(ArticleInterface $article): void
    {
        Assert::null($this->reload($article)->getImage());
    }

    #[Then('the image of the :article article should be described as :alt in :localeCode')]
    public function theImageOfTheArticleShouldBeDescribedAsIn(ArticleInterface $article, string $alt, string $localeCode): void
    {
        Assert::same($this->reload($article)->getTranslation($localeCode)->getImageAlt(), $alt);
    }

    #[Then('I should be warned that the image has no alt text in :localeName')]
    public function iShouldBeWarnedThatTheImageHasNoAltTextIn(string $localeName): void
    {
        Assert::contains($this->resolveCurrentPage()->getLocalesWithoutAlt(), $localeName);
    }

    #[Then('I should not be warned that the image has no alt text')]
    public function iShouldNotBeWarnedThatTheImageHasNoAltText(): void
    {
        Assert::false($this->resolveCurrentPage()->hasMissingAltWarning());
    }

    #[Then('I should not be able to edit its code')]
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    #[Then('I should be notified that the code is already used')]
    public function iShouldBeNotifiedThatTheCodeIsAlreadyUsed(): void
    {
        Assert::same($this->resolveCurrentPage()->getCodeValidationMessage(), 'This code is already used.');
    }

    #[Then('I should be notified that the slug is already used in :localeCode')]
    public function iShouldBeNotifiedThatTheSlugIsAlreadyUsedIn(string $localeCode): void
    {
        Assert::same($this->resolveCurrentPage()->getSlugValidationMessage($localeCode), 'This slug is already used.');
    }

    #[Then('I should be notified that the content must be valid JSON in :localeCode')]
    public function iShouldBeNotifiedThatTheContentMustBeValidJsonIn(string $localeCode): void
    {
        Assert::same($this->resolveCurrentPage()->getContentValidationMessage($localeCode), 'The content is not valid JSON.');
    }

    #[Then('I should be notified that the content uses the unknown element :type in :localeCode')]
    public function iShouldBeNotifiedThatTheContentUsesTheUnknownElementIn(string $type, string $localeCode): void
    {
        Assert::same(
            $this->resolveCurrentPage()->getContentValidationMessage($localeCode),
            sprintf('The content uses an element the blog does not know: "%s".', $type),
        );
    }

    #[Then('its content in :localeCode should have :words words and take :minutes minute(s) to read')]
    public function itsContentInShouldHaveWordsAndTakeMinutesToRead(string $localeCode, string $words, string $minutes): void
    {
        Assert::same($this->resolveCurrentPage()->getContentEditorStats($localeCode), sprintf('Words: %s · Reading time: %s min', $words, $minutes));
    }

    #[Then('the :article article should have this content in :localeCode:')]
    public function theArticleShouldHaveThisContentIn(ArticleInterface $article, string $localeCode, PyStringNode $content): void
    {
        Assert::same($this->reload($article)->getTranslation($localeCode)->getContent(), $content->getRaw());
    }

    #[Then('the content of the :article article should show an image described as :alt in :localeCode')]
    public function theContentOfTheArticleShouldShowAnImageDescribedAsIn(ArticleInterface $article, string $alt, string $localeCode): void
    {
        $images = $this->contentNodes($article, $localeCode, 'image');
        Assert::count($images, 1);
        Assert::same($images[0]['attrs']['alt'] ?? null, $alt);
        $path = $images[0]['attrs']['path'] ?? null;
        Assert::string($path);
        Assert::regex($path, '~^[\w-]+(?:/[\w-]+)*\.\w+$~');
    }

    #[Then('the content of the :article article should show a gallery of :count images in :localeCode')]
    public function theContentOfTheArticleShouldShowAGalleryOfImagesIn(ArticleInterface $article, int $count, string $localeCode): void
    {
        $galleries = $this->contentNodes($article, $localeCode, 'gallery');
        Assert::count($galleries, 1);
        $images = $galleries[0]['attrs']['images'] ?? null;
        Assert::isArray($images);
        Assert::count($images, $count);
    }

    /** @return list<array{attrs?: array<string, mixed>}> The top-level nodes of a type in the saved content. */
    private function contentNodes(ArticleInterface $article, string $localeCode, string $type): array
    {
        $document = json_decode((string) $this->reload($article)->getTranslation($localeCode)->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        Assert::isArray($document);
        /** @var list<array{type?: string, attrs?: array<string, mixed>}> $nodes */
        $nodes = $document['content'] ?? [];

        return array_values(array_filter($nodes, static fn (array $node): bool => $type === ($node['type'] ?? null)));
    }

    private function reload(ArticleInterface $article): ArticleInterface
    {
        $this->manager->clear();

        $reloaded = $this->manager->find($article::class, $article->getId());
        Assert::isInstanceOf($reloaded, ArticleInterface::class);

        return $reloaded;
    }

    private function resolveCurrentPage(): ArticleFormInterface
    {
        $page = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);
        Assert::isInstanceOf($page, ArticleFormInterface::class);

        return $page;
    }
}
