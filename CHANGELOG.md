# Changelog

## 2026-04-14

- Adopt `SESSIONLOG.md` convention for session handoff notes; add Session Continuity section to `AGENTS.md`
- Update `README.md` to match current architecture — document `inc/` modular layout, `page-style-guide` template, text-balance utility, and block spacing supports
- Bump PHP requirement from `5.7` to `7.4` in `style.css` and README (`5.7` was not a valid PHP version)

## 2026-04-13

- **`functions.php` refactor** — modularize into an `inc/<category>/` auto-loader. Features now live in focused, single-responsibility files grouped by concern (`inc/shortcodes/`, `inc/enqueues/`, `inc/block-mods/`) and auto-loaded via a `glob()` bootstrap. Function prefix: `wp_basetheme_<category>_<feature>`. Filenames carry category prefixes (`shortcode-*.php`, `enqueue-*.php`, `block-mod-*.php`).
- **Text balancing: JS → native CSS** — replace the vendored `vanilla-balance-text` library with native CSS `text-wrap: balance`, shipped as `assets/css/text-balance.css` and enqueued via `inc/enqueues/enqueue-text-balance.php`. Canonical utility class is `.text-balance`; legacy `.balance-text` kept as a backward-compat alias. Default selectors match the former JS init (`h1`–`h6` + utility class). No JavaScript; degrades gracefully to normal wrapping in browsers without `text-wrap: balance` (pre-Chrome 114, pre-Firefox 121, pre-Safari 17.5).
- **Style Guide page template** — add `templates/page-style-guide.html`, a custom page template registered via `theme.json > customTemplates` that renders every core block (h1–h6, lists, quotes, code/preformatted/verse, tables, buttons, images with default/wide/full alignment variants, gallery, cover, media & text, columns, details/summary, separator style variants, spacer) with the current `theme.json` settings. Selectable from the page template picker as "Style Guide."
- **Block spacing supports** — enable margin and padding controls on `core/template-part` and `core/post-content` via a `register_block_type_args` filter so they can be tuned from the Site Editor like any other block.

## 2026-03-23

- Add `AGENTS.md` with agent instructions; `CLAUDE.md`, `CODEX.md`, `GEMINI.md` symlink to it

## 2026-03-12

- Rewrite README

## 2025-08-11

- v0.1.0 initial commit — minimal FSE starter theme with grayscale palette, fluid typography, system font stack, utility shortcodes
