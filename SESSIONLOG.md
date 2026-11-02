# Session Log

Newest first. Update at the end of every session. See `AGENTS.md > Session Continuity` for full instructions.

---

## 2026-11-02 - MC - Root padding gutter (0.3.0)

### What was done
- PR #1 (brand color ramps, 0.2.0) merged at 16:14 UTC.
- Moved the side gutter from the `min()` width formula to root padding (`var:preset|spacing|40`) with plain 1000px/1500px widths, and set `fluid.maxViewportWidth` to 1600px so font sizes stay put.
- Covers take their side padding from the root padding instead of core's fixed `1em` (Michael, 2026-11-02: covers should follow the same layout rule, based on the root padding variable). Nested Groups and the pagination row follow core and line up with the text around them (Michael: the nested Group layout "was always a frustration").
- Measured on basetheme.local at 7 widths, front end and editor: body text, header, footer, wide and full-width blocks and font sizes unchanged; cover text and nested Groups move as described in the CHANGELOG.

### Current state
- Branch `feature/root-padding-gutter`, PR #3 open. Version 0.3.0, the same number PR #2 (type scale) uses; whichever merges second goes to 0.4.0 by hand and resolves the CHANGELOG, README and AGENTS conflicts.
- Every cover follows one rule: text one root padding inside its edge. A constrained cover outside any padded container sets its own side padding to 0 to get there (`single.html`'s hero does), because core also pads its inner container.

### What's next
- Michael reviews #3.
- Measure a real site with the change before merging.

### Lessons
- Core puts root padding on the outermost padded container only. Anything that leaned on the old formula's inset inside every constrained container (covers, nested Groups) either follows core or needs its own setting.
- theme.json can't reach a cover's inner container, so a cover at the top level needs its own side padding set to 0 in the template to match covers in content.

## 2026-11-02 — MC — Brand color ramps (0.2.0)

### What was done
- Worked out the palette formula: per-channel sRGB mix toward white or black, `round(c * (1 - n * 0.01))` for darker, `round(c + (255 - c) * n * 0.01)` for lighter, round-half-up. It reproduces all 34 existing primary and gray entries.
- Added `primary-80-darker` (`#001133`) and `primary-90-darker` (`#000819`). Gray keeps its 80% and 90% darker steps (Michael, 2026-11-02: put them on every ramp for consistency).
- Moved the formula out of `AGENTS.md` into `bin/palette-ramp.php` (generator plus `--check`) and a README Palette section; `AGENTS.md` points to both. Corrected its stale primary count. Version 0.2.0.

### Current state
- Branch `feature/brand-color-ramps-v2`, pushed, PR #1 open, not merged. Palette is 40 colors.
- Existing site themes copied from this one are unchanged; their palettes live in their own repos.

### What's next
- Michael reviews and merges.
- Font and spacing size changes are a separate open question.

### Lessons
- The formula only reproduces primary-70-darker with the float form `1 - n * 0.01`; `1 - n / 100` rounds tie values differently.

## 2026-04-13 — MC — Modularize functions.php, balance-text JS → CSS, Style Guide template

### What was done
- **Scaffolded `inc/<category>/` auto-loader** in `functions.php` — 17-line bootstrap that globs `inc/*/*.php` via `get_theme_file_path()` and `require_once`s every match. Drop a file, it loads.
- **Extracted shortcodes** to `inc/shortcodes/shortcode-{current-year,site-title}.php` — one file per shortcode, `wp_basetheme_shortcode_*` function prefix, `wp_date( 'Y' )` for timezone-aware year.
- **Extracted block spacing supports filter** to `inc/block-mods/block-mod-spacing-supports.php` — closure → named `wp_basetheme_block_mod_spacing_supports()`, deduplicated branches with `in_array()`.
- **Vendored `vanilla-balance-text` locally**, pinned to upstream commit `30adf5e9` (2016-12-16, newest available — repo dormant since 2016). Enqueue went to `inc/enqueues/enqueue-balance-text.php` using `wp_add_inline_script()` instead of the old `wp_footer` echo hack.
- **Then replaced the whole JS approach with native CSS** `text-wrap: balance` shipped via `assets/css/text-balance.css` + `inc/enqueues/enqueue-text-balance.php`. Canonical utility class is `.text-balance` (matches stylesheet/enqueue/handle names). Legacy `.balance-text` kept as backward-compat alias.
- **Added `templates/page-style-guide.html`** — comprehensive block showcase: h1–h6, paragraph with inline fmt + dropcap, unordered/ordered/nested lists, blockquote, pullquote, code/preformatted/verse, table, buttons (default + outline), images (default/wide/full aligned, with captions), 6-image gallery, cover with centered heading + CTA, media & text, 2- and 3-column layouts, details/summary, separator style variants (default/wide/dots), spacer. No `wp:post-title`, no `wp:post-content` — demo lives in the template. Registered via `theme.json > customTemplates` as "Style Guide."
- **Relaxed AGENTS.md "no custom CSS" convention** to "narrow utility stylesheets may be enqueued, one file per concern — design tokens still belong in theme.json."
- **Configured `gh auth setup-git` globally** — git HTTPS auth for github.com now routes through gh's token instead of osxkeychain.

### Current state
- Branch `main` at `de6e957`, pushed to `origin/main` (github.com/diffyweb/wp-basetheme)
- Working tree clean
- `functions.php` is a 17-line auto-loader — zero feature logic lives there
- Theme ships zero vendored JavaScript
- 9 commits landed this session
- Style Guide template selectable via page template picker ("Style Guide")

### What's next
- Decide whether to swap `picsum.photos` image placeholders in `page-style-guide.html` for local assets under `assets/images/` — would make the theme fully offline-self-contained
- Consider a `wp-cli` helper to auto-create a "Style Guide" page with the template assigned on fresh installs
- Style guide does not yet exercise query loop, latest posts, navigation, social icons, file block, or site-title/tagline/logo — add if forks need style-guide coverage for those
- The misplaced session log at `context/2026-04-13-modularization-and-style-guide-session.md` needs removal (done in the commit landing alongside this entry)

### Lessons
- **Verify remote git state with ground truth.** `git log origin/main..HEAD` being empty only means local == cached tracking ref. Use `git ls-remote origin refs/heads/<branch>` or `gh api repos/<owner>/<repo>/commits/<branch>` and compare against local HEAD. A silent fetch failure will make the tracking ref lie. Saved to `lessons.md`.
- **Session log goes at project root as `SESSIONLOG.md`**, not in `context/` per-file. `context/` is for architectural decisions, research, and ADRs — durable, topic-scoped artifacts. `SESSIONLOG.md` is the rolling handoff log, one entry per session, newest-first.
- **Atomic commit discipline**: every modularization step committed separately so each commit leaves the theme functional. JS→CSS swap bundled into one commit because partial state would have broken the feature.
