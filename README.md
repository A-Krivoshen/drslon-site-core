# DrSlon Site Core

Compatibility and site-core plugin for krivoshein.site.

This plugin stores functionality that should not live inside the WordPress theme:

- legacy CPT registration;
- ACF-related logic;
- legacy shortcodes from the old Arkai child theme;
- partners, services and site showcase output;
- compatibility layer for migration to the new drslon-blog-theme block theme.

## Current status

Version 0.1.0 is a temporary compatibility bridge.

At this stage the plugin loads legacy logic from:

includes/legacy-arkai-child-functions.php

Later this file should be split into normal modules:

- includes/cpt.php
- includes/acf-fields.php
- includes/shortcodes/partners-grid.php
- includes/shortcodes/services-pages-showcase.php
- includes/shortcodes/services-landing.php
- includes/shortcodes/clients-grid.php

## Related project

Theme repository:

drslon-blog-theme

The theme is responsible for layout, templates, parts, patterns and visual style.

This plugin is responsible for site logic, CPT, ACF and legacy shortcodes.
