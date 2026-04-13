# Diffyweb Base Theme

Minimal Full Site Editing (FSE) starter theme for WordPress 6.8+.

## Requirements

- WordPress 6.8+
- PHP 5.7+

## Install

Upload the theme directory to `wp-content/themes/` and activate in **Appearance → Themes**.

## What's included

**Templates:** front-page, home, archive, single, page, index

**Parts:** header, footer

**theme.json:** Grayscale color palette, appearance tools enabled, no default gradients

**Shortcodes:**
- `[current_year]` — renders the current year
- `[site_title]` — renders the site name

**Balance text:** All headings (h1–h6) and any element with the `.text-balance` utility class get balanced wrapping via native CSS `text-wrap: balance`. Shipped as a tiny enqueued stylesheet at `assets/css/text-balance.css`. The legacy `.balance-text` class name is kept as a backward-compat alias. No JavaScript; degrades gracefully to normal wrapping in browsers that don't yet support the property (Chrome 114+, Firefox 121+, Safari 17.5+).

## Customizing

This is a starter theme. Fork it and modify `theme.json` for your color palette, typography, and layout settings. Templates and parts use standard WordPress block markup.
