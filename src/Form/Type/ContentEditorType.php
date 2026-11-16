<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** Rich text editor that saves Tiptap JSON. Without JavaScript it is a JSON textarea. */
final class ContentEditorType extends AbstractType
{
    public function __construct(private readonly int $wordsPerMinute)
    {
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['words_per_minute'] = $this->wordsPerMinute;
        $view->vars['placeholder'] = $options['placeholder'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'placeholder' => 'odiseo_blog.ui.editor.placeholder',
                'attr' => ['rows' => 12, 'class' => 'font-monospace'],
            ])
            ->setAllowedTypes('placeholder', ['string', 'null'])
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
