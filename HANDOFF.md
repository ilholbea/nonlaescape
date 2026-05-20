# Handoff — nonlaescape.com local build

This document lives in `app/public/` so it travels with the WordPress repo on GitHub.
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

## Git repos & workflow

There are **two nested git repos**:
- `nonla-local/` — environment-level files (`CLAUDE.md`, `inject-elementor.php`, `sections/`, `pages/`, this `HANDOFF.md`)
- `nonla-local/app/public/` — WordPress files; **this is the one you edit day-to-day**

`app/public` has branches: `main`, `development`, `v1-demo`. Always work on `development`.

### Typical commit + merge flow

```bash
# stage only intentional changes (avoid committing plugin/DB noise)
git -C app/public add sections/instagram.php wp-content/themes/nonla-theme/style.css ...
git -C app/public commit -m "feat: ..."

# merge to main, stay on development
git -C app/public checkout main && git -C app/public merge development --no-edit && git -C app/public checkout development

# push both
git -C app/public push origin development
git -C app/public push origin main
```

The parent `nonla-local/` repo needs a commit only when env-level files change.

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

### 2026-05-20

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