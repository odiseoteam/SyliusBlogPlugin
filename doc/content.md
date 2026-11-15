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
| Text nodes | `paragraph`, `heading` (levels 2 to 4), `blockquote`, `bulletList`, `orderedList`, `listItem`, `codeBlock`, `horizontalRule`, `hardBreak`, `table`, `tableRow`, `tableHeader`, `tableCell` |
| Media | `video`: a YouTube or Vimeo URL, drawn as an embed (YouTube without cookies) |
| Marks | `bold`, `italic`, `underline`, `strike`, `code`, `link` (`href`, `rel` of nofollow, sponsored and ugc, and `target`) |
| Escape | `html`: raw HTML, sanitized every time it is drawn |

A link can only go to a web page, an email, a phone or a path of the site. Anything else is drawn as plain
text.

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
| `odiseo_blog.content.word_counter` | Counts the words of a text. |
| `odiseo_blog.content.reading_time` | Minutes to read a number of words at `words_per_minute`, rounded up. |
| `odiseo_blog.content.validator` | Checks the JSON of a content before it is saved. |

```php
$document = $parser->parse($translation->getContent());

$html = $htmlRenderer->render($document);
$markdown = $markdownRenderer->render($document);
$entries = $tocExtractor->extract($document); // level, text and anchor of each heading
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

A node is a class, a Twig template and, in the editor, a Tiptap extension. Implementing
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

A mark is the same with `ContentMarkInterface`, `AbstractContentMark` and the tag `odiseo_blog.content_mark`;
its template receives `mark`, `attributes` and `content`.

To replace a node of the plugin, register another one of the same `type` with a higher `priority` in the tag.
To change how a node is drawn, override its template, as with any bundle template:
`templates/bundles/OdiseoSyliusBlogPlugin/content/node/paragraph.html.twig`.

The `Content` folder knows nothing about articles, so it can be used for any other content.
