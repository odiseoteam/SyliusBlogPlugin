<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Type;

use Odiseo\SyliusBlogPlugin\Content\Media\ImageFilters;
use Odiseo\SyliusBlogPlugin\Content\Media\ImageUrlResolverInterface;
use Odiseo\SyliusBlogPlugin\Controller\Admin\MediaUploadAction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** Rich text editor that saves Tiptap JSON. Without JavaScript it is a JSON textarea. */
final class ContentEditorType extends AbstractType
{
    /** Stands for the path of an image in the addresses the editor fills in. */
    private const PATH_PLACEHOLDER = '__PATH__';

    /** @param list<string> $imageMimeTypes */
    public function __construct(
        private readonly int $wordsPerMinute,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly ImageUrlResolverInterface $imageUrlResolver,
        private readonly array $imageMimeTypes,
    ) {
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        // Untyped in Symfony 6.4.
        /** @var array<string, mixed> $vars */
        $vars = $view->vars;
        $vars['words_per_minute'] = $this->wordsPerMinute;
        $vars['placeholder'] = $options['placeholder'];
        $vars['product_search_url'] = $options['product_search_url'];
        $vars['media_upload_url'] = $options['media_upload_url'];
        $vars['media_csrf_token_id'] = MediaUploadAction::CSRF_TOKEN_ID;
        $vars['image_mime_types'] = implode(',', $this->imageMimeTypes);
        $vars['media_urls'] = [
            'placeholder' => self::PATH_PLACEHOLDER,
            'image' => $this->imageUrlResolver->resolve(self::PATH_PLACEHOLDER, ImageFilters::CONTENT[640]),
            'thumbnail' => $this->imageUrlResolver->resolve(self::PATH_PLACEHOLDER, 'odiseo_blog_admin_thumbnail'),
        ];
        $view->vars = $vars;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'placeholder' => 'odiseo_blog.ui.editor.placeholder',
                'attr' => ['rows' => 12, 'class' => 'font-monospace'],
                // Where the product dialog searches; null leaves products out of the toolbar.
                'product_search_url' => fn (Options $options): string => $this->urlGenerator->generate('odiseo_blog_admin_product_search'),
                // Where images are uploaded; null leaves images and galleries out of the toolbar.
                'media_upload_url' => fn (Options $options): string => $this->urlGenerator->generate('odiseo_blog_admin_media_upload'),
            ])
            ->setAllowedTypes('placeholder', ['string', 'null'])
            ->setAllowedTypes('product_search_url', ['string', 'null'])
            ->setAllowedTypes('media_upload_url', ['string', 'null'])
        ;
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'odiseo_blog_content_editor';
    }
}
