## Installation

1. Run `composer require odiseoteam/sylius-blog-plugin`

2. Enable the plugin in bundles.php. This plugin need the FOSCKEditorBundle and EWZRecaptchaBundle so make sure to include
them too

```php
<?php
// config/bundles.php

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
# config/packages/_sylius.yaml
imports:
    ...

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
        _locale: ^[a-z]{2}(?:_[A-Z]{2})?$
```

5. This plugin includes an API version. If you want to use it you have to add the route

```yml
# config/routes.yaml
odiseo_sylius_blog_plugin_api:
    resource: "@OdiseoSyliusBlogPlugin/Resources/config/routing/api.yaml"
    prefix: /api/blog
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
