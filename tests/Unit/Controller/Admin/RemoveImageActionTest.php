<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Unit\Controller\Admin;

use Doctrine\Persistence\ObjectManager;
use Odiseo\SyliusBlogPlugin\Controller\Admin\RemoveImageAction;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategory;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCategoryImage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[CoversClass(RemoveImageAction::class)]
final class RemoveImageActionTest extends TestCase
{
    public function testItRemovesTheImageAndRedirectsToTheOwner(): void
    {
        $category = new ArticleCategory();
        $category->setImage(new ArticleCategoryImage());

        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::once())->method('flush');

        $response = $this->action($category, $manager, true)(new Request([], [], ['id' => '7']));

        self::assertNull($category->getImage());
        self::assertSame('/edit/7', $response->headers->get('Location'));
    }

    public function testAnInvalidCsrfTokenIsRejected(): void
    {
        $category = new ArticleCategory();
        $category->setImage(new ArticleCategoryImage());

        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::never())->method('flush');

        try {
            $this->action($category, $manager, false)(new Request(['_csrf_token' => 'nope'], [], ['id' => '7']));
            self::fail('The request should have been rejected.');
        } catch (HttpException $exception) {
            self::assertSame(403, $exception->getStatusCode());
        }

        self::assertNotNull($category->getImage());
    }

    public function testAnUnknownOwnerIsNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->action(null, $this->createStub(ObjectManager::class), true)(new Request([], [], ['id' => '7']));
    }

    private function action(?ImageAwareInterface $owner, ObjectManager $manager, bool $validToken): RemoveImageAction
    {
        /** @var RepositoryInterface<ResourceInterface>&Stub $repository */
        $repository = $this->createStub(RepositoryInterface::class);
        $repository->method('find')->willReturn($owner);

        $router = $this->createStub(RouterInterface::class);
        $router->method('generate')->willReturnCallback(static fn (string $route): string => '/' . $route . '/7');

        $tokens = $this->createStub(CsrfTokenManagerInterface::class);
        $tokens->method('isTokenValid')->willReturnCallback(static fn (CsrfToken $token): bool => $validToken);

        return new RemoveImageAction($repository, $manager, $router, $tokens, 'edit');
    }
}
