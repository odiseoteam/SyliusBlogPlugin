<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Odiseo\SyliusBlogPlugin\Entity\ContentMediaInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/** Stores an image uploaded from the editor and answers with its path and size. */
final class MediaUploadAction
{
    public const CSRF_TOKEN_ID = 'odiseo_blog_media';

    /** @param FactoryInterface<ContentMediaInterface> $mediaFactory */
    public function __construct(
        private readonly FactoryInterface $mediaFactory,
        private readonly ImageUploaderInterface $uploader,
        private readonly EntityManagerInterface $manager,
        private readonly ValidatorInterface $validator,
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken(self::CSRF_TOKEN_ID, $request->request->getString('_token')))) {
            return $this->error('odiseo_blog.ui.editor.invalid_token', Response::HTTP_FORBIDDEN);
        }

        $media = $this->mediaFactory->createNew();
        $file = $request->files->get('file');
        $file = $file instanceof UploadedFile ? $file : null;
        $media->setFile($file);

        $violations = $this->validator->validate($media, null, ['odiseo_blog']);
        if (0 < count($violations) || null === $file) {
            return new JsonResponse(['error' => (string) $violations->get(0)->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $size = @getimagesize($file->getPathname()) ?: null;

        $this->uploader->upload($media);
        $this->manager->persist($media);
        $this->manager->flush();

        return new JsonResponse([
            'path' => $media->getPath(),
            'width' => $size[0] ?? null,
            'height' => $size[1] ?? null,
        ], Response::HTTP_CREATED);
    }

    private function error(string $message, int $status): JsonResponse
    {
        return new JsonResponse(['error' => $this->translator->trans($message)], $status);
    }
}
