# Changelog

## [Unreleased]

Version 2.0 is a rewrite for Sylius 2.0–2.3, developed on `master`. 1.x stays on the `1.4` branch.

### Added

- Sylius 2 plugin skeleton: `config/`, `templates/` and `translations/` at the root, configuration root `odiseo_sylius_blog`, and empty `assets/admin` and `assets/shop` entrypoints.
- Data model, as Sylius resources: articles (translations, channels, categories with a main one, tags and ordered authors), a category tree, tags and authors (optionally linked to an admin user). Article content is stored as Tiptap JSON, exactly as sent.
- MySQL and PostgreSQL migrations. Coming from 1.x, they rename the old tables to `odiseo_blog_legacy_*`, which Doctrine's schema tools ignore.
- Validation: unique codes, slugs unique per locale, and a main category that is one of the article's categories. Messages in English and Spanish.
- Images: a featured image per article, with its alt text per locale, a category image and an author avatar. They are stored with Sylius' image uploader and storage, and come with their own LiipImagine filter sets, a local placeholder, and the `odiseo_blog_image_url` and `odiseo_blog_image_alt` Twig functions.
- Installation guide in `doc/`.

### Changed

- Requires PHP 8.2+, Sylius 2.0+ and Symfony 6.4 or 7.4.

### Removed

- The 1.x code: entities and repository built on `odiseoteam/blog-bundle`, Sonata blocks, form extensions, Semantic UI templates and the 1.x migration.
- The `odiseoteam/blog-bundle`, `symfony/webpack-encore-bundle`, phpspec and Psalm dependencies.
- The 1.x test application, Behat suites, phpspec specs, docs and screenshots.
