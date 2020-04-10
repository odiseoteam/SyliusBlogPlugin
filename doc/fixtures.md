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
                blog:
                    options:
                        random: 12
```
