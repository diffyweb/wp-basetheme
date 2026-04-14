# Diffyweb Base Theme

Minimal Full Site Editing (FSE) starter theme for WordPress 6.8+. Pure block theme — no classic PHP templates, no build step. Fork it, tune `theme.json`, ship.

## Requirements

- WordPress 6.8+
- PHP 7.4+

## Install

Upload the theme directory to `wp-content/themes/` and activate in **Appearance → Themes**.

## What's included

**Templates:** `front-page`, `home`, `archive`, `single`, `page`, `index`, plus a `page-style-guide` custom template you can assign to any page to preview every core block — headings, lists, quotes, code, tables, buttons, images (default/wide/full), gallery, cover, media & text, columns, details/summary, separators, and spacer — with your current `theme.json` settings.

**Parts:** `header`, `footer`

**theme.json:** Grayscale + primary-blue color palette, 8 fluid font sizes over a system font stack, content width 1000px / wide width 1500px, root padding-aware alignments, block-level style overrides for headings, buttons, quotes, separators, site-title, and site-tagline. Custom gradients disabled; default palettes and gradients stripped.

**Shortcodes** (one file per shortcode under `inc/shortcodes/`):
- `[current_year]` — renders the current year (timezone-aware via `wp_date()`)
- `[site_title]` — renders the site name from Settings

**Text balancing:** All headings (h1–h6) and any element with the `.text-balance` utility class get balanced wrapping via native CSS `text-wrap: balance`, shipped as a tiny enqueued stylesheet at `assets/css/text-balance.css`. The legacy `.balance-text` class name is kept as a backward-compat alias. No JavaScript; degrades gracefully to normal wrapping in browsers that don't yet support the property (Chrome 114+, Firefox 121+, Safari 17.5+).

**Block modifications:** `core/template-part` and `core/post-content` have spacing supports (margin + padding) enabled so they can be tuned from the Site Editor like any other block.

## Architecture

`functions.php` is a thin bootstrap that auto-loads every PHP file under `inc/<category>/`. Features live in focused, single-responsibility files grouped by concern:

```
inc/
  shortcodes/      # one file per shortcode
  enqueues/        # asset enqueue logic (scripts, styles, inline blobs)
  block-mods/      # block-layer modifications (supports, styles, render filters, unregistrations)
```

Drop a new file into the matching category directory and it loads automatically — no registration step. Filenames carry their category as a prefix (`shortcode-*.php`, `enqueue-*.php`, `block-mod-*.php`) so they read clearly in grep output and stack traces. Functions are named with the `wp_basetheme_<category>_<feature>` pattern — no anonymous closures on hooks.

Narrow utility stylesheets live under `assets/css/<feature>.css` paired with a matching `inc/enqueues/enqueue-<feature>.php`. Design tokens still belong in `theme.json`.

## Customizing

This is a starter theme — fork it and modify `theme.json` for colors, typography, spacing, and layout. Templates and parts use standard WordPress block markup. To preview your design system changes across all block types in one view, create a page and assign it the **Style Guide** template from the page template picker.
