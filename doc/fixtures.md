## Fixtures

This plugin comes with fixtures:

### Blog

Simply add this configuration on your fixture suite:

```yml
# config/packages/_sylius.yaml
sylius_fixtures:
    suites:
        default:
            fixtures:
                blog_article_category:
                    options:
                        random: 5

                blog_article:
                    options:
                        random: 12
```
