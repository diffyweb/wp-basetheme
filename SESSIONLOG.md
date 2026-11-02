# Session Log

Newest first. Update at the end of every session. See `AGENTS.md > Session Continuity` for full instructions.

---

## 2026-11-02 — MC — Type scale (0.3.0) <!-- voice-lint: ok (heading format set in AGENTS.md) -->

### What was done
- Replaced the 8 font sizes with a 9-step scale: h1 to h6, body, small, extra-small. `norm` folded into `md` (the body size); new `md-plus` (h6) and `lg-minus` (h5); `xs`, `sm`, `lg`, `xl`, `xxl`, `xxxl` kept.
- Values: phone end (`min`) steps by 3^(1/6) ≈ 1.201 from 1rem to 3rem; desktop end (`max`) by 3.36^(1/6) ≈ 1.224 from 1.25rem to 4.2rem. Both anchors are the old `norm` and `xxxl` values, so h1 renders as `xxxl` did. `size` = `min`.
- Mapped h1 to h6 and the body in `theme.json` `styles`; moved the site title to `md-plus` and every `norm` in templates and parts to `md`. No CSS added.
- README Type scale section, AGENTS.md pointer, CHANGELOG 0.3.0, `style.css` 0.3.0.
- Rendered the Style Guide, a post and the front page before and after at 390px (device emulation) and 1440px on a Local copy, then restored it. Every computed heading, body, site-title and footer size matched the table to 0.1px; body text and the post-grid titles didn't change.
- After review: `norm` stays removed, with no alias. A test on real content showed a block still asking for `norm` inherits its parent's size (inside a heading, 16px text became 41 to 53px). Added the README section "Adopting this scale on an existing site": move old `md` first, then `norm` to `md`, then check `sm` and `xs`, with the grep, `wp db search` and `wp search-replace` lines. The search was checked read-only on a real database and the replaces as dry runs.

### Current state
- Branch `feature/type-scale`, branched from `main` while PR #1 (palette, 0.2.0) is open. After #1 merges, rebase: CHANGELOG order, the `style.css` version line, AGENTS.md line 66-67 and the README section placement need a hand merge.

### What's next
- The desktop end assumes WordPress's 1600px maximum viewport. Core takes that width from `layout.wideSize`, and today's `min()` value can't be parsed, so the default holds. If the gutter moves to root padding with a plain `wideSize` such as `1500px`, set `settings.typography.fluid.maxViewportWidth` to `1600px` in the same change, or every size stops growing at 1500px.
- Heading margins are still `3em` top and `1em` bottom on `core/heading`. In `em` they grow with the larger heading sizes; whether they become 2em, or rem tokens, is a separate decision.

### Lessons
- With both `fluid.min` and `fluid.max` set, WordPress ignores `size` when rendering and draws a straight line from `min` at 320px to `max` at 1600px (`wp_get_typography_font_size_value`). The `min()` formula in `wideSize` doesn't parse as a length, so 1600px holds.
- A plain `font-size--norm` search also matches core's old `font-size--normal`, and replacing it would write `--mdal`. End the pattern with `\b` and write a literal pipe as `[|]`, which every grep and PHP's regex read the same way.

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
