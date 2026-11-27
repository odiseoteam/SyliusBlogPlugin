<?php

declare(strict_types=1);

use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Tests\Odiseo\SyliusBlogPlugin\Controller\ArticlePageController;

return function (ContainerConfigurator $container) {
    $container->services()
        ->set(ArticlePageController::class)
        ->public()
        ->args([new Reference('doctrine.orm.entity_manager'), new Reference('twig'), '%odiseo_blog.model.article.class%'])
        ->tag('controller.service_arguments');

    if (str_starts_with($container->env(), 'test')) {
        // The test application ships this import but never loads it. Located through a class so it
        // resolves from any vendor directory.
        $container->import(\dirname((string) (new \ReflectionClass(SharedStorageInterface::class))->getFileName(), 2) . '/Resources/config/services.xml');
        $container->import('@OdiseoSyliusBlogPlugin/tests/Behat/Resources/services.xml');
    }
};
