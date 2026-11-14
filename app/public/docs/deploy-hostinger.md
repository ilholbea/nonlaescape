# Deploying to production (Hostinger)

Production is **nonlaescape.com** on Hostinger (hPanel, FTP with FileZilla, SSH access available — confirmed by the owner 2026-11-14). Code goes from local to production. Content does not: it is created in production WP Admin and stays there.

Finish the [pre-deploy checklist](development.md#6-pre-deploy-test-checklist) before starting.

## 1. Pick the path

| This deploy changes | Path | Typical time |
|---|---|---|
| Only files inside `wp-content/themes/nonla-theme/` | [Theme-only](#3-theme-only-deploy-the-normal-path) | A few minutes |
| New or replaced theme photos or videos | [New media assets](#4-new-media-assets) | Depends on file size |
| Anything in `sections/`, `pages/` or `inject-elementor.php` | Theme-only first, then [layout injection](#5-layout-injection-over-ssh-rare) | 15 minutes, needs SSH |
| Projects, places, team, testimonials, settings | Not a deploy. See [content-editing.md](content-editing.md) | |

## 2. Back up production first

Do both before every deploy. The second one is the fast rollback.

1. **A restorable backup of the live site.** Either:
   - hPanel → Websites → Manage → Files → **Backups**: check that a recent backup exists or generate a new one, or
   - production WP Admin → All-in-One WP Migration → **Export** → File, and download the `.wpress` file.
2. **A copy of the live theme.** In FileZilla, download `public_html/wp-content/themes/nonla-theme/` to a dated folder on this PC, for example `Desktop\Nonla\Backup\theme-2026-11-14\`. Skip `assets/photos` and `assets/videos` unless this deploy replaces them.

All-in-One WP Migration is used on production for **Export only**. Never Import there.

## 3. Theme-only deploy (the normal path)

1. FileZilla → connect with the FTP account from hPanel → Files → **FTP Accounts**.
2. Upload the theme folder:
   - local: `C:\Users\micro-local\Local Sites\nonla-local-import\app\public\wp-content\themes\nonla-theme\`
   - remote: `public_html/wp-content/themes/nonla-theme/`
3. Upload the code, not the whole folder every time. `assets/photos` and `assets/videos` are about 160 MB of binaries that rarely change. Leave them out unless they changed, or set FileZilla to "Overwrite if source newer".
4. If the deploy adds a new PHP file that `functions.php` loads (a new file in `shortcodes/`), upload the new file first and `functions.php` last. `functions.php` uses `require_once`, so the site fails for as long as it points at a file that has not arrived yet.
5. If a theme file was deleted or renamed locally, delete the old one on the server by hand. FTP upload never removes files.
6. Production WP Admin → Elementor → Tools → **Clear Files & Data**. (Older Elementor versions label it "Regenerate CSS & Data".)
7. Purge the page cache if production has one. See [To confirm](#10-to-confirm).
8. Run the [post-deploy checklist](#8-post-deploy-checklist).

`style.css` and `ig-carousel.js` are loaded with a fixed version (`?ver=1.0`), so browsers and caches can keep serving the old file after a deploy. Check the result in a private window. Returning visitors may see the old CSS until their cache expires.

Upload only the theme folder. Never upload `wp-config.php`, `.htaccess`, `wp-content/plugins/`, `wp-content/uploads/`, `wp-content/mu-plugins/`, `local-phpinfo.php`, `local-xdebuginfo.php` or `populate-content.php` from the local copy.

## 4. New media assets

- **Theme assets** (files the code refers to by name, such as `assets/photos/hero.jpg` or `assets/videos/hero-video-1080p.mp4`): upload just those files by FTP to `public_html/wp-content/themes/nonla-theme/assets/photos/` or `.../assets/videos/`. Upload them before the code that uses them.
- **Content images** (project covers, team photos, gallery photos, hotel photos): staff upload these in production WP Admin through the Media Library. They are not deployed.
- Resize and compress before uploading. The current theme photos are camera originals between 2 MB and 32 MB each, and the hero video is 40 MB. A photo sized for the web should be well under 500 KB.

## 5. Layout injection over SSH (rare)

Only needed when `sections/`, `pages/` or `inject-elementor.php` changed. Page layout HTML is stored in the production database, so FTP alone does not change it.

**What the inject overwrites on production** (checked against `inject-elementor.php`):

- The complete Elementor content of the pages with slugs `home`, `gallery`, `projects`, `team` and `legal`. Any edit made to those five pages with the Elementor editor in production WP Admin is gone after the inject.
- The static front page setting, which it points back at Home.
- It creates any of the five pages that does not exist yet.

**What it does not touch:** projects, places, team members, testimonials, gallery items, itinerary days, accommodations, the media library, Nonla Settings, and every other page.

Before running it, ask whoever edits the site whether anyone changed those five pages in the Elementor editor. If so, carry that change into `sections/` or `pages/` locally first.

Steps:

1. Do the [theme-only deploy](#3-theme-only-deploy-the-normal-path) first. The sections use theme CSS, shortcodes and assets.
2. FTP to `public_html/` (the WordPress root, beside `wp-config.php`):
   - `sections/` (whole folder)
   - `pages/` (whole folder)
   - `inject-elementor.php`
3. Open SSH. hPanel → Websites → Manage → Advanced → **SSH Access** shows the host, port (usually 65002) and username:
   ```bash
   ssh -p 65002 u123456789@<server-ip>
   cd ~/domains/nonlaescape.com/public_html     # the folder that contains wp-config.php
   wp option get home                           # must print https://nonlaescape.com
   ```
4. Take a database snapshot, kept outside the web root:
   ```bash
   wp db export ~/nonla-before-inject-$(date +%F-%H%M).sql
   ```
5. Run the inject and flush Elementor's CSS:
   ```bash
   wp eval-file inject-elementor.php && wp elementor flush-css
   ```
   Expect one `Success: Injected → ...` line per page.
6. **Delete the script from production.** It must not stay on the web server:
   ```bash
   rm inject-elementor.php
   ```
   Then confirm from this PC: `curl -sI https://nonlaescape.com/inject-elementor.php | head -1` must show `404`. `sections/` and `pages/` only define variables and can stay for next time, or be removed as well.
7. If the inject created a page that did not exist before (Gallery, for example), open it in production WP Admin → Pages → Page Attributes and set the template to **Elementor Full Width**. Otherwise the theme prints its own page title in addition to the custom header.
8. Purge the page cache, then run the [post-deploy checklist](#8-post-deploy-checklist).

The Contact Email is copied into the Home CTA and the Legal page at inject time. If it was changed in Nonla Settings since the last inject, those two places update now.

## 6. If SSH or WP-CLI is not available

Stop at the theme-only deploy. It still ships every change that lives in the theme folder. Do not fall back to an All-in-One WP Migration import.

Options for the layout change, in order of preference:

1. **Get SSH.** Check hPanel → Advanced → SSH Access. If the plan does not include it, ask Hostinger support or upgrade the plan. Check hPanel for a browser terminal as well.
2. **Move the change into the theme.** Markup that lives in a shortcode (`shortcodes/*.php`) deploys by FTP with no injection. If a section keeps changing, turning it into a shortcode removes the need for SSH.
3. **A one-off admin-only runner.** `inject-elementor.php` needs WP-CLI only because it prints with `WP_CLI::success()`. A small script that runs the same code for a logged-in administrator would work without SSH. It does not exist yet. It has to be written, tested locally and removed from production after use, like the inject script.
4. **By hand in Elementor.** Rebuild the changed block in the Elementor editor on production by pasting the new HTML into the page's HTML widget. This is error-prone and the next inject overwrites it, so use it only for a small urgent fix.

## 7. Rollback

| What went wrong | Fix |
|---|---|
| Theme deploy broke the site or the layout | Upload the theme copy saved in step 2 over the live folder, or check out the previous commit locally (`git checkout <commit> -- app/public/wp-content/themes/nonla-theme`) and upload that. Then Elementor → Tools → Clear Files & Data |
| White screen right after upload | Usually a half-finished upload or a PHP error in `functions.php`. Re-upload the whole theme folder, or restore the saved copy |
| Inject produced the wrong layout | Check out the previous version of `sections/`, `pages/` and `inject-elementor.php`, upload, run the inject again, delete the script |
| Inject damaged something else | Restore the snapshot from step 4 of the injection: `wp db import ~/nonla-before-inject-<timestamp>.sql`. This also discards any content staff added after the snapshot, so do it straight away or not at all |
| Nothing else works | Restore from hPanel → Files → Backups (files and database). Content added after that backup is lost |

## 8. Post-deploy checklist

- [ ] `https://nonlaescape.com/`, `/projects/`, one project detail page, `/team/`, `/gallery/`, `/legal/` load without errors, in a private window.
- [ ] The change you deployed is visible.
- [ ] EN/RO toggle works. The mobile menu opens and closes on a phone.
- [ ] Hero video plays. The Instagram carousel shows photos. The countdown shows if an upcoming project has a future start date.
- [ ] "Register Now" opens the right form. The footer email is right.
- [ ] Browser console shows no errors.
- [ ] `curl -sI https://nonlaescape.com/inject-elementor.php | head -1` shows `404`. The same for `/populate-content.php`.
- [ ] Production WP Admin login still works, and the projects, team members and places are all still there.
- [ ] Add a line to the session log in `HANDOFF.md`: date, what was deployed, which commit.

## 9. Never do

- **Never import an All-in-One WP Migration file into production**, and never import a local database there by any other route (phpMyAdmin, `wp db import` of a local export). It replaces live content with stale local test data and local passwords.
- Never run `populate-content.php` on production. It finds existing team members, places and projects by title and overwrites their fields with seed and lorem-ipsum text.
- Never leave `inject-elementor.php` on the server after running it.
- Never run `wp search-replace` on production as part of a deploy. The database is not deployed, so there is nothing to rewrite.
- Never edit theme files on the server (Theme File Editor, hPanel File Manager). The next FTP upload overwrites them. Change them locally and deploy.
- Never edit the five injected pages with the Elementor editor in production. The next inject overwrites them.
- Never add content locally and expect it to appear on the live site.
- Never upload local `wp-config.php`, `.htaccess`, plugins, uploads or `mu-plugins`.
- Never put FTP, SSH or WP Admin passwords in the repo, in these docs or in a chat with an AI assistant.

## 10. To confirm

These are assumptions until someone checks them in hPanel or on production. Update this file when they are known.

- [x] Does the Hostinger plan include SSH access? Yes (confirmed 2026-11-14).
- [ ] Is WP-CLI (`wp`) available in that SSH shell? Run `wp --info` once to check.
- [ ] The exact SSH host, port and username, and the path to the WordPress root (`~/domains/nonlaescape.com/public_html` is the usual layout).
- [ ] How often Hostinger backs up this plan automatically, and how long backups are kept.
- [ ] Is a page cache active on production (LiteSpeed Cache plugin, hPanel Cache Manager)? If yes, add "Purge All" after every deploy.
- [ ] Are `sections/`, `pages/` or an old `inject-elementor.php` on the production server right now? Delete the script if it is there.
- [ ] Has anyone edited Home, Gallery, Projects, Team or Legal with the Elementor editor in production?
- [ ] Do the five pages exist in production with the Elementor Full Width template?
- [ ] Do the field groups stored in the database (Project Details, Team Member Details and the others listed in [development.md](development.md#field-definitions-acf)) exist in production with the same field names as local?
- [ ] Which Elementor version runs in production? Local has 4.0.9.
