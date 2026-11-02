# Changelog

## [Unreleased]

Version 2.0 is a rewrite for Sylius 2.0–2.3, developed on `master`. 1.x stays on the `1.4` branch.

### Added

- Sylius 2 plugin skeleton: `config/`, `templates/` and `translations/` at the root, configuration root `odiseo_sylius_blog`, and empty `assets/admin` and `assets/shop` entrypoints.

### Changed

- Requires PHP 8.2+, Sylius 2.0+ and Symfony 6.4 or 7.4.

### Removed

- The 1.x code: entities and repository built on `odiseoteam/blog-bundle`, Sonata blocks, form extensions, Semantic UI templates and the 1.x migration.
- The `odiseoteam/blog-bundle`, `symfony/webpack-encore-bundle`, phpspec and Psalm dependencies.
- The 1.x test application, Behat suites, phpspec specs, docs and screenshots.
