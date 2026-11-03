<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/ecs.php',
    ]);

    $ecsConfig->skip([
        __DIR__ . '/tests/TestApplication',
    ]);

    $ecsConfig->import('vendor/sylius-labs/coding-standard/ecs.php');
};
