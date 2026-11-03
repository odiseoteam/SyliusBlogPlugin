<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

// Components pulled in by the sylius/sylius umbrella are not re-declared.
return (new Configuration())
    ->ignoreErrors([ErrorType::SHADOW_DEPENDENCY])
;
