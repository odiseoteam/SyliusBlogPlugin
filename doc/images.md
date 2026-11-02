[Docs index](README.md)

# Images

Articles have a featured image, with its alt text per locale. Categories have an image and authors an
avatar.

## Storage

The images go through Sylius' image uploader and storage, like product and taxon images. The files
land in `public/media/image` by default, and if you move Sylius' storage (to S3, for example) they
move with it. When an image is replaced or its owner is deleted, Sylius deletes the file and its
cached thumbnails.

Uploads take the image types Sylius allows (`sylius_core.allowed_images_mime_types`: JPEG, PNG, GIF
and WebP by default), up to 10 MB.

## Filter sets

| Filter set | Size | Format | Meant for |
| --- | --- | --- | --- |
| `odiseo_blog_card` | 800 × 450 | WebP | Article and category cards |
| `odiseo_blog_hero` | 1600 × 900 | WebP | The image on top of an article |
| `odiseo_blog_og` | 1200 × 630 | JPEG | Open Graph and Twitter Cards |
| `odiseo_blog_avatar` | 240 × 240 | WebP | Author avatars |
| `odiseo_blog_admin_thumbnail` | 200 × 200 | WebP | Admin grids |

All of them return exactly that size: they crop, and enlarge small images when needed. Your
application can change them in its own configuration. Its settings are merged with these, so change
the size through the `fixed` filter; another filter would be applied after it, not instead of it:

```yaml
# config/packages/liip_imagine.yaml
liip_imagine:
    filter_sets:
        odiseo_blog_card:
            filters:
                fixed: { width: 600, height: 400 }
```

## In templates

```twig
<img src="{{ odiseo_blog_image_url(article, 'odiseo_blog_card') }}" alt="{{ odiseo_blog_image_alt(article) }}">
```

- `odiseo_blog_image_url(subject, filter)` takes an article, a category or an author. Without an
  image, it returns a neutral placeholder, or Sylius' default avatar for authors.
- `odiseo_blog_image_alt(subject)` returns the alt text of the article in the current locale, or its
  title when there is none. For categories and authors, it returns their name.

The placeholder is a bundle asset, published by `bin/console assets:install`. A standard Sylius
application runs it on every `composer install`.
