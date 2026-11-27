<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Type;

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
    public function __construct(
        private readonly int $wordsPerMinute,
        private readonly UrlGeneratorInterface $urlGenerator,
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
            ])
            ->setAllowedTypes('placeholder', ['string', 'null'])
            ->setAllowedTypes('product_search_url', ['string', 'null'])
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
