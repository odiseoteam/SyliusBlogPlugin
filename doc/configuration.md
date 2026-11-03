[Docs index](README.md)

# Configuration

Each channel has its own blog settings. They are edited in the admin, under **Blog › Configuration**,
which lists every channel with the settings it uses.

| Setting | Default | Notes |
| --- | --- | --- |
| Show the blog in this channel | Yes | |
| Articles per page | 9 | From 1 to 100. |
| Title | "Blog" | Per locale. |
| Description | Empty | Per locale. A short text shown next to the articles. |

## Defaults

A channel nobody has configured yet uses the defaults. The first save in the admin stores its own
settings. To change the defaults:

```yaml
# config/packages/odiseo_sylius_blog.yaml
odiseo_sylius_blog:
    channel_defaults:
        enabled: true
        posts_per_page: 9
```

With `enabled: false`, the blog only shows in the channels where an admin turns it on.

## In code and templates

`odiseo_blog.provider.channel_configuration` (`BlogChannelConfigurationProviderInterface`) returns the
settings of a channel: its own, or an unsaved configuration with the defaults. In templates:

```twig
{% set configuration = odiseo_blog_channel_configuration(sylius.channel) %}
```
