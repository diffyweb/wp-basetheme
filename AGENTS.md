# wp-basetheme

Minimal Full Site Editing (FSE) starter theme for WordPress 6.8+. Pure block theme -- no classic PHP templates, no build step. All layout is WordPress block markup in HTML files, configured via `theme.json`.

## What this is

A starter/base theme designed to be forked and customized per-site. Ships with a grayscale + primary-blue color palette, fluid typography, system font stack, and a handful of shortcodes. Branded as "Diffyweb Base Theme."

Not a parent theme -- intended to be copied and modified directly.

## Tech stack

- **WordPress 6.8+** block theme (Full Site Editing / FSE)
- **theme.json v3** for all design tokens (colors, typography, spacing, layout)
- **Block markup HTML** templates and template parts (no PHP template files)
- **PHP** only in `functions.php` + modular `inc/<category>/` files (shortcodes, enqueues, block modifications)
- **vanilla-balance-text** JS library vendored locally at `assets/js/vendor/balancetext.min.js`
- No build tools, no CSS preprocessor, no bundler, no node dependencies

## Directory structure

```
wp-basetheme/
  functions.php                          # Thin bootstrap: auto-loads every inc/<category>/*.php
  style.css                              # Theme metadata header only (no custom CSS rules)
  theme.json                             # Design tokens, block styles, template part registration
  screenshot.png                         # Theme screenshot for WP admin
  README.md                              # Project readme
  inc/
    shortcodes/
      shortcode-current-year.php         # [current_year]
      shortcode-site-title.php           # [site_title]
    enqueues/
      enqueue-balance-text.php           # Registers vendored balance-text + inline init
    block-mods/
      block-mod-spacing-supports.php     # Enables margin/padding on template-part + post-content
  assets/
    js/
      vendor/
        balancetext.min.js               # Vendored vanilla-balance-text (pinned commit 30adf5e9)
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

### inc/ conventions

- **Auto-loading.** `functions.php` globs `inc/*/*.php` and `require_once`s each match. Drop a file into an existing category to add a feature — no registration step.
- **Category directories** group by concern. Current categories:
  - `shortcodes/` — `add_shortcode()` registrations, one shortcode per file.
  - `enqueues/` — asset enqueueing (scripts, styles, fonts, inline blobs).
  - `block-mods/` — block-layer modifications (register_block_type_args filters, `register_block_style()`, `unregister_block_type()`, `render_block` filters, block variations, pattern registration).
- **Filename pattern**: `<category-singular>-<feature>.php` (e.g., `shortcode-current-year.php`, `enqueue-balance-text.php`, `block-mod-spacing-supports.php`). Redundant with the parent dir by design — the filename alone reads clearly in grep output, stack traces, and open tabs.
- **Function prefix**: `wp_basetheme_<category>_<feature>()` (e.g., `wp_basetheme_shortcode_current_year`). Named functions over closures so hooks can be removed and filenames match function names.
- **File header**: each file starts with a short docblock, then `defined( 'ABSPATH' ) || exit;`, then the hook registration + function definition.
- **Adding a new category**: just create `inc/<new-category>/` and drop a file in. The auto-loader will pick it up automatically.

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
- **functions.php** is a thin bootstrap that auto-loads every `inc/<category>/*.php` file. Current features live in:
  - `inc/shortcodes/shortcode-current-year.php` — `[current_year]` renders the four-digit year (timezone-aware via `wp_date()`)
  - `inc/shortcodes/shortcode-site-title.php` — `[site_title]` renders the site name from Settings
  - `inc/enqueues/enqueue-balance-text.php` — registers the vendored `vanilla-balance-text` library and attaches an inline initializer that applies balancing to all `h1`-`h6` and `.balance-text` elements on load and resize
  - `inc/block-mods/block-mod-spacing-supports.php` — enables margin/padding controls on `core/template-part` and `core/post-content`

## Scripts / Key files

| File | Purpose |
|---|---|
| `theme.json` | All design tokens, block styles, template part registration. The single source of truth for colors, fonts, spacing, and layout. |
| `functions.php` | Thin bootstrap that `require_once`s every `inc/<category>/*.php` via a `glob()` auto-loader. |
| `inc/shortcodes/*.php` | One file per shortcode. Currently `[current_year]` and `[site_title]`. |
| `inc/enqueues/*.php` | Asset enqueue logic. Currently the vendored `balance-text` script + inline initializer. |
| `inc/block-mods/*.php` | Block-layer modifications (supports, styles, unregistrations, render filters). Currently spacing supports for `core/template-part` and `core/post-content`. |
| `assets/js/vendor/balancetext.min.js` | Vendored `vanilla-balance-text` library (pinned to upstream commit 30adf5e9, 2016-12-16 — upstream has been dormant since 2016). |
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
- **Vendor third-party JS locally.** Place vendored libraries under `assets/js/vendor/` and record the upstream commit hash in the enqueue file's docblock. No CDN dependencies.
- **One feature per inc/ file.** Shortcodes, enqueues, and block modifications each live in their own file under `inc/<category>/`. Filenames carry their category as a prefix (`shortcode-*.php`, `enqueue-*.php`, `block-mod-*.php`).
- **Named, prefixed functions on hooks.** Use `wp_basetheme_<category>_<feature>` naming. No anonymous closures on `add_action` / `add_filter` — they can't be removed and obscure stack traces.
- **Navigation blocks use `ref` IDs** that point to wp_navigation post IDs (will differ per WordPress install -- update after activation).

## Build / lint

No build step. No linters configured. No npm/composer dependencies.

To work on this theme:
1. Copy/symlink the directory into a WordPress install at `wp-content/themes/wp-basetheme/`
2. Activate in **Appearance > Themes**
3. Edit templates in the Site Editor (Appearance > Editor) or directly in the HTML files
4. After forking, update `style.css` metadata (theme name, author, version) and replace `screenshot.png`

