# Changelog

## [Unreleased]

Version 2.0 is a rewrite for Sylius 2.0–2.3, developed on `master`. 1.x stays on the `1.4` branch.

### Added

- Sylius 2 plugin skeleton: `config/`, `templates/` and `translations/` at the root, configuration root `odiseo_sylius_blog`, and empty `assets/admin` and `assets/shop` entrypoints.
- Data model, as Sylius resources: articles (translations, channels, categories with a main one, tags and ordered authors), a category tree, tags and authors (optionally linked to an admin user). Article content is stored as Tiptap JSON, exactly as sent.
- MySQL and PostgreSQL migrations. Coming from 1.x, they rename the old tables to `odiseo_blog_legacy_*`, which Doctrine's schema tools ignore.
- Validation: unique codes, slugs unique per locale, and a main category that is one of the article's categories. Messages in English and Spanish.
- Images: a featured image per article, with its alt text per locale, a category image and an author avatar. They are stored with Sylius' image uploader and storage, and come with their own LiipImagine filter sets, a local placeholder, and the `odiseo_blog_image_url` and `odiseo_blog_image_alt` Twig functions.
- Blog configuration per channel, in the admin under Blog › Configuration: whether the blog is shown, articles per page, and a title and description per locale. Channels without one use the defaults in `odiseo_sylius_blog.channel_defaults`.
- Articles admin, under Blog › Articles: a list with search and filters by state, channel, category, author and publication date, and a form with an image (warning when its alt text is missing in a language of the article), authors in byline order (a new article starts signed by the author linked to the administrator), channels (all checked on a new article), categories, tags, and a title, slug, excerpt, content and meta per locale. The main category is added to the categories when saving.
- Authors admin, under Blog › Authors: name and slug (generated from the name), avatar, social links from a configurable list of networks, an optional administrator, and job title and bio per locale. An author that signs articles can only be disabled.
- Categories admin, under Blog › Categories: a tree next to the form, to create, edit, reorder and delete categories, with their image and a name, slug and meta per locale.
- Tags admin, under Blog › Tags: tags with a name, slug and meta per locale. A button generates the slug from the name.
- Content pipeline (`Odiseo\SyliusBlogPlugin\Content`): the Tiptap JSON of an article is validated when saved and drawn as HTML (Twig template per node) or Markdown; the plain text, the table of contents and the word count are derived from it. It holds text, lists, quotes, code, tables, links, YouTube and Vimeo videos, and an HTML node sanitized with Symfony's HTML sanitizer and limited to allowed iframe hosts. Your own nodes and marks are tagged `odiseo_blog.content_node` and `odiseo_blog.content_mark`. Limits in `odiseo_sylius_blog.content`. Saving a translation stores its `plainText` and `wordCount`.
- Reading time: `odiseo_blog_reading_time(article)` gives the minutes to read an article at `odiseo_sylius_blog.content.words_per_minute` (200 by default).
- Content editor: Tiptap, with a toolbar of Sylius buttons and menus, a dialog for links, YouTube and Vimeo videos and HTML blocks, and the word count and reading time as you write. It ships as the npm package in `assets/admin` (`@odiseoteam/sylius-blog-plugin`). Without JavaScript, or with content it cannot open, the content is typed as JSON. `ContentEditorType` brings it to other forms, and the `odiseo-blog-editor:options` event adds your own Tiptap extensions.
- The icons of the plugin ship with it, so they don't depend on Iconify.
- Products in the content: a `product` node that keeps the product code and is drawn with the product card of the shop while the product is enabled in the channel. The products of a content are loaded together before it is drawn (`PreloadingNodeInterface` lets your own nodes do the same). The editor picks it with a search of the catalog by name or code, and `EmbeddedProductsExtractor` lists the products of a content. `odiseo_blog_content(article)` draws the content in templates.
- Images in the content: `image` and `gallery` nodes for files uploaded from the editor, with alt text (marked in the editor when it is missing), a caption, a size or a number of columns. They are drawn with a `srcset` of new LiipImagine filter sets, lazy loading and their dimensions. Images can also be pasted or dropped on the text. Uploads are kept as `odiseo_blog.content_media`, and `odiseo:blog:media:prune` deletes the ones no content uses (`MediaUsageProviderInterface` adds the contents of your own).
- Installation guide in `doc/`.

### Changed

- Requires PHP 8.2+, Sylius 2.0+, Symfony 6.4 or 7.4 and `symfony/html-sanitizer`.

### Removed

- The 1.x code: entities and repository built on `odiseoteam/blog-bundle`, Sonata blocks, form extensions, Semantic UI templates and the 1.x migration.
- The `odiseoteam/blog-bundle`, `symfony/webpack-encore-bundle`, phpspec and Psalm dependencies.
- The 1.x test application, Behat suites, phpspec specs, docs and screenshots.
