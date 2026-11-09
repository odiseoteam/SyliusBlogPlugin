[Docs index](README.md)

# Authors

Authors sign the articles. They are managed in the admin, under **Blog › Authors**, and are not the same as
administrators: an author can exist without a user, and a user needs no author.

| Field | | Notes |
| --- | --- | --- |
| Code | Required | Unique. Letters, numbers, dashes and underscores. It can't be changed later. |
| Name | Required | |
| Slug | Required | Unique. The button next to it generates it from the name, never from the administrator's username. |
| Enabled | | A disabled author is kept, but not shown. |
| Administrator | Optional | Links the author to one administrator, so the author is preselected for their articles. Each administrator can be linked to one author. |
| Avatar | Optional | Stored like the rest of the [images](images.md). A button removes it, with its file and thumbnails. |
| Social links | Optional | A network and a URL each, shown in the order they were added. To change the order, remove a link and add it again. |
| Job title, bio, meta title and description | Per locale | |

An author that signs articles can't be deleted, only disabled.

## Networks

The networks to choose from are `x`, `linkedin`, `facebook`, `instagram`, `youtube`, `tiktok`, `github` and
`website`. To use others, set the whole list:

```yaml
# config/packages/odiseo_sylius_blog.yaml
odiseo_sylius_blog:
    social_networks: [x, linkedin, mastodon]
```

Each network needs its label, as the translation `odiseo_blog.social_network.<code>`:

```yaml
# translations/messages.en.yaml
odiseo_blog:
    social_network:
        mastodon: Mastodon
```
