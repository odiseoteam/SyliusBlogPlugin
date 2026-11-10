<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Controller\Admin;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/** Removes the image of a category or an author. Sylius deletes the file and its thumbnails. */
final class RemoveImageAction
{
    /** @param RepositoryInterface<ResourceInterface> $repository */
    public function __construct(
        private readonly RepositoryInterface $repository,
        private readonly ObjectManager $manager,
        private readonly RouterInterface $router,
        private readonly ?CsrfTokenManagerInterface $csrfTokenManager,
        private readonly string $redirectRoute,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $id = $request->attributes->getString('id');

        if (null !== $this->csrfTokenManager && !$this->csrfTokenManager->isTokenValid(
            new CsrfToken($id, $request->query->getString('_csrf_token')),
        )) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid CSRF token.');
        }

        $owner = $this->repository->find($id);
        if (!$owner instanceof ImageAwareInterface) {
            throw new NotFoundHttpException();
        }

        $owner->setImage(null);
        $this->manager->flush();

        return new RedirectResponse($this->router->generate($this->redirectRoute, ['id' => $id]));
    }
}
