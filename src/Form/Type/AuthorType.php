<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Type;

use Odiseo\SyliusBlogPlugin\Entity\AuthorInterface;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class AuthorType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $author = $builder->getData();

        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('name', TextType::class, [
                'label' => 'sylius.ui.name',
            ])
            ->add('slug', TextType::class, [
                'label' => 'sylius.ui.slug',
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'sylius.ui.enabled',
                'required' => false,
            ])
            ->add('adminUser', AdminUserAutocompleteType::class, [
                'label' => 'odiseo_blog.form.author.admin_user',
                'help' => 'odiseo_blog.form.author.admin_user_help',
                'required' => false,
                'extra_options' => ['author_id' => $author instanceof AuthorInterface ? $author->getId() : null],
            ])
            ->add('image', AuthorImageType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('socialLinks', LiveCollectionType::class, [
                'entry_type' => AuthorSocialLinkType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'delete_empty' => true,
                'button_add_options' => [
                    'label' => 'odiseo_blog.form.author.add_social_link',
                ],
                'button_delete_options' => [
                    'label' => 'sylius.ui.delete',
                ],
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => AuthorTranslationType::class,
                'label' => 'sylius.ui.translations',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'odiseo_blog_author';
    }
}
