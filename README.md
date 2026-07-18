# DrSlon Site Core

Site-core plugin for krivoshein.site.

This plugin stores functionality that should not live inside the WordPress theme:

- CPT registration (`client`, `project`, `usluga`, `price`, `partner`) and `partner_category` taxonomy;
- ACF local field groups and the "Витрина сервисов" options page;
- shortcodes from the old Arkai child theme;
- Yandex RSYA ads (reco widget, InImage) and Telegram comments;
- The SEO Framework integration (titles, descriptions, robots);
- views counter, reading time and other site tweaks.

## Structure

- `drslon-site-core.php` — loader, activation/deactivation hooks.
- `includes/helpers.php` — shared constants, helpers, ad settings getters.
- `includes/seo.php` — The SEO Framework filters, sitemap disable.
- `includes/ads-settings.php` — admin page Настройки → Реклама РСЯ.
- `includes/ads.php` — RSYA loader, metatags, reco widget, InImage.
- `includes/comments.php` — Telegram comments, post extras renderer.
- `includes/site-tweaks.php` — views counter, reading time, misc tweaks.
- `includes/cpt.php` — CPT/taxonomy registration, one-time rewrite flush.
- `includes/acf-fields.php` — ACF local groups + options page.
- `includes/shortcodes/` — `services-landing`, `clients-grid`, `partners-grid`, `services-pages-showcase`, `translator-menu`.

## Compatibility bridge

The old arkai-child theme keeps the same logic in its own `functions.php`.
The plugin loads its modules only when arkai-child is NOT active
(sentinel: `krv_page_has_ui_shortcode()` must not exist yet).

## Ad settings

Настройки → Реклама РСЯ: reco widget and InImage on/off + block IDs.
InImage is disabled by default.

## Dependencies

- Advanced Custom Fields (optional: shortcodes degrade gracefully);
- ACF Font Awesome for the `service_icon` field type;
- The SEO Framework (optional: SEO filters activate only when present).

## Related project

Theme repository: drslon-blog-theme.

The theme is responsible for layout, templates, parts, patterns and visual style.
This plugin is responsible for site logic, CPT, ACF and shortcodes.
