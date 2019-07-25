<?php

namespace Odiseo\SyliusBlogPlugin\Form\Extension;

use Odiseo\BlogBundle\Form\Type\ArticleType;
use Odiseo\BlogBundle\Form\Type\ArticleUserCommentType;
use Odiseo\SyliusBlogPlugin\Model\ArticleCommentInterface;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
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

        if ($articleComment->getAuthor()) {
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
    public function getExtendedType(): string
    {
        return ArticleUserCommentType::class;
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): array
    {
        return [ArticleUserCommentType::class];
    }
}
