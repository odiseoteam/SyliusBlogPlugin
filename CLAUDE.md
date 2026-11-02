# CLAUDE.md

Guidance for Claude Code when working in this repository.

## What this is

MIT Sylius 2.x plugin (`odiseoteam/sylius-blog-plugin`, namespace `Odiseo\SyliusBlogPlugin\`): a blog
for Sylius stores, with products inside the articles. Version 2.0 is a rewrite; 1.x lives on the `1.4`
branch and is frozen.

## Conventions

- Code and comments in English; comments short and to the point.
- Prefix for services, routes, parameters and translations: `odiseo_blog.*`. Config root: `odiseo_sylius_blog`.
- Layout follows the other Odiseo 2.x plugins (Brevo, RBAC, Banner): `config/`, `templates/`,
  `translations/` and `assets/{admin,shop}` at the root, Doctrine XML mapping, Twig Hooks, Symfony
  Workflow, API Platform XML resources.
- `config/` imports only folders that exist: a glob import over a missing folder breaks the container.
- Admin and shop UI reuse Sylius' own helpers and components; custom CSS only where Sylius has no
  equivalent. No third-party scripts in the shop.
- Article content is Tiptap JSON; HTML, Markdown and plain text are derived from it.
- Code for upgrading from 1.x goes in `src/Legacy/`, and nothing else imports it.
- Every change updates `doc/`, `CHANGELOG.md` and the en/es translations.
- BDD-first: observable behavior starts as a red Behat scenario; mechanics get PHPUnit tests.
