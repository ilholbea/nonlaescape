# Handoff — nonlaescape.com local build

This document lives in `app/public/` and is tracked in the repo on GitHub.
Keep it up to date after every session — it's the primary context for future Claude instances.

Related docs: [docs/README.md](docs/README.md) (index), [docs/development.md](docs/development.md), [docs/deploy-hostinger.md](docs/deploy-hostinger.md), [docs/IMPROVEMENT-PLAN.md](docs/IMPROVEMENT-PLAN.md) (what to fix next).

---

## Local environment

| Setting | Value |
|---------|-------|
| Tool | Local by Flywheel |
| Local URL | http://nonla-local-import.local |
| WP Admin | http://nonla-local-import.local/wp-admin |
| Login | `nonlaescape@gmail.com` / see Local by Flywheel app |
| MySQL | `127.0.0.1:10005` — user `root`, database `local`, password in Local app |
| PHP | 8.2.29 |
| MySQL | 8.4.0 |
| WordPress root (Windows path) | `C:\Users\micro-local\Local Sites\nonla-local-import\app\public\` |
| WordPress root (Git Bash path) | `/c/Users/micro-local/Local Sites/nonla-local-import/app/public/` |

### Hostinger plugins (do NOT enable locally)
These crash outside Hostinger servers. On the previous PC they were renamed to `.disabled`; on this PC the folders are not present at all:
- `wp-content/plugins/hostinger.disabled`
- `wp-content/plugins/hostinger-ai-assistant.disabled`
- `wp-content/plugins/hostinger-easy-onboarding.disabled`

Two Hostinger mu-plugins are still present and active locally: `wp-content/mu-plugins/hostinger-auto-updates.php` (turns off auto-updates and routes wordpress.org requests through Hostinger's proxy) and `hostinger-preview-domain.php`.

### Local database is a mock (enriched 2026-11-14)
The original 15 Vietnam photos in `wp-content/uploads/2026/05/` are not on disk on this PC. On 2026-11-14 the local mock was enriched instead:
- Places, gallery, series and project covers now use 18 freely licensed Wikimedia Commons photos (`uploads/2026/11/nonla-*.jpg`).
- The five places have accurate EN/RO copy.
- Vol. 6 is a clean *mock* upcoming edition (dates and price not real).
- The test itinerary day and accommodation were trashed.

Details, copy to paste into production, and photo credits: [docs/content-enrichment.md](docs/content-enrichment.md). The "Current state" audit below describes the database **before** this enrichment; the content findings still apply to production. The local DB is never deployed.

### Admin menu cleanup
Posts and Comments are removed from the WP Admin sidebar (in `functions.php`) — this is intentional.

---

## Git — one repo

**A single repo** rooted at the site folder (`C:\Users\micro-local\Local Sites\nonla-local-import`), remote
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
wp eval-file inject-elementor.php
wp elementor flush-css
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

## Current state — audited 2026-11-14

> Audited on the **May 2026 backup code**; after syncing with GitHub (July 2026 code) the findings were spot-checked — only the EN/RO persistence bug was already fixed. Content findings describe the mock DB *before* the 2026-11-14 enrichment and still need checking on production.

Read-only audit of the local copy: source files, rendered HTML via curl, and read-only database queries. No browser was used, so the CSS findings are derived from the stylesheets and should be confirmed visually. Production could not be reached from the audit machine, so nothing here is verified against the live site. The local database is a test copy; content problems may or may not exist in production.

The fixes are planned in [docs/IMPROVEMENT-PLAN.md](docs/IMPROVEMENT-PLAN.md).

### Implementation map

| Layer | How it works |
|-------|--------------|
| Pages | Home (ID 6), Projects (7), Team (8), Legal (9), Gallery (266) are built by `inject-elementor.php`: each `sections/*.php` or `pages/*.php` file defines an HTML string, the injector wraps it in an Elementor section → column → HTML widget and writes `_elementor_data`. URLs and the contact email are baked in at inject time |
| Home order | hero (with `[nl_countdown]`) → ticker → about → series → places → why → testimonials → instagram → cta |
| Shortcodes | `[nl_countdown]`, `[nl_series]`, `[nl_places]`, `[nl_why_items]`, `[nl_testimonials]`, `[nl_gallery_items]`, `[nonla_projects]`, `[nonla_team]`. They read CPT posts and ACF fields at request time |
| Project detail | `single-nl_project.php`: header, itinerary accordion (`nl_itinerary_day`), accommodations (`nl_accommodation`), both linked by the ACF `project` field |
| ACF groups in the DB only | Project Details, Team Member Details, Series Details, Place Details, Why Item Details, Testimonial Details, Gallery Item Details. Also Hero Section, About Section and Places That Stay With You on the front page, which no code reads |
| Nonla Settings | Settings → Nonla Settings. Options `nonla_registration_url` and `nonla_contact_email`. Neither is saved in the local DB, so the code defaults apply |
| Nav and footer | Hardcoded in `header.php` and `footer.php`. WP menus are not used |
| Styles | One file, `style.css`, tokens in `:root`. Fonts from Google Fonts, enqueued in `functions.php` |
| Plugins active | elementor, advanced-custom-fields, instagram-feed, all-in-one-wp-migration |

JS behaviours:

| Behaviour | Where |
|-----------|-------|
| EN/RO switch, nav show/stick on scroll, mobile menu, hero video low-data fallback and autoplay retry | inline script at the end of `header.php` |
| Scroll reveal (`.nl-reveal`), tap-to-open destination cards on touch, `nlFilterProjects()` | `wp_footer` script in `functions.php` |
| Countdown tick | inline script printed by `shortcodes/countdown.php` |
| Itinerary accordion `nlToggleDay()` | inline script in `single-nl_project.php` |
| Instagram carousel layout override and arrows | `assets/js/ig-carousel.js` |

### Content inventory (local)

| Page | What is there | State |
|------|---------------|-------|
| Home `/` | Hero, stats (5+ Years, 5 Editions, 100+ Travelers), ticker, Who We Are, two series cards, five destination cards, six Why items, three testimonials, Instagram strip, CTA with email | Testimonials are lorem ipsum. Destination hover text is placeholder. Instagram strip is empty locally. Countdown is not shown (no future upcoming project). CTA email differs from the footer |
| Projects `/projects/` | Header, five filter tabs, two cards: Vol. 6 (Upcoming) and Vol. 5 (Completed) | Vol. 6 is test data. Both Register buttons go to `http://www.google.com`. "Vietnam Undiscovered" tab is empty |
| Project detail `/project/{slug}/` | Header with meta and cover; itinerary and accommodation sections | No itinerary or accommodations for either project. One test day and one test hotel are linked to a deleted project |
| Team `/team/` | Header, nine members, "We Travel With You" CTA | All bios lorem ipsum, no photos, roles EN only |
| Gallery `/gallery/` | Header, nine gallery items | Three items have no photo. Default page template adds a second `<h1>`. Markup has an extra `</div>` |
| Legal `/legal/` | Header, seven sections of legal text, CIF 49845083, Câmpulung Muscel | Body is EN only. Shows `contact@nonlaescape.com` |
| FAQ `/faq/` | Header only | Orphan page, not in the nav, not in git. Shows raw `[nl_faq]` and links to `nonla-local.local` |

Always EN regardless of the switch: series cards, destination tag/name/description, Why items, testimonials, footer tagline, legal body, destination pills, team city.

### Colour palette

Tokens are in `style.css` lines 10–25. Ratios are WCAG 2.x; AA needs 4.5:1 for normal text and 3:1 for large text.

| Token | Hex | Role | Contrast note |
|-------|-----|------|---------------|
| `--cream` | `#FAF5EC` | Intended page background, mobile menu, Instagram strip | Body background is in practice `#FFFFFF` (see Known issues 2) |
| `--parch` | `#F2E8D4` | Page headers, destinations and testimonials bands, footer text | |
| `--white` | `#FFFDF7` | Text on dark imagery | 19.2:1 on the overlay colour; over video it depends on the frame |
| `--card` | `#FFF8EE` | Card backgrounds | |
| `--text` | `#1A0E06` | Headings; footer background | 17.4:1 on cream, 15.6:1 on parch. Pass |
| `--text2` | `#4A3318` | Body paragraphs | 10.9:1 on cream, 9.7:1 on parch. Pass |
| `--muted` | `#7A6040` | Meta labels, breadcrumb, stuck nav links | 5.4:1 on cream, 4.8:1 on parch. Pass, little margin |
| `--terra` | `#B85520` | Accent text, buttons, ticker, badges (33 uses) | As text: 4.44:1 on cream, 3.96:1 on parch, 4.12:1 inside pills. **Fail** for small text. White on terra 4.82:1, pass |
| `--terra-l` | `#D4703A` | Hover background | White on it 3.40:1. **Fail** |
| `--gold-l` | `#D4A030` | Accent on dark (hero, CTA, footer dot) | 8.0:1 on dark. Never use on light (2.18:1) |
| `--border` | `#DDD0B0` | Borders | 1.4:1, decorative only |
| `--shadow` | `rgba(26,14,6,.08)` | Card shadows | |
| `--gold` | `#B8841A` | Unused | |
| `--forest` | `#2C4A1A` | Unused | |

Other failures:

| Pair | Ratio |
|------|-------|
| Inactive language button in the mobile menu (white at 45% on cream) | 1.03:1, invisible |
| Filter tab on hover (terra text on Hello Elementor pink `#CC3366`) | 1.03:1 |
| Footer logo on hover (`#333366` on footer) | 1.63:1 |
| Footer copyright line / column heads / social links | 2.56 / 3.13 / 3.92:1 |
| Ticker text on terra | 4.11:1 |
| "Completed" badge on the project detail page | 4.25:1 |
| Hero stat labels and inactive language button over the video (white at 45%) | 4.45:1 at best |

Colours outside the tokens: `#fff` (8 uses), alpha versions of white, terra, parch and text written as `rgba()`, a separate overlay dark `rgba(20,10,4,…)`, `#FAF5EC` in `pages/legal.php` line 60, and Hello Elementor's `#CC3366` / `#333366` / `#333` leaking in.

The palette itself is coherent: warm neutrals, one terracotta accent, gold reserved for dark surfaces. The problems are the accent being slightly too light for small text and the heavy use of low-alpha text.

### Typography

| Font | Loaded | Used for |
|------|--------|----------|
| Cormorant Garamond | 400, 600, italics | Headings, card titles, quotes. Display sizes use `clamp()`, up to 156px in the hero |
| Bebas Neue | 400 | Stats, countdown digits, text logos |
| Inter | 300, 400, 500, 600 | Declared for body text but not applied: the parent reset overrides it, so body text renders in the system font |
| Roboto, Roboto Slab | 18 styles each, added by Elementor | Nothing |

Body copy is 15–19px at weight 300. Labels, buttons and badges are 10–14px uppercase with wide letter-spacing. Weight 700 is used in seven rules but not loaded.

### Known issues

Functional
1. **Language switch does not persist.** `applyLang()` runs inline in `header.php` (line 134) before the page body exists, so a saved RO choice only translates the nav. Desktop nav labels the Legal link "Contact" in RO (line 20).
2. **Parent CSS overrides the theme.** Hello Elementor's `reset.css` is printed after `style.css`. Effects: white body background, system font instead of Inter, pink or navy hover and focus states on buttons and links, itinerary titles cannot wrap.
3. **Two contact emails.** `inject-elementor.php` line 76 falls back to `contact@nonlaescape.com`; the theme falls back to `nonlaescape@gmail.com`.
4. **Register buttons go to google.com** (from the Vol. 6 test project's `register_form_url`).
5. **Gallery shortcode** only handles exactly 5 items; the Gallery page lacks the Elementor Full Width template.
6. **Project status is manual.** Vol. 6's dates have passed but it still says Upcoming. The countdown parses the free-text English `dates_en` field.

Content
7. Lorem ipsum testimonials with invented names; lorem ipsum team bios; placeholder hover text on all destinations.
8. Test data: Vol. 6 project (`Dest1, Dest2, Dest3`, cover `goku.jpeg`), test itinerary day and accommodation, orphan FAQ page, orphan `nl_destination` post.
9. Romanian copy: "Găsește-ți Călătorie." (`sections/cta.php`), "Povestea Lor" for "Their Stories", mixed tu/voi, Title Case headings, month names in English order in `dates_ro`.
10. The site does not say how to volunteer, donate or partner. Contact is a single email link. Terms for the trips vary (Projects, Escapes, Editions, Trip).
11. Legal text mentions analytics cookies; no analytics script was found. No Romanian version.
12. No meta description, Open Graph tags or favicon (the stored site icon points to a deleted attachment). Romanian text is not indexable because translation is JS-only.

Performance
13. Home page loads about 108 MB: `hero-video-1080p.mp4` 40.1 MB, `boats.jpg` 25.9 MB, `tamcoc.jpg` 19.8 MB, `hero.jpg` 11.3 MB, `ninhbinh.jpg` 11.2 MB. No lazy loading. Four unused photos (53 MB) sit in the theme and in git.
14. `style.css` is enqueued with a fixed version `1.0`, so caches keep old CSS after a deploy.

Fragile or risky code
15. `wp-content/mu-plugins/nonla-setup.php` calls `switch_theme('nonla-child')`, which does not exist. Inert only while the option `nonla_setup_done` is set.
16. **Do not run `populate-content.php` on a populated site.** Its photo path is `C:/Users/ionut/Desktop/Photos/modified/` (line 10); with the photos missing it overwrites every photo field with 0, resets team bios to lorem ipsum and re-creates the lorem testimonials.
17. `inject-elementor.php`, `populate-content.php`, `sections/`, `pages/` and the `.md` docs sit in the web root with no direct-access guard. `local-phpinfo.php` and `local-xdebuginfo.php` are Local's and must never be uploaded.
18. `applyLang()` writes `data-en`/`data-ro` values with `innerHTML`, which undoes the server-side escaping of admin-entered fields. `shortcodes/places.php` line 33 echoes without `esc_html`.
19. Duplication: the default registration URL appears in 4 places, the default email in 6, the Instagram and Facebook URLs in 5. Card and detail templates repeat the same lookup logic with small differences.
20. Most ACF field groups exist only in the database, so they are not in git.
21. The inner repo still tracks 218 All-in-One WP Migration files, `wp-content/litespeed/`, the theme photos and an empty file named `posts}`. `wp-content/README.md` is out of date (old URL, old branch name).
22. A full All-in-One WP Migration import would carry the weak local admin password and the test content to production. See [docs/deploy-hostinger.md](docs/deploy-hostinger.md) for the current deploy method.

Accessibility
23. No `prefers-reduced-motion` handling, no visible focus style, no fallback when JS is off (`.nl-reveal` content stays invisible), closed mobile menu is still tabbable, home nav is hidden until the visitor scrolls.

---

## Session log

### 2026-11-14

**Audit and docs, no code changes.** Added the "Current state" section above and [docs/IMPROVEMENT-PLAN.md](docs/IMPROVEMENT-PLAN.md). Corrected this file: CPT list, ACF description, repo layout, Hostinger plugin status on this PC, missing git remote.

**Docs and tooling.** Added `docs/` (development, Hostinger deploy, content editing). Hostinger SSH access is confirmed. Added project Claude skills, agents and plugins in `.claude/`; Playwright drives Brave.

**Local mock content enriched** (DB only, no code changes). Added accurate places copy and Commons photos; see [docs/content-enrichment.md](docs/content-enrichment.md).

**Synced with GitHub.** The May backup was replaced by the July 2026 single repo from GitHub (`development`). Today's docs, `.claude/` tooling and path updates were re-applied on top, and `.claude/settings.local.json` was untracked (it is per-machine). Inject + flush were run locally on the July layout.

---

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

Code and design fixes are in [docs/IMPROVEMENT-PLAN.md](docs/IMPROVEMENT-PLAN.md). Content still owed by the NGO:

| Item | Notes |
|------|-------|
| Team bios | Lorem ipsum — need real bios (EN and RO), plus `role_ro` |
| Team photos | Not on public site — needs sourcing |
| Testimonials | Lorem ipsum — need real quotes from Vol. 5 travelers |
| Destination hover text | Placeholder on production; EN/RO copy ready in [docs/content-enrichment.md](docs/content-enrichment.md) |
| Populate Vol. 5 itinerary | 16 days from Anexa nr. 2 (Google Drive) → WP Admin → Itinerary Days |
| Populate Vol. 5 accommodations | 4 hotels from Anexa nr. 1 → WP Admin → Accommodations |
| Remove test project | Was "Vol. 100", now "Vietnam Escape Vol. 6" plus its test day, hotel and images |
| Real next edition | Dates, price, registration form URL |
| Production deploy | Follow [docs/deploy-hostinger.md](docs/deploy-hostinger.md) |

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