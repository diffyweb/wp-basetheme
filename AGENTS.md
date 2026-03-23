# wp-basetheme

Minimal Full Site Editing (FSE) starter theme for WordPress 6.8+. Pure block theme -- no classic PHP templates, no build step. All layout is WordPress block markup in HTML files, configured via `theme.json`.

## What this is

A starter/base theme designed to be forked and customized per-site. Ships with a grayscale + primary-blue color palette, fluid typography, system font stack, and a handful of shortcodes. Branded as "Diffyweb Base Theme."

Not a parent theme -- intended to be copied and modified directly.

## Tech stack

- **WordPress 6.8+** block theme (Full Site Editing / FSE)
- **theme.json v3** for all design tokens (colors, typography, spacing, layout)
- **Block markup HTML** templates and template parts (no PHP template files)
- **PHP** only for `functions.php` (shortcodes + script enqueue)
- **vanilla-balance-text** JS library loaded from jsDelivr CDN
- No build tools, no CSS preprocessor, no bundler, no node dependencies

## Directory structure

```
wp-basetheme/
  functions.php        # Shortcodes + balance-text script enqueue
  style.css            # Theme metadata header only (no custom CSS rules)
  theme.json           # Design tokens, block styles, template part registration
  screenshot.png       # Theme screenshot for WP admin
  README.md            # Project readme
  templates/
    index.html         # Fallback post-list grid with pagination
    front-page.html    # Static front page -- same grid layout with top spacer
    home.html          # Blog home -- post-list grid with pagination
    archive.html       # Archive pages -- same grid pattern
    single.html        # Single post -- featured image cover + post content
    page.html          # Single page -- title + post content
  parts/
    header.html        # Site header: logo image + site-title + navigation
    footer.html        # Site footer: logo image + site-title + navigation
```

## How it works

This is a **block theme** -- WordPress renders pages entirely from block markup in HTML template files. There are no classic PHP template files (`single.php`, `page.php`, etc.).

- **Templates** (`templates/*.html`) define full page layouts using WordPress block comments (`<!-- wp:group -->`, `<!-- wp:query -->`, etc.). Each template includes the header and footer parts via `<!-- wp:template-part -->`.
- **Template parts** (`parts/*.html`) are reusable header/footer fragments.
- **theme.json** is the central configuration file. It defines:
  - Color palette: 21 grayscale shades (white through black) + 18 primary-blue shades
  - Typography: system font stack, 8 fluid font sizes (xs through xxxl)
  - Layout: content width 1000px, wide width 1500px (both with responsive `min()`)
  - Block-level style overrides for headings, buttons, quotes, separators, site-title, site-tagline
  - Root padding-aware alignments enabled
  - Custom gradients disabled; default palettes/gradients stripped
- **functions.php** provides:
  - `[current_year]` shortcode -- renders the four-digit year
  - `[site_title]` shortcode -- renders the site name from Settings
  - Enqueues `vanilla-balance-text` from jsDelivr CDN, then applies it to all `h1`-`h6` and `.balance-text` elements on load and resize

## Scripts / Key files

| File | Purpose |
|---|---|
| `theme.json` | All design tokens, block styles, template part registration. The single source of truth for colors, fonts, spacing, and layout. |
| `functions.php` | `[current_year]` and `[site_title]` shortcodes; balance-text CDN script enqueue + inline init script in footer. |
| `style.css` | Theme metadata header only (name, version, requirements). Contains no CSS rules -- all styling is in theme.json. |
| `templates/single.html` | Single post template with featured-image cover block (70% dim overlay). |
| `templates/index.html` | Default post-list: responsive grid (min 15rem columns), featured images, titles, pagination. |
| `parts/header.html` | Flex layout: logo + site-title (left), navigation block ref (right), bottom border. |
| `parts/footer.html` | Same structure as header but with top border. |

## Key conventions

- **No custom CSS.** All styling goes through `theme.json` design tokens and block-level style settings. `style.css` is metadata-only.
- **Block markup only.** Templates are pure WordPress block comments in HTML. No PHP template tags, no `the_content()` calls, no template hierarchy hacks.
- **Fluid typography.** Every font size has `min`/`max` fluid values. Do not use fixed sizes.
- **Color palette naming.** Grayscale uses `gray-{N}-lighter` / `gray-{N}-darker` pattern. Primary uses `primary-{N}-lighter` / `primary-{N}-darker`. Base colors are `white`, `black`, `gray`, `primary`.
- **Shortcodes over hardcoded values.** Use `[current_year]` in footer copyright rather than a hardcoded year.
- **CDN for JS dependencies.** `vanilla-balance-text` is loaded from jsDelivr pinned to a specific commit hash, not vendored locally.
- **Navigation blocks use `ref` IDs** that point to wp_navigation post IDs (will differ per WordPress install -- update after activation).

## Build / lint

No build step. No linters configured. No npm/composer dependencies.

To work on this theme:
1. Copy/symlink the directory into a WordPress install at `wp-content/themes/wp-basetheme/`
2. Activate in **Appearance > Themes**
3. Edit templates in the Site Editor (Appearance > Editor) or directly in the HTML files
4. After forking, update `style.css` metadata (theme name, author, version) and replace `screenshot.png`

