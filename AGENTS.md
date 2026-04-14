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
- **Narrow utility stylesheets** in `assets/css/*.css` enqueued via `inc/enqueues/*.php` (one per concern). First one: native `text-wrap: balance` for headings. No JS, no vendored libraries.
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
      enqueue-text-balance.php           # Enqueues assets/css/text-balance.css
    block-mods/
      block-mod-spacing-supports.php     # Enables margin/padding on template-part + post-content
  assets/
    css/
      text-balance.css                   # text-wrap: balance on h1-h6 + .text-balance (+ legacy .balance-text alias)
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
- **Filename pattern**: `<category-singular>-<feature>.php` (e.g., `shortcode-current-year.php`, `enqueue-text-balance.php`, `block-mod-spacing-supports.php`). Redundant with the parent dir by design — the filename alone reads clearly in grep output, stack traces, and open tabs.
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
  - `inc/enqueues/enqueue-text-balance.php` — enqueues `assets/css/text-balance.css`, which applies native `text-wrap: balance` to all `h1`-`h6` and any element with the `.text-balance` utility class (or its legacy alias `.balance-text`)
  - `inc/block-mods/block-mod-spacing-supports.php` — enables margin/padding controls on `core/template-part` and `core/post-content`

## Scripts / Key files

| File | Purpose |
|---|---|
| `theme.json` | All design tokens, block styles, template part registration. The single source of truth for colors, fonts, spacing, and layout. |
| `functions.php` | Thin bootstrap that `require_once`s every `inc/<category>/*.php` via a `glob()` auto-loader. |
| `inc/shortcodes/*.php` | One file per shortcode. Currently `[current_year]` and `[site_title]`. |
| `inc/enqueues/*.php` | Asset enqueue logic. Currently the `text-balance.css` stylesheet. |
| `inc/block-mods/*.php` | Block-layer modifications (supports, styles, unregistrations, render filters). Currently spacing supports for `core/template-part` and `core/post-content`. |
| `assets/css/text-balance.css` | Narrow utility stylesheet: `text-wrap: balance` on headings, `.text-balance`, and legacy `.balance-text`. Progressive enhancement — falls back to normal wrap where unsupported. |
| `style.css` | Theme metadata header only (name, version, requirements). Contains no CSS rules -- all styling is in theme.json. |
| `templates/single.html` | Single post template with featured-image cover block (70% dim overlay). |
| `templates/index.html` | Default post-list: responsive grid (min 15rem columns), featured images, titles, pagination. |
| `parts/header.html` | Flex layout: logo + site-title (left), navigation block ref (right), bottom border. |
| `parts/footer.html` | Same structure as header but with top border. |

## Key conventions

- **Design tokens live in `theme.json`.** Colors, typography, spacing, layout, and block-level style overrides all belong there. `style.css` stays metadata-only.
- **Narrow CSS utilities may be enqueued.** Small, single-purpose stylesheets that can't be expressed as design tokens (e.g., `text-wrap: balance` on headings) live under `assets/css/<feature>.css` and are enqueued via a matching `inc/enqueues/enqueue-<feature>.php` file. Keep each stylesheet focused on one concern — no catch-all `main.css`. If something *can* be expressed as a theme.json design token, it still belongs there.
- **Block markup only.** Templates are pure WordPress block comments in HTML. No PHP template tags, no `the_content()` calls, no template hierarchy hacks.
- **Fluid typography.** Every font size has `min`/`max` fluid values. Do not use fixed sizes.
- **Color palette naming.** Grayscale uses `gray-{N}-lighter` / `gray-{N}-darker` pattern. Primary uses `primary-{N}-lighter` / `primary-{N}-darker`. Base colors are `white`, `black`, `gray`, `primary`.
- **Shortcodes over hardcoded values.** Use `[current_year]` in footer copyright rather than a hardcoded year.
- **Prefer native platform features over JS libraries.** Before vendoring or enqueueing a script, check whether a native CSS / HTML feature covers the use case. If a third-party JS dependency is still required, vendor it locally under `assets/js/vendor/` with the upstream commit hash in the enqueue file's docblock — no CDN dependencies.
- **One feature per inc/ file.** Shortcodes, enqueues, and block modifications each live in their own file under `inc/<category>/`. Filenames carry their category as a prefix (`shortcode-*.php`, `enqueue-*.php`, `block-mod-*.php`).
- **Named, prefixed functions on hooks.** Use `wp_basetheme_<category>_<feature>` naming. No anonymous closures on `add_action` / `add_filter` — they can't be removed and obscure stack traces.
- **Navigation blocks use `ref` IDs** that point to wp_navigation post IDs (will differ per WordPress install -- update after activation).

## Session Continuity

If `SESSIONLOG.md` exists, read it at the start of every session. It contains handoff notes from prior sessions — what was done, current state, what's next, and lessons learned.

**At session end**, update `SESSIONLOG.md`:
- Add a new entry at the top (below the header) with format: `## YYYY-MM-DD — INITIALS — Scope`
- Include four sections: `### What was done`, `### Current state`, `### What's next`, `### Lessons`
- Bullets, not prose. Concise.
- Keep all entries — history is cheap and useful for tracing when things happened
- If the file gets unwieldy, add an `## Archive` section and fold older entries below it
- If the file does not exist, carry on without it

`context/` is a different artifact: it holds durable, topic-scoped decision logs (architectural decisions, research, ADRs) committed alongside the work they document. `SESSIONLOG.md` is the rolling handoff log. Do not conflate the two.

If the `/session-search` skill is installed, use it to pull additional context when needed (e.g., searching for decisions or work from sessions older than what `SESSIONLOG.md` covers).

## Build / lint

No build step. No linters configured. No npm/composer dependencies.

To work on this theme:
1. Copy/symlink the directory into a WordPress install at `wp-content/themes/wp-basetheme/`
2. Activate in **Appearance > Themes**
3. Edit templates in the Site Editor (Appearance > Editor) or directly in the HTML files
4. After forking, update `style.css` metadata (theme name, author, version) and replace `screenshot.png`

