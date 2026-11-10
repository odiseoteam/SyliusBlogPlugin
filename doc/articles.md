[Docs index](README.md)

# Articles

Articles are managed in the admin, under **Blog › Articles**. The list shows each article's image, title, code, state,
publication date, authors and channels. It can be searched by code and title, and filtered by state, channel,
category, author and publication date.

| Field | | Notes |
| --- | --- | --- |
| Code | Required | Unique. Letters, numbers, dashes and underscores. It can't be changed later. |
| Featured | | |
| Comments enabled | | On by default. |
| Authors | Optional | Enabled [authors](authors.md), in byline order: the order they are added in. To move one, remove it and add it again. A new article starts signed by the author linked to the administrator writing it. |
| Channels | Optional | All of them are checked on a new article. Without channels, the article isn't shown anywhere. |
| Image | Optional | Stored like the rest of the [images](images.md). A button removes it, with its file and thumbnails. |
| Main category | Optional | It is added to the categories when saving. |
| Categories and tags | Optional | |
| Title | Required, per locale | |
| Slug | Required, per locale | Unique per locale. The button next to it generates it from the title. |
| Excerpt | Per locale | A summary for listings and search results. |
| Content | Per locale | Tiptap JSON, typed by hand for now. It must be valid JSON. |
| Image alt text | Per locale | Describes the image for those who can't see it. |
| Meta title and description | Per locale | |

New articles are drafts.

When the image lacks its alt text in a language the article is written in, the form warns about it. The article
can still be saved.
