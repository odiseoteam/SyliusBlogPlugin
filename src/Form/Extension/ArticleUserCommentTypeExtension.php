<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Extension;

use Odiseo\BlogBundle\Form\Type\ArticleUserCommentType;
use Odiseo\SyliusBlogPlugin\Entity\ArticleCommentInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ArticleUserCommentTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        /** @var ArticleCommentInterface $articleComment */
        $articleComment = $builder->getData();

        if ($articleComment->getAuthor() instanceof ShopUserInterface) {
            $builder
                ->remove('name')
                ->remove('email')
                ->remove('recaptcha')
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [ArticleUserCommentType::class];
    }
}
