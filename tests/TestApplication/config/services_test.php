<?php

declare(strict_types=1);

use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container) {
    if (str_starts_with($container->env(), 'test')) {
        // The test application ships this import but never loads it. Located through a class so it
        // resolves from any vendor directory.
        $container->import(\dirname((string) (new \ReflectionClass(SharedStorageInterface::class))->getFileName(), 2) . '/Resources/config/services.xml');
    }
};
