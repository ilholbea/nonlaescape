---
name: nonla-dev-loop
description: Local development loop for the Nonla Escape WordPress site (nonlaescape.com, Local by Flywheel). Use whenever changing or debugging the site locally - theme CSS or PHP, shortcodes, header, footer, nav, home sections, inner pages, EN/RO strings, ACF fields - or when asked to run WP-CLI, inject-elementor, flush Elementor CSS, or check a page with curl. Tells you which file to edit, whether inject + flush is needed, and how to verify.
allowed-tools: Bash(bash .claude/skills/nonla-dev-loop/scripts/wp-local.sh *)
---

# Nonla dev loop

Full reference: `app/public/docs/development.md`. Architecture and history: `app/public/HANDOFF.md`.

Local site: http://nonla-local-import.local. WordPress root: `app/public/` (one git repo at the project root, branch `development`, remote GitHub). The project path contains a space, so quote it.

## 1. Find the file

Paths relative to `app/public/`.

| Change | File | After saving |
|---|---|---|
| Styles, tokens, responsive | `wp-content/themes/nonla-theme/style.css` | reload |
| Nav, language toggle, mobile menu | `wp-content/themes/nonla-theme/header.php` | reload |
| Footer | `wp-content/themes/nonla-theme/footer.php` | reload |
| Enqueues, footer JS, Nonla Settings | `wp-content/themes/nonla-theme/functions.php` | reload |
| CPT rendering (`[nonla_projects]`, `[nonla_team]`, `[nl_places]`, `[nl_series]`, `[nl_why_items]`, `[nl_testimonials]`, `[nl_gallery_items]`, `[nl_countdown]`) | `wp-content/themes/nonla-theme/shortcodes/*.php` | reload |
| Project detail page | `wp-content/themes/nonla-theme/single-nl_project.php` | reload |
| Home blocks | `sections/*.php` | inject + flush |
| Inner page headers, Team CTA, Legal text | `pages/*.php` | inject + flush |
| Page composition, new injected page | `inject-elementor.php` | inject + flush |

Prefer putting markup that will change often in a shortcode: theme files deploy by FTP alone, while `sections/` and `pages/` need SSH on production.

## 2. Rules for the code

- Every visitor-facing string needs both languages: `<span class="t" data-en="..." data-ro="...">English</span>`. Romanian uses ă â î ș ț.
- Use the design tokens at the top of `style.css` (`--terra`, `--gold`, `--cream`, ...) and the `nl-` class prefix. Add `nl-reveal` for the scroll-in animation.
- Escape output in PHP: `esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`.
- Never hardcode `nonla-local-import.local` or `nonlaescape.com`. Use `home_url()` and `get_stylesheet_directory_uri()`.
- New ACF fields go in PHP (`acf_add_local_field_group`, see `shortcodes/project-acf-fields.php`). Fields made in the ACF admin screen live in the local database and never reach production.
- Do not use the Elementor visual editor on home, gallery, projects, team or legal. The inject replaces their content.

## 3. WP-CLI

From the project root, in Git Bash:

```bash
bash .claude/skills/nonla-dev-loop/scripts/wp-local.sh --php -l app/public/sections/hero.php   # syntax check
bash .claude/skills/nonla-dev-loop/scripts/wp-local.sh eval-file inject-elementor.php
bash .claude/skills/nonla-dev-loop/scripts/wp-local.sh elementor flush-css
```

The wrapper needs the site running in Local (it reads `app/.envrc`). If it fails, ask the user to start the site, or to run the commands in Local → right-click the site → Open site shell.

Inject + flush on the local site is the normal loop after editing `sections/`, `pages/` or `inject-elementor.php`. It rewrites the Elementor data of five pages from those files and nothing else.

Do not run, unless the user explicitly asks for that exact thing:

- `populate-content.php` (overwrites CPT content with seed and lorem data; its photo path does not exist on this PC)
- `wp db import`, `wp db reset`, `wp search-replace`, `wp post delete`, plugin or core updates
- anything against production. This skill is local only. Deploying is `nonla-deploy-checklist`.

## 4. Verify

```bash
for u in / /projects/ /team/ /gallery/ /legal/; do
  curl -s -o /dev/null -w "%{http_code} $u\n" "http://nonla-local-import.local$u"
done
curl -s http://nonla-local-import.local/ | grep -c 'nl-hero'            # the block you changed is in the HTML
curl -s http://nonla-local-import.local/ | grep -ciE 'fatal error|warning:|parse error'   # expect 0
```

For layout, hover and mobile checks use the Playwright plugin if its tools are loaded (screenshots at 1440 and 390 px wide), otherwise ask the user to look. Check that new strings switch in RO.

## 5. Finish

- Say plainly whether the change is theme-only or needs the injection path on production.
- Commit only when the user asks. Stage files by name from the project root: `git add app/public/<files>`, branch `development`. Push only when the user asks.
- Offer to add a session-log entry to `app/public/HANDOFF.md`.
