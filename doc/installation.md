## Installation

1. Run `composer require odiseoteam/sylius-blog-plugin --no-scripts`

2. Enable the plugin in bundles.php. This plugin need the FOSCKEditorBundle and EWZRecaptchaBundle so make sure to include
them too. The bundle must be enabled before SyliusResourceBundle (i.e. before `Sylius\Bundle\ResourceBundle\SyliusResourceBundle::class => ['all' => true],`).

```php
<?php
// config/bundles.php

return [
    // ...
    Odiseo\BlogBundle\OdiseoBlogBundle::class => ['all' => true],
    FOS\CKEditorBundle\FOSCKEditorBundle::class => ['all' => true],
    EWZ\Bundle\RecaptchaBundle\EWZRecaptchaBundle::class => ['all' => true],
    Odiseo\SyliusBlogPlugin\OdiseoSyliusBlogPlugin::class => ['all' => true],
    // ...
];
```

3. Import the plugin configurations

```yml
# config/packages/_sylius.yaml
imports:
    # ...
    - { resource: "@OdiseoSyliusBlogPlugin/Resources/config/config.yaml" }
```

4. Add the shop and admin routes

```yml
# config/routes.yaml
odiseo_sylius_blog_plugin_admin:
    resource: "@OdiseoSyliusBlogPlugin/Resources/config/routing/admin.yaml"
    prefix: /admin/blog

odiseo_sylius_blog_plugin_shop:
    resource: "@OdiseoSyliusBlogPlugin/Resources/config/routing/shop.yaml"
    prefix: /{_locale}/blog
    requirements:
        _locale: ^[A-Za-z]{2,4}(_([A-Za-z]{4}|[0-9]{3}))?(_([A-Za-z]{2}|[0-9]{3}))?$
```

5. Because this plugin uses FOSCKeditorBundle you need to execute the following commands according to the bundle [installation](https://symfony.com/doc/current/bundles/FOSCKEditorBundle/installation.html)

```
php bin/console ckeditor:install
php bin/console assets:install public
```

6. Finish the installation updating the database schema and installing assets

```
php bin/console doctrine:migrations:migrate
php bin/console sylius:theme:assets:install
php bin/console cache:clear
```
