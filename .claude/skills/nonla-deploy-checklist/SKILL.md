---
name: nonla-deploy-checklist
description: Prepare a production deploy of the Nonla Escape site to Hostinger. Use when the user says deploy, go live, push to production, upload to Hostinger, FTP, FileZilla, release, or asks what needs uploading or whether a change needs the Elementor inject on prod. Produces the file list and a step-by-step checklist for the user to carry out. It never uploads, connects to the server or deploys anything itself.
---

# Nonla deploy checklist

Source of truth: `app/public/docs/deploy-hostinger.md`. Read it first. If it and this skill disagree, the doc wins.

## Hard limits

- You do not deploy. No `ftp`, `sftp`, `scp`, `rsync`, `ssh`, `lftp`, or upload requests to nonlaescape.com or Hostinger. No WP-CLI against production.
- Do not ask for, read, store or type FTP, SSH, hPanel or WP Admin credentials.
- Never propose an All-in-One WP Migration import, a database import or `wp search-replace` on production. If the user asks for one, explain that it replaces live content and point to the doc.
- Never include `populate-content.php`, `wp-config.php`, `.htaccess`, plugins, uploads or `mu-plugins` in an upload list.
- Read-only GET requests to the public site (`curl -sI https://nonlaescape.com/...`) are fine for verification after the user says the deploy is done.

## Steps

1. **Find what changed.** In `app/public`: `git status --short`, then `git diff --stat <last deployed ref>..HEAD`. Look for a `deployed-*` tag. If there is none, ask the user what was last deployed, or treat every code file in the theme as changed. Uncommitted changes: stop and ask whether they are meant to ship.
2. **Classify each changed path.**
   - `wp-content/themes/nonla-theme/**` except `assets/photos`, `assets/videos` → theme-only upload
   - `wp-content/themes/nonla-theme/assets/photos|videos/**` → media upload (report file sizes; flag anything over 500 KB)
   - `sections/**`, `pages/**`, `inject-elementor.php` → layout injection over SSH
   - anything else (docs, `HANDOFF.md`, `.claude/`) → not deployed
   - Also flag: a new file required by `functions.php` (upload it before `functions.php`), deleted or renamed theme files (must be removed on the server by hand), and fields or content that exist only in the local database (cannot be deployed).
3. **Run the local pre-deploy checks** from `app/public/docs/development.md` section 6 that you can run yourself (PHP lint, page status codes, greps for local URLs and debug leftovers). List the visual checks the user must do.
4. **Write the deploy sheet** and show it to the user:
   - path chosen (theme-only, media, injection) and why
   - backup steps
   - upload table: local path → remote path, in upload order
   - the exact steps from the doc for that path, including Elementor → Tools → Clear Files & Data, and for injection the database snapshot, the inject command and deleting `inject-elementor.php`
   - if injecting: a warning that it replaces the Elementor content of home, gallery, projects, team and legal, and the question "has anyone edited these in the Elementor editor on production?"
   - open items from the doc's "To confirm" list that affect this deploy (SSH availability first)
   - rollback for this path
5. **Wait.** The user performs each step. Do not continue as if a step happened until the user says so.
6. **After the user confirms the deploy,** run the read-only checks: status of `/`, `/projects/`, `/team/`, `/gallery/`, `/legal/`, and that `/inject-elementor.php` and `/populate-content.php` return 404. Report results and list the checks only a person can do (EN/RO toggle, mobile menu, Register button).
7. **Offer** to add a session-log entry to `app/public/HANDOFF.md`, and suggest the user tag the commit (`git tag deployed-YYYY-MM-DD` from the project root). Create the tag only if asked.

If SSH is not available and the deploy needs injection, ship the theme-only part and present the options in section 6 of the doc. Do not improvise another route.
