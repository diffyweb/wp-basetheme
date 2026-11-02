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

**theme.json:** 40-color palette (white, black, and gray and primary-blue ramps, each with 10% tints and shades from 90% lighter to 90% darker), a 9-step fluid type scale (h1 to h6, body, small and extra-small) over a system font stack, content width 1000px / wide width 1500px, root padding-aware alignments, block-level style overrides for headings, buttons, quotes, separators, site-title, and site-tagline. Custom gradients disabled; default palettes and gradients stripped.

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

## Type scale

Nine font sizes: one for each heading level, one for body text and two for smaller text. Each grows from its phone size at a 320px screen to its desktop size at 1600px, in a straight line between the two.

| Slug | Used for | Phone (320px) | Desktop (1600px) |
|---|---|---|---|
| `xxxl` | h1, page and post titles | 3rem (48px) | 4.2rem (67px) |
| `xxl` | h2 | 2.5rem (40px) | 3.43rem (55px) |
| `xl` | h3 | 2.08rem (33px) | 2.8rem (45px) |
| `lg` | h4 | 1.73rem (28px) | 2.29rem (37px) |
| `lg-minus` | h5 | 1.44rem (23px) | 1.87rem (30px) |
| `md-plus` | h6, the site title | 1.2rem (19px) | 1.53rem (24px) |
| `md` | body text, post titles in the post grid | 1rem (16px) | 1.25rem (20px) |
| `sm` | captions, meta lines, the footer | 0.83rem (13px) | 1.02rem (16px) |
| `xs` | fine print | 0.69rem (11px) | 0.83rem (13px) |

Headings and body text take these sizes from `theme.json` (`styles.elements.h1` to `h6`, and `styles.typography`), so a heading block needs no size picked. Pick a size on a block only when it should look like another level, such as a paragraph set in `xl` to read like an h3. Never type a raw size; use the nearest slug.

Each end of the range is a steady scale. On phones every step is 1.2 times the one below it, which takes the 1rem body to a 3rem h1. On desktop the step is 1.224, from the 1.25rem body to a 4.2rem h1, so headings open up a little more than body text on wide screens. `sm` and `xs` step down by the same ratios.

To retune a site, choose its body and h1 sizes for each end, then size every step as body × (h1 ÷ body)^(n ÷ 6), where n is 1 for h6 up to 6 for h1, -1 for `sm` and -2 for `xs`, rounded to two decimals. Put the phone value in `size` as well as `min`: WordPress only uses `size` when fluid type is off, and the editor shows it.

## Adopting this scale on an existing site

A site made from an older copy of this theme has the old slugs in its theme files and its stored content, and three of them now behave differently. Copy the new `fontSizes` and the h1 to h6 styles into the site's `theme.json`, then work through the slugs in this order:

1. Move the old `md` uses first. `md` was 1.5rem and is now the body size, so anything set in `md` shrinks to body text. Most of these belong in `md-plus`, the nearest new step; look at each one and decide. Do this before step 2, because once `norm` becomes `md` the two can't be told apart. Run the `md` search before adding any `md-plus`, since its variable form also matches `md-plus`.
2. Change every `norm` to `md`. `norm` no longer exists, and a block that still asks for it gets no size of its own: it inherits its parent's. Inside a heading that's the heading's size, so a line of 16px text can come out at 40px or more.
3. Check what uses `sm` and `xs`. They keep their names but come out 6–13% larger, which can matter in tight places such as buttons, badges and footer rows. `lg`, `xl` and `xxl` move by up to 14% and keep their roles.

A slug can appear in four forms: the block attribute `"fontSize":"norm"`, the class `has-norm-font-size`, the variable `--wp--preset--font-size--norm` and the style reference `var:preset|font-size|norm`. These commands list all four, shown for `norm` (swap in `md`, `sm` or `xs`). `[|]` is a literal pipe, and `\b` keeps core's old `normal` size out of the results.

In the theme folder (templates, parts, patterns, styles and `theme.json`):

```
grep -rnE 'fontSize":"norm"|has-norm-font-size|font-size(--|[|])norm\b' .
```

In the database (post content, Site Editor changes to templates, parts and global styles, and block widgets):

```
wp db search 'fontSize":"norm"|has-norm-font-size|font-size(--|[|])norm\b' --regex
```

Edit the theme files by hand. To change `norm` to `md` in the database, back up first (`wp db export`, saved outside the web root), dry-run each line and compare its count with the search, then run the lines again without `--dry-run` and flush the cache:

```
wp search-replace '"fontSize":"norm"' '"fontSize":"md"' --dry-run
wp search-replace 'has-norm-font-size' 'has-md-font-size' --dry-run
wp search-replace 'font-size(--|[|])norm\b' 'font-size${1}md' --regex --dry-run
wp cache flush
```

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
