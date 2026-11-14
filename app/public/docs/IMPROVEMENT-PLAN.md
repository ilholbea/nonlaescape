# Improvement plan

Written 2026-11-14 from a read-only audit of the local copy. Nothing here has been executed yet.

> **Re-checked 2026-11-14 against the July 2026 code from GitHub.** The audit first ran on an older May backup. After syncing with GitHub, only the EN/RO persistence part of item 1 turned out to be already fixed. Items 2, 3, 9, 10, 11 and 12 were spot-checked and still apply. Line numbers quoted below may have shifted, so search by the quoted code. In the **local mock DB**, item 4's test project and test records are already cleaned up ([content-enrichment.md](content-enrichment.md)), but production still needs checking.
Findings and evidence are in [../HANDOFF.md](../HANDOFF.md) under "Current state — audited 2026-11-14".

**Read this first**

- The audit covered the local site only. Production could not be reached from the audit machine, so every content item below must be checked against production WP Admin before acting.
- Content (projects, team, testimonials, places, settings) is edited in production WP Admin, not deployed. Code goes up as described in [deploy-hostinger.md](deploy-hostinger.md).
- The CSS findings come from reading the stylesheets and the rendered HTML, not from a browser. Confirm each one visually before and after the fix.
- Effort: S = under an hour, M = half a day or needs input from the NGO.
- "Inject" means `wp eval-file inject-elementor.php && wp elementor flush-css`, locally and again on production.

| # | Item | Group | Effort | Needs |
|---|------|-------|--------|-------|
| 1 | Make the EN/RO switch work | Quick wins | S | Theme files |
| 2 | Load the theme CSS after the parent reset | Quick wins | S | Theme files |
| 3 | One contact email everywhere | Quick wins | S | Inject + WP Admin |
| 4 | Remove test and orphan content | Quick wins | S | WP Admin |
| 5 | Replace or hide lorem ipsum | Content & copy | S to hide, M to replace | WP Admin |
| 6 | Fix placeholder and Romanian copy | Content & copy | S | Inject + WP Admin |
| 7 | Add a "Get involved" block | Content & copy | M | Inject, NGO input |
| 8 | Legal page: accuracy and Romanian version | Content & copy | M | Inject, legal advisor |
| 9 | Fix the contrast failures | Design & accessibility | S | Theme files |
| 10 | Motion, no-JS and keyboard basics | Design & accessibility | S | Theme files |
| 11 | Shrink the photos and the hero video | Tech & performance | M | Theme assets by FTP |
| 12 | Code hygiene | Tech & performance | S | Theme files, one inject |

---

## Quick wins

### 1. Make the EN/RO switch work

- **What:**
  - ~~Run `applyLang(...)` on `DOMContentLoaded`~~ — **already fixed in the July 2026 code** (it re-applies the saved language on `DOMContentLoaded`).
  - In `style.css`, add `.nl-mobile-menu .nl-lang-btn { color: var(--muted); }` and a matching hover colour. The inactive button in the mobile menu is near-white on cream (1.03:1), so "RO" is invisible on phones.
  - In `header.php` line 20, change `data-ro="Contact"` on the Legal link to match the mobile menu (`Juridic`) or the footer (`Mențiuni Legale`).
  - Optional: default to RO when `navigator.language` starts with `ro` and nothing is saved.
- **Why:** the audience is Romanian. Today a Romanian visitor on a phone cannot see the RO button, and on desktop has to click RO again on every page.
- **Files:** `wp-content/themes/nonla-theme/header.php`, `wp-content/themes/nonla-theme/style.css`
- **Verify:** choose RO, open `/projects/` and `/team/`: headings and buttons are Romanian without clicking again. At 400px width, open the menu: both EN and RO are readable.

### 2. Load the theme CSS after the parent reset

- **What:**
  - In `functions.php` line 22, make `nonla-style` depend on `['hello-elementor', 'hello-elementor-theme-style', 'hello-elementor-header-footer']` and drop the `parent-style` enqueue (that file is only a header comment).
  - In `style.css`, add explicit `background-color` and `color` for `:hover` and `:focus` on `.nl-lang-btn`, `.nl-filter-btn`, `.nl-nav-burger`, `.nl-mobile-close`, `.nl-ig-arrow`, `.nl-itin-head`, and `white-space: normal` on `.nl-itin-head`.
  - Add `:hover` colours for `.nl-btn-outline`, `.nl-series-link`, `.nl-accomm-link`, `.nl-footer-logo`, `.nl-mobile-logo`, `.nl-breadcrumb a`, `.nl-team-email a`.
- **Why:** Hello Elementor's `reset.css` loads after `style.css` and wins on equal specificity. The body is white instead of cream, body text uses the system font instead of Inter, and hover or focus states pick up Hello's pink `#CC3366` and navy `#333366`. Filter tabs on hover become terracotta text on pink (1.03:1).
- **Risk:** this visibly changes the site (cream background, Inter everywhere). It is the intended design, but compare before and after.
- **Files:** `wp-content/themes/nonla-theme/functions.php`, `wp-content/themes/nonla-theme/style.css`
- **Verify:** in the page source, `nonla-style-css` comes after `hello-elementor-header-footer-css`. Body background is `#FAF5EC`. Hovering and tabbing through filter tabs, language buttons and the itinerary accordion shows no pink.

### 3. One contact email everywhere

- **What:**
  - Confirm with the NGO which address is real.
  - Change the fallback in `inject-elementor.php` line 76 from `contact@nonlaescape.com` to the same default the theme uses (`nonlaescape@gmail.com`), then inject.
  - Save the address and the registration form URL once in WP Admin → Settings → Nonla Settings. Neither option is stored in the local database, so everything currently runs on code defaults.
- **Why:** the home page CTA and the Legal page show `contact@nonlaescape.com`, the footer shows `nonlaescape@gmail.com`. If the first mailbox does not exist, enquiries from the main call to action are lost.
- **Files:** `inject-elementor.php`; production WP Admin
- **Verify:** `curl -s http://nonla-local-import.local/ | grep -o 'mailto:[^"]*' | sort -u` returns one address. Same for `/legal/`.

### 4. Remove test and orphan content

- **What (WP Admin, check each on production):**
  - Page "FAQ" (`/faq/`): set to Draft. It is public, shows the raw text `[nl_faq]` and links to the old domain `nonla-local.local`.
  - Project "Vietnam Escape Vol. 6": test data. Description `1 / 2 / best`, destinations `Dest1, Dest2, Dest3`, Register button goes to `http://www.google.com`, cover image is `goku.jpeg`, dates (October 14 – 29, 2026) are already past but the status is Upcoming. Delete it or replace it with the real next edition.
  - "Test itinerary Day 1" and "Accommodation Test 1": linked to a deleted project. Delete.
  - Media `goku.jpeg` and `polished-colors-wave-4k-8u.jpg`: delete.
  - Page "Gallery": set Template to "Elementor Full Width". It uses the default template, which adds a second `<h1>Gallery</h1>`.
- **Why:** "Register for Next Edition" on the Vol. 5 card inherits the google.com link from Vol. 6, so the only way to sign up is broken.
- **Files:** none (database content)
- **Verify:** `/faq/` returns 404. `/projects/` has no link to google.com. `/gallery/` has one `<h1>`.

---

## Content & copy

### 5. Replace or hide lorem ipsum

- **What:**
  - Testimonials: all three are lorem ipsum with invented names ("Andreea M.", "Radu C.", "Michael S."). Set them to Draft now. The section's heading still renders when there are none, so also remove `sections/testimonials.php` from the home assembly in `inject-elementor.php` until real quotes exist, or publish real ones the same day.
  - Team: all nine bios are lorem ipsum, there are no photos, and `role_ro` is empty. Clear the bio fields (the card hides an empty bio) until real text arrives.
- **Why:** invented testimonials presented as real are a trust and legal risk for an NGO.
- **Files:** WP Admin → Testimonials, Team Members; optionally `inject-elementor.php`
- **Verify:** `curl -s http://nonla-local-import.local/ http://nonla-local-import.local/team/ | grep -ci "lorem ipsum"` returns 0.

### 6. Fix placeholder and Romanian copy

- **What:**
  - Places (WP Admin → Places): the hover text on all five cards is `Some description about Hanoi... 1-2 sentences.` Write real text or empty the field. Fill the RO field too.
  - `sections/cta.php` line 17: `data-ro="Călătorie."` gives "Găsește-ți Călătorie.", which is ungrammatical. Use `Călătoria.`
  - `sections/testimonials.php` lines 11–12: "Povestea Lor" is singular; EN is "Their Stories". Use "Poveștile lor".
  - `sections/hero.php` lines 43–51 and `sections/ticker.php` line 13: "5+ Years", "5 Editions", "100+ Travelers" and "Since 2020" are hardcoded. Confirm the numbers with the NGO.
  - Pick one word for the trips. "Projects", "Escapes", "Editions" and "Trip" all name the same thing, and the nav has two links to `/projects`.
  - Romanian tone: most copy says "tu", but `pages/team.php` ("Cu Voi"), `shortcodes/team.php` ("contactați-ne") and `single-nl_project.php` ("Unde Stați") say "voi". Pick one.
  - Home advertises "Two Series" but there is no Vietnam Undiscovered edition, and that filter tab shows an empty list. Either add an edition or soften the series card.
- **Why:** placeholder text and grammar slips are visible on the home page.
- **Files:** `sections/cta.php`, `sections/testimonials.php`, `sections/hero.php`, `sections/ticker.php`, then inject; WP Admin → Places
- **Verify:** `curl -s http://nonla-local-import.local/ | grep -c "Some description about"` returns 0. Switch to RO and read the home page top to bottom.

### 7. Add a "Get involved" block

- **What:** a small three-card section on the home page, before the final CTA:
  - Join a trip → `/projects`
  - Volunteer or become a Cultural Ambassador → mailto
  - Support or partner with us → mailto. If the association is registered for it, mention redirecting 3.5% of income tax (Formular 230). The NGO must confirm.
- **Why:** the site only offers trip registration. It does not say how to volunteer, donate or partner, and the only contact route is one email link.
- **Files:** new `sections/involved.php`, one `require` and one row in `inject-elementor.php`, a few rules in `style.css` (reuse `.nl-why-grid` and `.nl-why-item`); then inject
- **Verify:** the block renders in EN and RO, and each card has a working link.

### 8. Legal page: accuracy and Romanian version

- **What:**
  - Add `class="t"` with `data-en`/`data-ro` to the body of `pages/legal.php`. Only the header is bilingual today.
  - "This site uses cookies for basic analytics only": no analytics script was found on the local site. Make the sentence match what production really runs.
  - Name the third parties that receive visitor data: Google Forms (registration), Google Fonts, the Instagram feed.
  - Ask the association's legal advisor to review the full text and say whether a street address, registration number or consumer-protection links are required.
- **Why:** a Romanian association publishes its terms in English only, and the privacy text does not match the site.
- **Files:** `pages/legal.php`, then inject
- **Verify:** `/legal/` in RO shows Romanian body text. The legal advisor signs off.

---

## Design & accessibility

### 9. Fix the contrast failures

- **What (all in `style.css`):**

| Change | Now | After |
|--------|-----|-------|
| `--terra: #B85520` → `#A04516` | 3.96:1 on parch, 4.44:1 on cream, 4.12:1 in pills | 5.14:1 on parch; white on terra 6.25:1 |
| Primary button and nav CTA hover: darker (`#8F3D12`) instead of `--terra-l` | white on `--terra-l` 3.40:1 | 7.38:1 |
| Footer text alphas → at least `.6` (`.nl-footer-social-link`, `.nl-footer-head`, `.nl-footer-bottom`) | 3.92, 3.13, 2.56:1 | 6.10:1 |
| `.nl-stat-l` alpha `.45` → `.7`; `.nl-lang-btn` alpha `.45` → `.75` | 4.45:1 at best, less over a bright frame | passes over a typical frame |
| `.nl-ticker-item` colour → `#fff` | 4.11:1 | passes with the new terra |
| `.nl-project-status-badge.completed` text → `var(--white)` | 4.25:1 on the detail page | 5.57:1 |

- **Why:** eyebrows, breadcrumb links, outline buttons, footer links and the copyright line fail WCAG AA for small text.
- **Risk:** `--terra` is used 33 times, so the whole site shifts slightly darker. Show the NGO a before/after.
- **Files:** `wp-content/themes/nonla-theme/style.css`
- **Verify:** re-check the pairs above with any contrast checker; all are 4.5:1 or better.

### 10. Motion, no-JS and keyboard basics

- **What:**
  - `style.css`: add `@media (prefers-reduced-motion: reduce)` that stops the ticker animation and shows `.nl-reveal` content immediately.
  - `header.php`: add `<noscript><style>.nl-reveal{opacity:1;transform:none}.nl-nav{opacity:1;transform:none}</style></noscript>`. Without JavaScript most content stays invisible.
  - `style.css`: add a visible `:focus-visible` outline for links and buttons.
  - `header.php`: set `aria-pressed` on the language buttons, and add the `inert` attribute to the closed mobile menu so keyboard users cannot tab into it.
  - Optional: show the nav at the top of the home page. It is hidden until 20px of scroll, so the menu and language switch are not visible on arrival.
- **Why:** basic accessibility for keyboard and motion-sensitive visitors.
- **Files:** `wp-content/themes/nonla-theme/style.css`, `wp-content/themes/nonla-theme/header.php`
- **Verify:** tab through the home page: focus is always visible and never lands in the closed menu. With JavaScript disabled, all sections are visible.

---

## Tech & performance

### 11. Shrink the photos and the hero video

- **What:**
  - Re-export the four photos in use at about 2000px wide, JPEG quality around 80, under 400 KB each:

| File | Now | Used by |
|------|-----|---------|
| `boats.jpg` | 25.9 MB, 8140×6124 | `sections/about.php` |
| `tamcoc.jpg` | 19.8 MB, 7240×5448 | `sections/about.php` |
| `hero.jpg` | 11.3 MB, 4060×3052 | `sections/hero.php` (poster) |
| `ninhbinh.jpg` | 11.2 MB, 4080×3072 | `sections/cta.php` |

  - Re-encode `hero-video-1080p.mp4` (40.1 MB) to 720p H.264 without audio, aiming for under 5 MB.
  - Delete the four unused photos: `hanoi.jpg`, `garden.jpg`, `halong.jpg`, `phuquoc.jpg` (53 MB).
  - Add `loading="lazy"` plus `width` and `height` to the two `<img>` tags in `sections/about.php`, then inject.
  - Production WP Admin → Elementor → Settings: disable Google Fonts. Elementor loads 36 unused Roboto and Roboto Slab styles.
- **Why:** the home page downloads about 108 MB. Most visitors arrive on phones from Instagram and Facebook.
- **Files:** `wp-content/themes/nonla-theme/assets/photos/`, `assets/videos/`, `sections/about.php`; FTP the new assets under the same filenames
- **Verify:** `ls -la` on the assets folders shows a total under 8 MB. Browser dev tools show the home page under 10 MB.

### 12. Code hygiene

- **What:**
  - Delete `wp-content/mu-plugins/nonla-setup.php`. It calls `switch_theme('nonla-child')`, a theme that does not exist, and would fire on any database without the `nonla_setup_done` option.
  - `inject-elementor.php`, `populate-content.php`: add `if (!defined('WP_CLI')) exit;` at the top. In `nl_inject()`, also set `_wp_page_template` to `elementor_header_footer` so new pages do not repeat the Gallery problem.
  - `populate-content.php`: do not run it on a populated site. The photo path is `C:/Users/ionut/...` (line 10), so every photo field would be overwritten with 0 and team bios reset to lorem ipsum. Add a warning comment or delete the script.
  - `functions.php`: version `nonla-style` with `filemtime()` instead of `'1.0'` so an FTP deploy of `style.css` is not served from cache.
  - `shortcodes/gallery.php`: the column logic only works for exactly 5 items. With the current 9 it emits an extra `</div>`. Simplify to a plain grid.
  - `header.php` or `functions.php`: add a meta description and Open Graph title, description and image. Shared links currently show no preview. Set a site icon in WP Admin (the stored one points to a deleted attachment).
  - Remove the stray empty file `posts}`. In WP Admin → ACF, deactivate the unused groups "Hero Section", "About Section" and "Places That Stay With You".
- **Why:** removes traps for the next developer and makes shared links look right.
- **Files:** as listed
- **Verify:** `/gallery/` HTML has balanced `<div>` tags. The page source shows `style.css?ver=` with a timestamp and `og:` tags. `git status` shows `posts}` deleted.

---

## Not in this plan

- Server-rendered Romanian pages. The translation is JavaScript-only, so search engines only index English. Fixing that is a rewrite.
- Replacing Elementor, jQuery or the Instagram plugin.
- Moving the DB-only ACF field groups into code. Worth doing later so the field definitions are in git.
