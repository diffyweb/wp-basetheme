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

**theme.json:** 40-color palette (white, black, and gray and primary-blue ramps, each with 10% tints and shades from 90% lighter to 90% darker), 8 fluid font sizes over a system font stack, content width 1000px / wide width 1500px with a 1rem side gutter set as root padding (see Gutter below), block-level style overrides for headings, buttons, quotes, separators, site-title, and site-tagline. Custom gradients disabled; default palettes and gradients stripped.

**Shortcodes** (one file per shortcode under `inc/shortcodes/`):
- `[current_year]` — renders the current year (timezone-aware via `wp_date()`)
- `[site_title]` — renders the site name from Settings

**Text balancing (companion plugin):** Heading/short-line balancing (`text-wrap: balance`) is provided by the **[wp-balance-text](https://github.com/diffyweb/wp-balance-text)** plugin, not the theme. It adds a *Balance* toggle to the editor's native Typography panel (per-block, composes with any block style variation) plus a **Settings → Balance Text** page to pick block types balanced by default (H1–H6 on out of the box). Install + activate it alongside this theme; the theme no longer ships its own text-balance stylesheet — one reusable home for the feature instead of a per-theme copy.

**Block modifications:** `core/template-part` and `core/post-content` have spacing supports (margin + padding) enabled so they can be tuned from the Site Editor like any other block.

## Palette

`white`, `black` and the `gray` ramp stay on every site. Every brand color gets its full ramp, from 90% lighter to 90% darker in 10% steps (19 entries, slugs like `primary-30-lighter` and `primary-70-darker`), so any brand color can be tinted or shaded the same way.

To add a brand color, run the script and paste its output into the `palette` array in `theme.json`, after `black`:

```
php bin/palette-ramp.php accent "Accent" "#e0457b"
```

To check that every ramp in `theme.json` still matches the formula in the script: `php bin/palette-ramp.php --check theme.json`.

## Gutter

Text never touches the screen edge: there's a gutter on each side, set once as WordPress root padding (`styles.spacing.padding` left and right). Its value is core's spacing preset 40 (`var:preset|spacing|40`, which core's default spacing scale makes 1rem), so the gutter relies on that preset: if a site changes its spacing scale, the gutter changes with it. `useRootPaddingAwareAlignments` is on, so full-width blocks still reach the edge. `contentSize` and `wideSize` are plain `1000px` and `1500px`. Change the gutter in that one place; never put it back into the widths.

Covers take the same value for their side padding (`styles.blocks.core/cover.spacing.padding` is `var(--wp--style--root--padding-left)` and `var(--wp--style--root--padding-right)`), so cover text sits one root padding inside the cover's edge and a full-width cover's text lines up with body text. A cover that sets its own side padding uses that instead. <!-- voice-lint: ok (CSS variable names) -->

One placement needs a template setting: a constrained cover at the top level of a template, in a template part, or in a flow-layout `main` isn't inside any padded container, so core also pads its inner container with the root padding, and theme.json can't reach that. Set that cover's own side padding to `0` (left and right) so its text sits one root padding in like every other cover. `single.html`'s hero does this.

Groups follow core: a Group inside another constrained container, such as a Group in post content or the pagination row in `front-page`, `home`, `index` and `archive`, lines up with the text around it.

`settings.typography.fluid.maxViewportWidth` is `1600px`, because fluid type otherwise takes its widest viewport from `wideSize`. Keep it if you change `wideSize`, or your font sizes change too.

A site built from an older copy of this theme adopts this in theme.json (the two layout sizes, the root padding, `maxViewportWidth` and the cover padding), plus side padding `0` on any constrained cover its templates place at the top level, in a part or in a flow-layout `main`. Cover text moves `1em` closer to the cover's edge, and nested Groups line up with the text around them, so measure the site's pages before shipping.

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
