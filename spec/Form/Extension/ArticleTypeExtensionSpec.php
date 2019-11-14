<?php

declare(strict_types=1);

namespace spec\Odiseo\SyliusBlogPlugin\Form\Extension;

use Odiseo\BlogBundle\Form\Type\ArticleType;
use Odiseo\SyliusBlogPlugin\Form\Extension\ArticleTypeExtension;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ArticleTypeExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ArticleTypeExtension::class);
    }

    function it_should_be_abstract_type_extension_object()
    {
        $this->shouldHaveType(AbstractTypeExtension::class);
    }

    function it_build_form_with_proper_fields(
        FormBuilderInterface $builder
    ) {
        $builder->add('channels', ChannelChoiceType::class, Argument::any())->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, []);
    }

    function it_has_extended_type()
    {
        $this->getExtendedType()->shouldReturn(ArticleType::class);
    }
}
