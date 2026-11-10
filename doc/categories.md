[Docs index](README.md)

# Categories

Categories organize articles in a tree. They are managed in the admin, under **Blog › Categories**, like
taxons: the tree is on the left, and the form to create or edit a category on the right.

| Field | | Notes |
| --- | --- | --- |
| Code | Required | Unique. Letters, numbers, dashes and underscores. It can't be changed later. |
| Parent | Optional | Empty for a root category. It can't be the category itself or one of its subcategories. |
| Name | Required, per locale | |
| Slug | Required, per locale | Unique per locale, and flat: it doesn't include the parent's. The button next to it generates it from the name. |
| Description | Per locale | |
| Meta title and description | Per locale | |
| Image | Optional | Stored like the rest of the [images](images.md). A button removes it, with its file and thumbnails. |

From the tree you can edit a category, create a subcategory, move it up or down among its siblings, and delete it.
Deleting a category also deletes its subcategories. The articles that used them keep existing, without those categories.
