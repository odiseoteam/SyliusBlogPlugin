[Docs index](README.md)

# Images

Articles have a featured image, with its alt text per locale, and images and galleries in their content (see
[content](content.md#images)). Categories have an image and authors an avatar.

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
| `odiseo_blog_admin_thumbnail` | 200 × 200 | WebP | Admin grids and the galleries of the editor |
| `odiseo_blog_gallery` | 600 × 600 | WebP | The thumbnails of a gallery in the content |
| `odiseo_blog_content_640`, `_960`, `_1280`, `_1920` | That width at most | WebP | The images of the content |

All of them but the content ones return exactly that size: they crop, and enlarge small images when needed.
The content ones shrink the image to that width, keep its proportions and never enlarge it. Your
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

Keep the width of the content filter sets: the `srcset` of the content images tells the browser those widths.
Their format and quality can be changed.

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
