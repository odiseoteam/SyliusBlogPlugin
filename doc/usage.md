## Usage

For the administration you will have the Blog menu. And for the frontend you can go to the /{locale}/blog/articles to see the blog articles. 
Feel free to modify the plugin templates like you want.

### Partial routes

To render a list of latest articles you can do something like this:

```twig
{{ render(url('odiseo_sylius_blog_plugin_shop_partial_article_index_latest', {'count': 4, 'template': '@OdiseoSyliusBlogPlugin/Shop/Article/_latest.html.twig'})) }}
``` 

And to render a list of categories:

```twig
{{ render(url('odiseo_sylius_blog_plugin_shop_partial_article_category_index', {'template': '@OdiseoSyliusBlogPlugin/Shop/ArticleCategory/_verticalMenu.html.twig'})) }}
``` 

For forms use the validation group `odiseo`

This plugin comes with Disqus support. You can enable it adding the following configuration:

```yml
odiseo_sylius_blog:
    disqus:
        shortname: EDITME
```
