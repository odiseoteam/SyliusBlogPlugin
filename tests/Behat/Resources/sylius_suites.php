<?php

declare(strict_types=1);

use Behat\Config\Config;

// Sylius 2.3 ships its suites as PHP; earlier versions, as YAML.
$suites = dirname(__DIR__, 3) . '/vendor/sylius/sylius/src/Sylius/Behat/Resources/config/suites';

return (new Config())->import(file_exists($suites . '.php') ? $suites . '.php' : $suites . '.yml');
