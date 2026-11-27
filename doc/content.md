[Docs index](README.md)

# Content

The content of an article is a [Tiptap](https://tiptap.dev) document (ProseMirror JSON), saved exactly as
the editor sends it. The server derives everything else from it: HTML, Markdown, plain text, the table of
contents, the word count and the reading time.

```json
{"type": "doc", "content": [
    {"type": "heading", "attrs": {"level": 2}, "content": [{"type": "text", "text": "Brewing"}]},
    {"type": "paragraph", "content": [{"type": "text", "text": "Grind, pour and wait.", "marks": [{"type": "bold"}]}]}
]}
```

## What it can hold

| | Types |
| --- | --- |
| Text nodes | `paragraph` and `heading` (levels 2 to 4), both with an optional `textAlign` (`left`, `center` or `right`), and `blockquote`, `bulletList`, `orderedList`, `listItem`, `codeBlock`, `horizontalRule`, `hardBreak`, `table`, `tableRow`, `tableHeader`, `tableCell` |
| Media | `image`: an uploaded image, with `alt`, `caption`, `size` (`small`, `medium` or `full`) and `align` (`left`, `center` or `right`); `gallery`: uploaded `images` (each with its `alt`), `columns` (2, 3 or 4) and a `caption`; `video`: a YouTube or Vimeo URL, drawn as an embed (YouTube without cookies) |
| Catalog | `product`: the `code` of a product, drawn with the product card of the shop |
| Marks | `bold`, `italic`, `underline`, `strike`, `code`, `link` (`href`, `rel` of nofollow, sponsored and ugc, and `target`) |
| Escape | `html`: raw HTML, sanitized every time it is drawn |

Aligned blocks are drawn with the Bootstrap classes `text-start`, `text-center` and `text-end`; without an
alignment, the one of the theme applies. Markdown has no alignment, so it is left out there.

A link can only go to a web page, an email, a phone or a path of the site. Anything else is drawn as plain
text.

## Images

An `image` node keeps the path of a file uploaded from the editor, never an address of another site. It is
drawn with a `srcset` of the `odiseo_blog_content_*` filter sets (640, 960, 1280 and 1920 pixels wide, never
enlarged), `loading="lazy"`, and its `width` and `height` when the editor knew them, so the page does not jump
while it loads. On phones it takes the whole width of the text; from tablets up, half (`small`), three
quarters (`medium`) or all of it (`full`). Aligned to the left or right, a small or medium image floats with
the text around it, and takes a third or half of the column so that the text beside it stays readable. In a gallery, each image is a square thumbnail (`odiseo_blog_gallery`)
that links to a large version. See [images](images.md#filter-sets) to change the filter sets.

The alt text is optional: without it the image is drawn as decorative (`alt=""`), and the editor marks it. The
captions count as text and words; the alt texts do not. In Markdown an image is `![alt](address)` with its
caption below.

Each upload is kept as an `odiseo_blog.content_media` resource. Files no content uses any more are deleted by:

```bash
bin/console odiseo:blog:media:prune --dry-run   # list them
bin/console odiseo:blog:media:prune             # delete them, with their thumbnails
bin/console odiseo:blog:media:prune --days=30   # keep those uploaded in the last 30 days
```

Files uploaded in the last `media.prune_after_days` (7 by default) are kept, since they may belong to a content
that is still being written. Run it from a cron job if you want it to run on its own.

The command looks for the files in the content of the articles. If your own contents show uploaded files, tell
it with a service that implements `Odiseo\SyliusBlogPlugin\Media\MediaUsageProviderInterface` (with
autoconfigure, that is enough), or the files would be deleted. A node of your own that shows uploaded files
implements `MediaNodeInterface`, so they are found in any content.

## Products

A `product` node keeps only the code, so the card always shows the current name, price and image. When the
content is drawn, the product is looked up in the current channel: if it does not exist, is disabled or is not
in the channel, nothing is drawn. Saving only checks that the code looks like a code, so a product removed later
just stops showing. In Markdown it is a link to the product page, and it adds no words.

Drawing is done on every request, with no cache, so the cards are never stale. The products of a document
are loaded together before it is drawn, with what their card shows (translations, images, variants and prices),
in three queries. Measured on the test application, an article of about 2,000 words takes 2 ms to draw; with
10 products, 18 ms and 36 queries (the card of the shop still makes a few of its own).

`ProductFinderInterface` (`odiseo_blog.content.product_finder`) looks the products up; replace it to find
them another way. A node of your own can load what it needs in the same way by implementing
`PreloadingNodeInterface`: its `preload()` receives the whole document before it is drawn.

## Validation

The content is checked when an article is saved. It is rejected if it:

- is not JSON, or not a `doc`;
- uses a node or mark the blog does not know (they are never drawn, so they are never saved);
- has an attribute that is not valid, like a heading of level 1 or a video of another site;
- is larger than `max_size`, or has more nested levels than `max_depth`.

```yaml
# config/packages/odiseo_sylius_blog.yaml
odiseo_sylius_blog:
    content:
        max_size: 1048576   # bytes
        max_depth: 20       # up to 100
        iframe_hosts: [www.youtube-nocookie.com, player.vimeo.com]
        words_per_minute: 200   # reading speed, see below
        media:
            prune_after_days: 7   # uploads younger than this are never pruned
```

## The HTML node

The HTML node is for what has no node of its own. It goes through Symfony's HTML sanitizer: scripts, styles,
forms and event handlers are removed, links can only be `http`, `https`, `mailto` and `tel`, and images only
`https`. An iframe is kept only when its `src` is an `https` page of one of the `iframe_hosts` (or of their
subdomains).

## Using it

Services, all in `Odiseo\SyliusBlogPlugin\Content`:

| Service | Does |
| --- | --- |
| `odiseo_blog.content.parser` | Reads the stored JSON into a `Document\Node` tree. Content it cannot read is an empty document. |
| `odiseo_blog.content.renderer.html` | Draws the document with the Twig template of each node and mark. |
| `odiseo_blog.content.renderer.markdown` | Writes it as Markdown. |
| `odiseo_blog.content.extractor.plain_text` | The text without formatting, with a blank line between blocks. |
| `odiseo_blog.content.extractor.toc` | The headings with their anchors. The headings drawn by the HTML renderer carry the same `id`. |
| `odiseo_blog.content.extractor.embedded_products` | The codes of the products of a document, in order and without repeats. |
| `odiseo_blog.content.extractor.embedded_media` | The paths of the uploaded files a document shows, in order and without repeats. |
| `odiseo_blog.content.word_counter` | Counts the words of a text. |
| `odiseo_blog.content.reading_time` | Minutes to read a number of words at `words_per_minute`, rounded up. |
| `odiseo_blog.content.validator` | Checks the JSON of a content before it is saved. |

```php
$document = $parser->parse($translation->getContent());

$html = $htmlRenderer->render($document);
$markdown = $markdownRenderer->render($document);
$entries = $tocExtractor->extract($document); // level, text and anchor of each heading
```

In templates, `odiseo_blog_content(article)` draws the content of an article in the current locale:

```twig
{{ odiseo_blog_content(article) }}
```

The anchors come from the text of the title (`why-coffee`, and `why-coffee-2` when it repeats), so changing a
title changes its anchor.

Saving a translation stores its `plainText` and `wordCount`, whatever the way the content arrives.

### Reading time

`words_per_minute` (200 by default) sets the reading speed. An article with text takes at least a minute;
one without text, 0. In templates, for the current locale:

```twig
{% set minutes = odiseo_blog_reading_time(article) %}
```

## Your own node

A node is a class, a Twig template and, in the editor, a Tiptap extension (see
[the editor](editor.md#your-own-node)). Implementing
`ContentNodeInterface` is enough: with autoconfigure, the service is tagged `odiseo_blog.content_node`.
Extend `AbstractContentNode` to skip the defaults.

```php
use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Odiseo\SyliusBlogPlugin\Content\Nodes\AbstractContentNode;
use Symfony\Component\Validator\Constraints as Assert;

final class CalloutNode extends AbstractContentNode
{
    public function getType(): string
    {
        return 'callout';
    }

    // The attributes the node uses, with their defaults. Any other one is ignored.
    public function getAttributes(): array
    {
        return ['tone' => 'info'];
    }

    // Checked when the content is saved.
    public function getConstraints(): array
    {
        return ['tone' => new Assert\Choice(choices: ['info', 'warning'])];
    }

    // Defaults to @OdiseoSyliusBlogPlugin/content/node/callout.html.twig.
    public function getTemplate(): string
    {
        return 'content/callout.html.twig';
    }

    public function toMarkdown(Node $node, array $children): string
    {
        return '> ' . implode("\n> ", $children);
    }
}
```

```twig
{# templates/content/callout.html.twig #}
<div class="alert alert-{{ attributes.tone }}">{{ children }}</div>
```

The template receives `node`, `attributes` (with the defaults filled in), `children` (the HTML of the child
nodes, already safe), `anchor` (only for headings) and whatever `getVariables()` returns. Output that comes
from the author must be escaped, as Twig does by default.

A node without children whose attributes hold text, like a caption, implements `TextNodeInterface` so that the
text counts for the plain text and the words.

A mark is the same with `ContentMarkInterface`, `AbstractContentMark` and the tag `odiseo_blog.content_mark`;
its template receives `mark`, `attributes` and `content`.

To replace a node of the plugin, register another one of the same `type` with a higher `priority` in the tag.
To change how a node is drawn, override its template, as with any bundle template:
`templates/bundles/OdiseoSyliusBlogPlugin/content/node/paragraph.html.twig`.

The `Content` folder knows nothing about articles, so it can be used for any other content.
