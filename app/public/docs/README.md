# Nonla Escape: project docs

Working docs for developing and deploying **nonlaescape.com** (Asociația Nonla Cultural Escape).

| Doc | Read it when |
|-----|--------------|
| [development.md](development.md) | You are changing the site on the local copy |
| [deploy-hostinger.md](deploy-hostinger.md) | You are putting changes on production (Hostinger) |
| [content-editing.md](content-editing.md) | NGO staff are adding projects, places, team members or testimonials in production WP Admin |
| [content-enrichment.md](content-enrichment.md) | You want the accurate places copy (EN/RO) and photo credits added to the local mock DB, to paste into production |
| [IMPROVEMENT-PLAN.md](IMPROVEMENT-PLAN.md) | You are deciding what to improve next |
| [../HANDOFF.md](../HANDOFF.md) | You need the architecture overview and the session log |

Where these docs disagree with older notes (`wp-content/README.md`, old HANDOFF entries, anything that describes a full All-in-One WP Migration deploy), these docs are the current ones.

## The rule: code flows up, content stays in prod

Code is written and tested on the local copy, then copied up to production: the `wp-content/themes/nonla-theme/` folder by FTP, and occasionally `sections/`, `pages/` and `inject-elementor.php` for a layout change. Content (projects, places, team members, testimonials, gallery items, media uploads, Nonla Settings) is created only in production WP Admin and never travels in either direction as part of a deploy. The local database is a disposable test copy, so nothing typed into local WP Admin reaches the live site. A full All-in-One WP Migration import into production is never part of a deploy: it replaces the production database with the local one and wipes the content staff have added.

| What changed | How it reaches production |
|--------------|---------------------------|
| Theme files: `style.css`, `header.php`, `footer.php`, `functions.php`, `shortcodes/`, `single-nl_project.php`, `assets/js/` | FTP the theme folder, then clear Elementor's files and data |
| Layout HTML in `sections/` or `pages/` | FTP those folders plus `inject-elementor.php`, run the inject over SSH, delete the script |
| Theme photos or videos in `assets/` | FTP just those files |
| Projects, places, team, testimonials, gallery, settings | Typed into production WP Admin by staff. Not deployed |
