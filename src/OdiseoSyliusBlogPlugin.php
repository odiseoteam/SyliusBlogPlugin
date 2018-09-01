<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\ResourceBundleInterface;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

/**
 * @author Diego D'amico <diego@odiseo.com.ar>
 */
final class OdiseoSyliusBlogPlugin extends AbstractResourceBundle
{
    use SyliusPluginTrait;

    protected $mappingFormat = ResourceBundleInterface::MAPPING_YAML;

    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace(): ?string
    {
        return 'Odiseo\SyliusBlogPlugin\Model';
    }
}
