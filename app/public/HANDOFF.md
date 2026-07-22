# Handoff — nonlaescape.com local build

This document lives in `app/public/` and is tracked in the repo on GitHub.
Keep it up to date after every session — it's the primary context for future Claude instances.

---

## Local environment

| Setting | Value |
|---------|-------|
| Tool | Local by Flywheel |
| Local URL | http://nonla-local.local |
| WP Admin | http://nonla-local.local/wp-admin |
| Login | `nonlaescape@gmail.com` / see Local by Flywheel app |
| MySQL | `127.0.0.1:10005` — user `root`, database `local`, password in Local app |
| PHP | 8.2.29 |
| MySQL | 8.4.0 |
| WordPress root (Windows path) | `C:\Users\ionut\Local Sites\nonla-local\app\public\` |
| WordPress root (WSL path) | `/mnt/c/Users/ionut/Local Sites/nonla-local/app/public/` |

### Disabled plugins (Hostinger-specific — do NOT re-enable locally)
These crash outside Hostinger servers. They were renamed to `.disabled` to keep them inert:
- `wp-content/plugins/hostinger.disabled`
- `wp-content/plugins/hostinger-ai-assistant.disabled`
- `wp-content/plugins/hostinger-easy-onboarding.disabled`

### Admin menu cleanup
Posts and Comments are removed from the WP Admin sidebar (in `functions.php`) — this is intentional.

---

## Git — one repo

**A single repo** rooted at the site folder (`C:\Users\ionut\Local Sites\nonla-local`), remote
`https://github.com/ilholbea/nonlaescape.git`. Every branch contains the whole thing:
`CLAUDE.md`, `conf/`, and `app/public/**`.

Branches: **`development`** (always work here), `main`, and the stale `v1-demo`.

> **Changed 2026-07-22.** This used to be *two nested repos* — a root env repo on `master` plus a
> separate `app/public` repo — joined by a gitlink with no `.gitmodules`, so cloning `master`
> produced an empty `app/public`. They were unified with a subtree merge (`ae0cf3a`); all 57
> commits are preserved and `master` has been deleted. **Old `git -C app/public …` commands no
> longer apply** — paths are now relative to the site root.

### Typical commit + merge flow

```bash
# stage only intentional changes (avoid committing plugin/DB noise)
git add app/public/sections/instagram.php app/public/wp-content/themes/nonla-theme/style.css
git commit -m "feat: ..."

# merge to main, stay on development
git checkout main && git merge development --no-edit && git checkout development

# push both
git push origin development
git push origin main
```

---

## How to inject Elementor pages

Section/page HTML is assembled in `inject-elementor.php` and pushed into Elementor's DB meta via WP-CLI.
Run after changing anything under `sections/` or `pages/`:

```bash
wp eval-file inject-elementor.php --path=/mnt/c/Users/ionut/Local\ Sites/nonla-local/app/public
wp elementor flush-css --path=/mnt/c/Users/ionut/Local\ Sites/nonla-local/app/public
```

Changes to `style.css`, `header.php`, or shortcode PHP files take effect immediately on page reload — no injection needed.

---

## Key file locations

| What | Where (relative to `app/public/`) |
|------|------------------------------------|
| Theme CSS | `wp-content/themes/nonla-theme/style.css` |
| Nav HTML + global JS | `wp-content/themes/nonla-theme/header.php` |
| Footer JS (reveal, touch, filter) | `wp-content/themes/nonla-theme/functions.php` |
| Home page sections | `sections/*.php` |
| Inner page sections | `pages/*.php` |
| Elementor injector | `inject-elementor.php` |
| Shortcodes + CPT registrations | `wp-content/themes/nonla-theme/shortcodes/` |
| Project detail template | `wp-content/themes/nonla-theme/single-nl_project.php` |

---

## Architecture overview

- **Theme:** `nonla-theme` (Hello Elementor child theme) — all custom code lives here, nothing in Elementor's visual editor
- **CPTs:** `nl_project`, `nl_place`, `nl_team_member`, `nl_itinerary_day`, `nl_accommodation`
- **ACF:** free version only — field groups registered in PHP (`shortcodes/project-acf-fields.php`, `shortcodes/place-acf-fields.php`)
- **Elementor:** used only as a page layout wrapper; actual HTML injected via WP-CLI (`inject-elementor.php`). Never use the Elementor visual editor — it will overwrite injected content.
- **Language:** EN/RO toggle stored in `localStorage`. Elements with class `.t` and `data-en`/`data-ro` attributes are swapped by JS in `header.php`.
- **Instagram feed:** Smash Balloon Social Photo Feed plugin (`[instagram-feed]` shortcode). Plugin UI (header, bio, buttons) hidden via CSS; styled as horizontal scroll-snap carousel.

---

## Session log

### 2026-05-21 (continued)

**Mobile fixes**
- Carousel arrows hidden on mobile via `@media (max-width: 960px)` — `(hover: none)` was unreliable across browsers
- Places hover text no longer triggers on mobile tap via same breakpoint — `:hover` on touch devices fires on tap and never clears, so overriding it with hidden state at 960px fixes the always-visible bug
- IG logo: switched from CSS `::after` (unreliable when Smash Balloon overrides parent styles) to JS-injected `<span class="nl-ig-logo">`. JS also explicitly sets `sbi_photo_wrap` inline styles (`position:relative; width:100%; height:100%`) so the logo is always correctly positioned regardless of what the plugin does on mobile
- Arrow buttons restyled to use `var(--terra)` background with shadow for better visibility on desktop
- Touch scroll blur/echo fixed: added `touchstart` listener (pauses rAF loop while finger is on screen) and `touchend` listener (resumes auto-scroll after 1500ms). Without this the rAF loop fought native touch scroll causing a jittery blur effect and auto-scroll stopped working after lift.
- Auto-scroll removed entirely — could not get it to work reliably on mobile without interfering with native touch scroll. Carousel is now swipeable with finger (native overflow-x scroll) and arrow buttons on desktop. No JS-driven auto-scroll.

---

### 2026-05-21 (earlier)

**Project cover image — background-image approach**
- Switched from `<img>` tag to `background-image` inline style on `.nl-project-cover` (same pattern as place cards) — eliminates the intrinsic aspect-ratio conflict that prevented img from filling the container height
- Zoom-on-hover done via `::after` pseudo-element inheriting the background
- CSS changed to `display: flex` on card, `flex: 0 0 420px` on cover, `background-size: cover; background-position: center` on cover
- Mobile: `flex-direction: column` on card, `aspect-ratio: 16/9` on cover
- File: `shortcodes/projects.php`, `style.css`

**Register buttons — arrow removed**
- Removed SVG arrow from "Register Now" and "Register for Next Edition" buttons

**Instagram carousel — spacing and background**
- Added `padding: 40px 0` to `.nl-ig-wrap` so the cream background visually separates the carousel from the parch testimonials section above and the CTA below
- Background kept as `var(--cream)`

**Instagram carousel — extracted to separate JS file**
- Moved all carousel JS from `functions.php` footer script to `assets/js/ig-carousel.js`
- Enqueued via `wp_enqueue_script` in `functions.php` (loads in footer)
- Fixed `scroll-behavior: smooth` bug — was interfering with rAF auto-scroll loop; removed from container, arrow buttons use `scrollBy({ behavior: 'smooth' })` instead
- Gap between photos: 16px (updated in both CSS and JS override)
- Duplicate `position: relative` on `.nl-ig-wrap` consolidated

**Deploy workflow clarified** — FTP theme folder for code changes; full AIWPM migration no longer needed for day-to-day deploys; SSH + WP-CLI on Hostinger for Elementor page re-injection when sections/pages change

---

### 2026-05-20 (continued)

**Instagram carousel — horizontal scroll fix**
- Smash Balloon's JS was overriding flex layout with its own inline styles, causing vertical-only scroll
- Added MutationObserver in `functions.php` footer script that forces `display:flex; overflow-x:auto; scroll-snap-type:x mandatory` via inline styles after the plugin renders — runs on initial load and on any DOM mutation inside `.nl-ig-wrap`
- Item height bumped 280px → 360px in both CSS and the JS override
- IG logo enlarged 20px → 28px, stroke-width 2 → 2.5, opacity 0.8 → 0.9

**Deploy note:** CSS-only changes aren't enough for Smash Balloon — always check the JS observer is present if the carousel stops scrolling after a plugin update.

---

### 2026-05-20 (earlier)

**Instagram section → carousel only**
- Removed custom `nl-ig-header` div from `sections/instagram.php`
- CSS hides all Smash Balloon plugin UI (profile, bio, follow/load buttons)
- `#sbi_images` forced into horizontal flex scroll-snap carousel (280×280px items)
- White IG logo added bottom-right of each photo via `.sbi_photo_wrap::after` (SVG data URI)

**Nav — hidden by default, slides in on scroll**
- `.nl-nav` starts `opacity: 0; transform: translateY(-100%)`
- `.nl-nav-visible` added at `scrollY > 20`, `.stuck` at `scrollY > 50`
- Inner pages: both classes applied immediately so nav is always visible there
- Files: `style.css`, `header.php`

**Hero — metered connection fallback**
- Checks `navigator.connection.saveData` or `effectiveType` (`slow-2g`/`2g`) on `DOMContentLoaded`
- If detected, replaces `<video>` with `<img poster>` (class preserved → same CSS applies)
- File: `header.php`

**Places cards — background image fix**
- `background-size: 170%` → `cover` so images always fill card regardless of source dimensions
- Removed background-size hover zoom (was causing empty-space artifacts)

**Places hover text — always-visible bug fixed**
- Added `visibility: hidden/visible` alongside existing `opacity`/`max-height` transition
- File: `style.css`

---

### 2026-05-14 to 2026-05-19

**Instagram feed added** — Smash Balloon plugin installed, shortcode added to home page between Testimonials and CTA

**Hover details on destination cards** — `hover_details_en`/`hover_details_ro` ACF fields on `nl_place`; revealed on `:hover`/`.nl-active`

**Project card fixes** — cover image `position: absolute; inset: 0` (reliable fill); SVG placeholder when no cover; hero video autoplay fix; social-link fallback for completed projects with no next edition

---

### 2026-05-14 (original build)

**Project detail pages** — `single-nl_project.php`: header, itinerary accordion, accommodations grid

**Child CPTs** — `nl_itinerary_day` and `nl_accommodation`, each linked to a parent project via ACF Post Object

**Other** — "View Details" button on project cards; contact email corrected to `nonlaescape@gmail.com`

---

## Remaining backlog

| Item | Notes |
|------|-------|
| Team bios | Lorem ipsum — need real bios |
| Team photos | Not on public site — needs sourcing |
| Testimonials | Lorem ipsum — need real quotes from Vol. 5 travelers |
| Populate Vol. 5 itinerary | 16 days from Anexa nr. 2 (Google Drive) → WP Admin → Itinerary Days |
| Populate Vol. 5 accommodations | 4 hotels from Anexa nr. 1 → WP Admin → Accommodations |
| Remove Vol. 100 | Test project used for countdown — delete before deploy |
| Production deploy | All-in-One WP Migration, media + plugins excluded (~158MB); Hostinger already has plugins/media; **reset admin password immediately** after import (local uses `admin123`) |

---

## How the local environment was set up (for reference)

1. Exported live site from Hostinger WP Admin via All-in-One WP Migration (.wpress, ~330MB)
2. Created blank site in Local by Flywheel
3. Increased PHP upload limit via `php.ini` to allow the large import
4. Imported .wpress (files restored; DB partially failed at 93%)
5. Fixed MySQL startup issues (stale PID file, file lock from crashed import)
6. Disabled Hostinger-specific plugins that fatal-error outside Hostinger servers
7. Ran WP DB update (`wp core update-db`)
8. Reset admin password via MySQL CLI