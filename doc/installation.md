[Docs index](README.md)

# Installation

## 1. Require the plugin

```bash
composer require odiseoteam/sylius-blog-plugin
```

## 2. Enable it

```php
// config/bundles.php
return [
    // ...
    Odiseo\SyliusBlogPlugin\OdiseoSyliusBlogPlugin::class => ['all' => true],
];
```

## 3. Import the configuration

```yaml
# config/packages/_sylius.yaml
imports:
    # ...
    - { resource: "@OdiseoSyliusBlogPlugin/config/config.yaml" }
```

## 4. Import the routes

```yaml
# config/routes/odiseo_sylius_blog.yaml
odiseo_blog_admin:
    resource: "@OdiseoSyliusBlogPlugin/config/routes/admin.yaml"
    prefix: '/%sylius_admin.path_name%'
```

## 5. Install the editor

The content editor is a Stimulus controller with [Tiptap](https://tiptap.dev). Like Sylius' own admin
controllers, it ships as an npm package that your application depends on locally.

```json
// package.json
{
    "dependencies": {
        "@odiseoteam/sylius-blog-plugin": "file:vendor/odiseoteam/sylius-blog-plugin/assets/admin"
    }
}
```

```json
// assets/admin/controllers.json
{
    "controllers": {
        "@odiseoteam/sylius-blog-plugin": {
            "editor": {
                "enabled": true,
                "fetch": "lazy",
                "autoimport": {
                    "@odiseoteam/sylius-blog-plugin/styles/editor.css": true
                }
            }
        }
    }
}
```

```bash
yarn install --force
yarn build
```

Skip this step and the content is still typed as Tiptap JSON. See [the editor](editor.md).

## 6. Run the migrations

```bash
bin/console doctrine:migrations:migrate
```

The plugin registers its own migrations, for MySQL and PostgreSQL, so there is nothing to
configure. If your application does not use `sylius-labs/doctrine-migrations-extra-bundle`,
register them yourself:

```yaml
# config/packages/doctrine_migrations.yaml
doctrine_migrations:
    migrations_paths:
        # ...
        'Odiseo\SyliusBlogPlugin\Migrations': '%kernel.project_dir%/vendor/odiseoteam/sylius-blog-plugin/src/Migrations'
```

## Coming from 1.x

The migrations keep the 1.x data: they rename its tables to `odiseo_blog_legacy_*` before creating
the new ones. Doctrine's schema tools ignore those tables, so `doctrine:migrations:diff` never drops
them. The 1.x images are already in Sylius' image storage, `public/media/image`, and stay there.

Doctrine warns that the 1.x migration is executed but no longer exists. Remove it from the
migrations table:

```bash
bin/console doctrine:migrations:version 'Odiseo\SyliusBlogPlugin\Migrations\Version20240309220727' --delete
```
