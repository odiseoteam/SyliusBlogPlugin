<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Odiseo\SyliusBlogPlugin\Entity\ArticleInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

/** A minimal article page of the shop, to see the content drawn until the blog has its own pages. */
final class ArticlePageController
{
    /** @param class-string<ArticleInterface> $articleClass */
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly Environment $twig,
        private readonly string $articleClass,
    ) {
    }

    public function __invoke(Request $request, string $slug): Response
    {
        $article = $this->manager->createQueryBuilder()
            ->select('article')
            ->from($this->articleClass, 'article')
            ->innerJoin('article.translations', 'translation', 'WITH', 'translation.locale = :locale AND translation.slug = :slug')
            ->setParameter('locale', $request->getLocale())
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
        if (!$article instanceof ArticleInterface) {
            throw new NotFoundHttpException();
        }

        return new Response($this->twig->render('@BlogTestApplication/article/show.html.twig', ['article' => $article]));
    }
}
