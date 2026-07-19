# DrSlon Site Core

Compatibility and site-core plugin for krivoshein.site.

This plugin stores functionality that should not live inside the WordPress theme:

- legacy CPT registration;
- ACF-related logic;
- legacy shortcodes from the old Arkai child theme;
- partners, services and site showcase output;
- compatibility layer for migration to the new drslon-blog-theme block theme.

## Current status

Version 0.4.2 — modular site-core with ACF-managed pages, cache-independent
view counting, and an isolated, cookie-free Telegram comments/media proxy on
`tg.krivoshein.site`.

## Third-party hotfixes

Tracked patches for Zen Feed and WP Fastest Cache Premium live in
`deploy/patches/`. Check them after plugin updates without changing files:

```bash
sudo -u www-data ./deploy/apply-third-party-hotfixes.sh --check \
  --wordpress-root /var/www/krivoshein.site/htdocs
```

Apply compatible missing patches explicitly:

```bash
sudo -u www-data ./deploy/apply-third-party-hotfixes.sh --apply \
  --wordpress-root /var/www/krivoshein.site/htdocs
```

The script stops on an unexpected plugin version or source layout. Review the
new upstream code before updating the expected version or patch; never force an
old patch onto an unknown release.

`includes/legacy-arkai-child-functions.php` is now a slim bootstrap (helpers, TSF SEO, RSYA/Telegram hooks) that loads:

- `includes/cpt.php` — CPT registrations (client, project, usluga, price, partner)
- `includes/acf-fields.php` — ACF options pages and local field groups
- `includes/shortcodes/clients-grid.php` — `[krv_clients_grid]`
- `includes/shortcodes/partners-grid.php` — `[krv_partners_grid]`
- `includes/shortcodes/services-pages-showcase.php` — `[krv_services_pages_showcase]`
- `includes/shortcodes/services-landing.php` — `[krv_services_landing]`
- `includes/price-list-acf.php` — ACF options, canonical defaults and safe v2 migration for `/prays-list/`
- `includes/price-list-widget.php` — ACF-driven `[krv_price_list]` renderer
- `includes/views-counter.php` — throttled post/project view beacon compatible with full-page caching

## Price List

The WordPress admin page `Прайс-лист` controls the complete `/prays-list/` output:

- hero, contacts and diagnostic panel;
- all six service landing cards and links;
- popular packages;
- sortable tabs and nested price items;
- technologies, process, FAQ, CTA and disclaimer.

The v2 seed migrates the previous hardcoded content without overwriting custom
hero copy. The shortcode still falls back to canonical PHP defaults if ACF is
temporarily unavailable.

## Related project

Theme repository:

drslon-blog-theme

The theme is responsible for layout, templates, parts, patterns and visual style.

This plugin is responsible for site logic, CPT, ACF and legacy shortcodes.
