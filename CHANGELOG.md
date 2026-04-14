# Changelog

## 2026-04-14

- **Session log capture** — initially saved as a per-file artifact under `context/2026-04-13-modularization-and-style-guide-session.md`, then corrected to the project-root `SESSIONLOG.md` convention used on sibling WP projects. `context/` is reserved for durable topic-scoped decision logs and research; `SESSIONLOG.md` is the rolling handoff log.
- **`AGENTS.md` — add Session Continuity section** — instructs future sessions to read `SESSIONLOG.md` at start and append newest-first at end, with a four-section entry format (What was done / Current state / What's next / Lessons).
- **`README.md` rewrite** — bring in sync with current architecture. Document the `inc/` modular layout, filename and function-naming conventions, `page-style-guide` custom template, text-balance utility, and block spacing supports. Expand the `theme.json` description to mention primary-blue palette, fluid typography, system font stack, and root-padding-aware alignments.
- **Bump PHP requirement** from `5.7` (not a valid PHP version — there is no 5.7, PHP went from 5.6 to 7.0) to `7.4` in both `style.css` and `README.md`. `7.4` is the sensible floor for a WP 6.8+ theme that uses PHP 7.0+ null-coalescing in code.

## 2026-04-13

- **Enable spacing supports on `core/template-part` and `core/post-content`** — add a `register_block_type_args` filter to `functions.php` so both blocks expose margin and padding controls in the Site Editor. Initially landed as an anonymous closure inline in `functions.php`; later extracted to `inc/block-mods/` (see modularization entry below).
- **Scaffold `inc/<category>/` auto-loader** — introduce a modular layout. `functions.php` becomes a thin `glob()`-based bootstrap that `require_once`s every `inc/*/*.php`. Drop a file into a category directory and it loads — no registration step. Filename pattern: `<category-singular>-<feature>.php` (e.g. `shortcode-current-year.php`). Function-naming pattern: `wp_basetheme_<category>_<feature>()`. Each file guards with `defined( 'ABSPATH' ) || exit;` and registers its own hook.
- **Extract `[current_year]` shortcode** to `inc/shortcodes/shortcode-current-year.php`. Switch from `date( 'Y' )` to `wp_date( 'Y' )` for timezone-aware output.
- **Extract `[site_title]` shortcode** to `inc/shortcodes/shortcode-site-title.php`.
- **Vendor `vanilla-balance-text` locally** — drop the jsDelivr CDN dependency. Vendor at `assets/js/vendor/balancetext.min.js` pinned to upstream commit `30adf5e9` (2016-12-16, the newest available — the upstream repo has been dormant since 2016). Extract the enqueue to `inc/enqueues/enqueue-balance-text.php`, switching from the raw `wp_footer` echo hack to `wp_add_inline_script()` with `filemtime()`-based cache busting. Same-day follow-up drops this approach entirely in favor of native CSS (see next entry).
- **Extract block spacing supports filter** to `inc/block-mods/block-mod-spacing-supports.php`. Convert the anonymous closure to a named `wp_basetheme_block_mod_spacing_supports()` function, collapse the two duplicated branches to a single `in_array()` check.
- **Document modular `inc/` structure in `AGENTS.md`** — auto-loading convention, filename patterns, function prefix, per-file header requirements, and instructions for adding new categories.
- **Text balancing: replace vendored JS with native CSS `text-wrap: balance`** — drop `assets/js/vendor/balancetext.min.js` and `inc/enqueues/enqueue-balance-text.php` in favor of a tiny enqueued stylesheet at `assets/css/text-balance.css` loaded via `inc/enqueues/enqueue-text-balance.php`. Canonical utility class is `.text-balance` (matches the stylesheet, enqueue file, and handle names); legacy `.balance-text` kept as a backward-compat alias. Default selectors match the former JS init (`h1`–`h6` + utility class). No JavaScript; degrades gracefully to normal wrapping in browsers without `text-wrap: balance` (pre-Chrome 114, pre-Firefox 121, pre-Safari 17.5). Relaxes the prior "no custom CSS" convention to "narrow utility stylesheets may be enqueued, one file per concern — design tokens still belong in `theme.json`."
- **Style Guide page template** — add `templates/page-style-guide.html`, a custom page template registered via `theme.json > customTemplates` that renders every core block (h1–h6, lists, quotes, code/preformatted/verse, tables, buttons, images with default/wide/full alignment variants, gallery, cover, media & text, columns, details/summary, separator style variants, spacer) with the current `theme.json` settings. Selectable from the page template picker as "Style Guide." Template intentionally omits `wp:post-title` and `wp:post-content` so demo content lives in the template itself and can't be accidentally edited through the page editor.

## 2026-03-23

- Add `AGENTS.md` with agent instructions; `CLAUDE.md`, `CODEX.md`, `GEMINI.md` symlink to it

## 2026-03-12

- Rewrite README

## 2025-08-11

- v0.1.0 initial commit — minimal FSE starter theme with grayscale palette, fluid typography, system font stack, utility shortcodes
