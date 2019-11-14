<h1 align="center">
    <a href="https://odiseo.com.ar/" target="_blank" title="Odiseo">
        <img src="https://github.com/odiseoteam/SyliusBlogPlugin/blob/master/sylius-blog-plugin.png" alt="Sylius Blog Plugin" />
    </a>
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
    <p align="center"><a href="https://sylius.com/plugins/" target="_blank"><img src="https://sylius.com/assets/badge-approved-by-sylius.png" width="100"></a></p>
</h1>

## Description

This is a Sylius Plugin that add blog features to your store. It uses the [OdiseoBlogBundle](https://github.com/odiseoteam/OdiseoBlogBundle) Symfony bundle.

Features:

* Articles and article categories, both translatables.

* Multi images for articles.

* This plugin comes with fixtures.

* Comments and comment replies. Comments can be made as ShopUser or as guest.

Support Sylius version 1.3+.

<img src="https://github.com/odiseoteam/SyliusBlogPlugin/blob/master/screenshot_1.png" alt="Blog admin" width="80%">
<img src="https://github.com/odiseoteam/SyliusBlogPlugin/blob/master/screenshot_2.png" alt="Blog admin" width="80%">

## Demo

You can see this plugin in action in our Sylius Demo application.

- Frontend: [sylius-demo.odiseo.com.ar](https://sylius-demo.odiseo.com.ar).
- Administration: [sylius-demo.odiseo.com.ar/admin](https://sylius-demo.odiseo.com.ar/admin) with `odiseo: odiseo` credentials.

## Installation

1. Run `composer require odiseoteam/sylius-blog-plugin`

2. Enable the plugin in bundles.php. This plugin need the FOSCKEditorBundle and EWZRecaptchaBundle so make sure to include
them too

```php
<?php

return [
    // ...
    Odiseo\BlogBundle\OdiseoBlogBundle::class => ['all' => true],
    FOS\CKEditorBundle\FOSCKEditorBundle::class => ['all' => true],
    EWZ\Bundle\RecaptchaBundle\EWZRecaptchaBundle::class => ['all' => true],
    Odiseo\SyliusBlogPlugin\OdiseoSyliusBlogPlugin::class => ['all' => true],
];
```

3. Import the plugin configurations

```yml
imports:
    - { resource: "@OdiseoSyliusBlogPlugin/Resources/config/config.yaml" }
```

4. Add the shop and admin routes

```yml
odiseo_sylius_blog_plugin_admin:
    resource: "@OdiseoSyliusBlogPlugin/Resources/config/routing/admin.yaml"
    prefix: /admin
    
odiseo_sylius_blog_plugin_shop:
    resource: "@OdiseoSyliusBlogPlugin/Resources/config/routing/shop.yaml"
    prefix: /{_locale}/blog
    requirements:
        _locale: ^[a-z]{2}(?:_[A-Z]{2})?$
```

5. Because this plugin uses FOSCKeditorBundle you need to execute the following commands according to the bundle [installation](https://symfony.com/doc/current/bundles/FOSCKEditorBundle/installation.html)

```
php bin/console ckeditor:install
php bin/console assets:install public
```

6. Finish the installation updating the database schema and installing assets

```
php bin/console doctrine:schema:update --force
php bin/console sylius:theme:assets:install
```

## Usage

For the administration you will have the Blog menu. And for the frontend you can go to the /{locale}/blog to see the blog articles. 
Feel free to modify the plugin templates like you want.

### Partial routes

To render a list of latest articles you can do something like this:

```twig
{{ render(url('odiseo_sylius_blog_shop_partial_article_index_latest', {'count': 4, 'template': '@OdiseoSyliusBlogPlugin/Shop/Article/_latest.html.twig'})) }}
``` 

And to render a list of categories:

```twig
{{ render(url('odiseo_sylius_blog_shop_partial_article_category_index', {'template': '@OdiseoSyliusBlogPlugin/Shop/ArticleCategory/_verticalMenu.html.twig'})) }}
``` 

## Fixtures

This plugin comes with fixtures:

### Blog

Simply add this configuration on your fixture suite:

```yml
blog:
    options:
        articles_per_channel: 12
```

## Test the plugin

You can follow the instructions to test this plugins in the proper documentation page: [Test the plugin](doc/tests.md).

## Credits

This plugin is maintained by <a href="https://odiseo.com.ar">Odiseo</a>. Want us to help you with this plugin or any Sylius project? Contact us on <a href="mailto:team@odiseo.com.ar">team@odiseo.com.ar</a>.
