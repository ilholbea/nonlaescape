# Local development

How to change the site on the local copy. For getting changes onto the live site see [deploy-hostinger.md](deploy-hostinger.md).

## 1. Start the site

1. Open **Local** (Local by Flywheel) and start the site `nonla-local-import`.
2. Site: http://nonla-local-import.local. Admin: http://nonla-local-import.local/wp-admin (the login is kept in the Local app).
3. Quick check from any terminal: `curl -sI http://nonla-local-import.local/ | head -1` should show `200`.

Paths (the folder name contains a space, so quote it):

| | Windows | Git Bash |
|---|---|---|
| Project root (`CLAUDE.md`, `.claude/`) | `C:\Users\micro-local\Local Sites\nonla-local-import\` | `/c/Users/micro-local/Local Sites/nonla-local-import/` |
| WordPress root | `...\app\public\` | `.../app/public/` |

## 2. Site shell (WP-CLI)

In Local, right-click the site and choose **Open site shell**. It opens in `app/public` with `wp`, `php` and `mysql` on the PATH.

```bash
wp eval-file inject-elementor.php   # rebuild the injected pages from sections/ and pages/
wp elementor flush-css              # clear Elementor's generated CSS (run after every inject)
php -l sections/hero.php            # syntax-check a PHP file
```

From a plain Git Bash (no site shell), the same commands work through the wrapper at the project root, which reads the paths Local writes to `app/.envrc`:

```bash
bash .claude/skills/nonla-dev-loop/scripts/wp-local.sh eval-file inject-elementor.php
bash .claude/skills/nonla-dev-loop/scripts/wp-local.sh --php -l app/public/sections/hero.php
```

Do not run `wp eval-file populate-content.php`. It overwrites projects, team, places and testimonials with seed and lorem-ipsum data, and it expects photos at `C:/Users/ionut/Desktop/Photos/modified/`, a folder from a previous PC that does not exist here.

## 3. Where to edit what

All paths are relative to `app/public/`.

| You want to change | Edit | Then |
|---|---|---|
| Colours, spacing, typography, responsive rules | `wp-content/themes/nonla-theme/style.css` (design tokens at the top) | Reload (Ctrl+F5) |
| Nav, logo, language toggle, mobile menu, hero video fallback | `wp-content/themes/nonla-theme/header.php` | Reload |
| Footer columns and tagline | `wp-content/themes/nonla-theme/footer.php` | Reload |
| Enqueued fonts and scripts, reveal/filter JS, Nonla Settings page | `wp-content/themes/nonla-theme/functions.php` | Reload |
| How CPT data renders (cards, grids, countdown) | `wp-content/themes/nonla-theme/shortcodes/*.php` | Reload |
| Project detail page | `wp-content/themes/nonla-theme/single-nl_project.php` | Reload |
| Instagram carousel behaviour | `wp-content/themes/nonla-theme/assets/js/ig-carousel.js` | Reload |
| Home page blocks (hero, ticker, about, section headings, CTA) | `sections/*.php` | **Inject + flush** |
| Inner page headers, Team CTA, Legal text | `pages/*.php` | **Inject + flush** |
| Which blocks a page has, their order, a new injected page | `inject-elementor.php` | **Inject + flush** |
| Test content (projects, places, team) | Local WP Admin | Nothing. Local content is throwaway and is never deployed |

## 4. Inject + flush, or just reload?

**Reload is enough** for anything inside `wp-content/themes/nonla-theme/`. PHP runs on every request. `style.css` and `ig-carousel.js` are enqueued with a fixed version (`1.0`), so use a hard refresh (Ctrl+F5) to get past the browser cache.

**Inject + flush is needed** after editing `sections/`, `pages/` or `inject-elementor.php`. That HTML is not read at request time. `inject-elementor.php` builds it once and stores it in the database as each page's `_elementor_data`.

```bash
wp eval-file inject-elementor.php && wp elementor flush-css
```

What the inject does (checked against the script):

- Replaces the whole Elementor content of five pages, found by slug: `home`, `gallery`, `projects`, `team`, `legal`. Anything changed on those pages with the Elementor editor is lost. This is why the Elementor visual editor is not used on them.
- Creates any of those pages that is missing, and sets Home as the static front page.
- Bakes absolute URLs (theme assets, home, projects) and the contact email into the stored HTML at the moment it runs. Two consequences: the inject must be run in the environment it serves (never copy `_elementor_data` between local and production), and changing the Contact Email in Nonla Settings does not update the Home CTA or the Legal page until the next inject.
- Leaves everything else alone: projects, places, team, testimonials, gallery, media, Nonla Settings, and any page that is not in the list above (the local `faq` page is one).
- Needs WP-CLI. It calls `WP_CLI::success()`, so it cannot be run from a browser.

Pages the inject creates get the default page template, which outputs the theme's own page title (`<h1 class="entry-title">`) in addition to the custom header. `gallery` is in that state locally. The other four use the Elementor Full Width template (`elementor_header_footer`), which was set separately. The inject does not set it.

### Field definitions (ACF)

Field groups come from two places, and only one of them deploys:

- **In theme code, deployed by FTP:** Itinerary Day and Accommodation (`shortcodes/project-acf-fields.php`), Place hover details (`shortcodes/place-acf-fields.php`).
- **In the database, not deployed:** Project Details, Team Member Details, Place Details, Series Details, Why Item Details, Testimonial Details, Gallery Item Details. They were created in the ACF admin screen.

A field added through the local ACF admin screen will not exist in production. Add new fields in PHP with `acf_add_local_field_group()`, following the two files above, so they travel with the theme.

### EN / RO text

Every visitor-facing string carries both languages. The toggle in `header.php` swaps them in the browser:

```html
<span class="t" data-en="See Our Projects" data-ro="Vezi Proiectele">See Our Projects</span>
```

The element's inner text is the English default. A string without `class="t"` and both attributes stays English when the visitor picks RO.

## 5. Git

One repo, rooted at the project folder, remote `origin` = https://github.com/ilholbea/nonlaescape.git. It holds `CLAUDE.md`, `.claude/`, `conf/` (Local config templates) and `app/public/**` (theme, `sections/`, `pages/`, `inject-elementor.php`, `docs/`, `HANDOFF.md`). WordPress core, plugins, uploads and big media are git-ignored.

Branches: work on `development`, merge to `main`. `v1-demo` is an old reference.

From the project root (paths include `app/public/`):

```bash
git status
git add app/public/wp-content/themes/nonla-theme/style.css app/public/sections/hero.php   # name the files; never `git add .`
git commit -m "fix: hero stats wrap on small screens"
git checkout main && git merge development --no-edit && git checkout development
git push origin development main
```

Commit prefixes in use: `feat:`, `fix:`, `chore:`, `docs:`. Stage files by name because plugin and runtime files show up in `git status`. After a session, add an entry to the session log in `HANDOFF.md`.

## 6. Pre-deploy test checklist

Run through this locally before following [deploy-hostinger.md](deploy-hostinger.md).

- [ ] Every changed PHP file passes `php -l`.
- [ ] If `sections/`, `pages/` or `inject-elementor.php` changed: inject + flush ran without errors. Write down that this deploy needs the injection path.
- [ ] These load and look right at desktop width and at about 390px wide: `/`, `/projects/`, one `/project/<slug>/`, `/team/`, `/gallery/`, `/legal/`.
- [ ] EN/RO toggle: every new or changed string switches, with correct Romanian diacritics.
- [ ] Mobile menu opens and closes, project filter tabs work, the itinerary accordion opens, the Instagram carousel scrolls.
- [ ] Browser console shows no errors.
- [ ] No local address in code: `grep -rn "nonla-local" wp-content/themes/nonla-theme sections pages inject-elementor.php` prints nothing.
- [ ] No leftovers: `grep -rnE "var_dump|print_r\(|error_log\(|console\.log" wp-content/themes/nonla-theme sections pages` prints nothing.
- [ ] New theme photos or videos are resized and compressed, and listed for upload.
- [ ] New fields are registered in PHP, not only in the local ACF admin.
- [ ] Changes are committed on `development` and merged to `main`. `git status` is clean for the theme, `sections/` and `pages/`.
