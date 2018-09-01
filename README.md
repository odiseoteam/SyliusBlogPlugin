<h1 align="center">
    <a href="https://odiseo.com.ar/" target="_blank" title="Odiseo">
        <img src="https://github.com/odiseoteam/SyliusBlogPlugin/blob/master/logo_odiseo.png" alt="Odiseo" width="300px" />
    </a>
    <br />
    Odiseo Sylius Blog Plugin
    <br />
    <a href="https://packagist.org/packages/odiseoteam/sylius-blog-plugin" title="License" target="_blank">
        <img src="https://img.shields.io/packagist/l/odiseoteam/sylius-blog-plugin.svg" />
    </a>
    <a href="https://packagist.org/packages/odiseoteam/sylius-blog-plugin" title="Version" target="_blank">
        <img src="https://img.shields.io/packagist/v/odiseoteam/sylius-blog-plugin.svg" />
    </a>
    <a href="http://travis-ci.org/odiseoteam/SyliusBlogPlugin" title="Build status" target="_blank">
        <img src="https://img.shields.io/travis/odiseoteam/SyliusBlogPlugin/master.svg" />
    </a>
    <a href="https://scrutinizer-ci.com/g/odiseoteam/SyliusBlogPlugin/" title="Scrutinizer" target="_blank">
        <img src="https://img.shields.io/scrutinizer/g/odiseoteam/SyliusBlogPlugin.svg" />
    </a>
    <a href="https://packagist.org/packages/odiseoteam/sylius-blog-plugin" title="Total Downloads" target="_blank">
        <img src="https://poser.pugx.org/odiseoteam/sylius-blog-plugin/downloads" />
    </a>
</h1>

## Description

This plugin add blog features the Sylius ecommerce framework. It uses the [OdiseoBlogBundle](https://github.com/odiseoteam/OdiseoBlogBundle) Symfony bundle.

<img src="https://github.com/odiseoteam/SyliusBlogPlugin/blob/master/screenshot_1.png" alt="Blog admin" width="80%">
<img src="https://github.com/odiseoteam/SyliusBlogPlugin/blob/master/screenshot_2.png" alt="Blog admin" width="80%">

## Demo

You can see this plugin in action in our Sylius Demo application.

- Frontend: [sylius-demo.odiseo.com.ar](https://sylius-demo.odiseo.com.ar). 
- Administration: [sylius-demo.odiseo.com.ar/admin](https://sylius-demo.odiseo.com.ar/admin) with `odiseo: odiseo` credentials.

## Installation

1. Run `composer require odiseoteam/sylius-blog-plugin`.

2. Add the plugin and the OdiseoBlogBundle to the AppKernel but add it before SyliusResourceBundle. To do that you need change the registerBundles.
The OdiseoBlogBundle uses FOSCKeditorBundle so you need add it to the kernel too.

```php
public function registerBundles(): array
{
    $preResourceBundles = [
        new \Odiseo\BlogBundle\OdiseoBlogBundle(),
        new \Odiseo\SyliusBlogPlugin\OdiseoSyliusBlogPlugin(),
    ];

    $bundles = [
        ...
        new \FOS\CKEditorBundle\FOSCKEditorBundle(),
    ];

    return array_merge($preResourceBundles, parent::registerBundles(), $bundles);
}
```
 
3. Import the configurations on your config.yml:
 
```yml
    - { resource: "@OdiseoSyliusBlogPlugin/Resources/config/config.yml" }
```

5. Add the routes:

```yml
odiseo_sylius_blog_plugin_admin:
    resource: "@OdiseoSyliusBlogPlugin/Resources/config/routing/admin.yml"
    prefix: /admin
    
odiseo_sylius_blog_plugin_shop:
    resource: "@OdiseoSyliusBlogPlugin/Resources/config/routing/shop.yml"
    prefix: /{_locale}/blog
```

6. Finish the installation updatating the database schema and installing assets:
   
```
php bin/console doctrine:schema:update --force
php bin/console assets:install
php bin/console sylius:theme:assets:install
```

## Test the plugin

You can follow the instructions to test this plugins in the proper documentation page: [Test the plugin](doc/tests.md).
    
## Credits

This plugin is maintained by <a href="https://odiseo.com.ar">Odiseo</a>. Want us to help you with this plugin or any Sylius project? Contact us on <a href="mailto:team@odiseo.com.ar">team@odiseo.com.ar</a>.
