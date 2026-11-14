# nonla-local — WordPress Development Environment

## Overview
This is a local copy of the Hostinger WordPress site for **nonlaescape.com**, running via **Local by Flywheel**.

## Local Environment
- **Tool:** Local by Flywheel
- **Local URL:** http://nonla-local-import.local
- **WP Admin:** http://nonla-local-import.local/wp-admin
- **Login:** `nonlaescape@gmail.com` / *(see local .env or Local by Flywheel app)*
- **MySQL:** 127.0.0.1:10005 (user: root, database: local) — *(password in Local by Flywheel app)*
- **PHP:** 8.2.29
- **MySQL:** 8.4.0

## Site Files
- WordPress root: `C:\Users\micro-local\Local Sites\nonla-local-import\app\public\`
- Theme/plugins: `wp-content/themes/` and `wp-content/plugins/`

## Disabled Plugins (Hostinger-specific, crash locally)
These were disabled by renaming to `.disabled` — do NOT re-enable locally:
- `wp-content/plugins/hostinger.disabled`
- `wp-content/plugins/hostinger-ai-assistant.disabled`
- `wp-content/plugins/hostinger-easy-onboarding.disabled`

They require Hostinger's server infrastructure and will fatal error locally.

## Workflow
- All changes here are **local only** — the live Hostinger site is untouched
- To deploy code to production, follow `app/public/docs/deploy-hostinger.md`: FTP the theme folder, plus an SSH inject for layout changes (Hostinger SSH is available). Never import a migration file or the local DB into production.
- The local database is a mock copy. Improving it locally is fine; it is never deployed.

## Git
- **One repo**, rooted at `C:\Users\micro-local\Local Sites\nonla-local-import` → `https://github.com/ilholbea/nonlaescape.git`
- Branches: **`development`** (work here), `main`, and the stale `v1-demo`
- Every branch holds the whole site: `CLAUDE.md`, `conf/`, and `app/public/**` — so a fresh clone rebuilds everything
- Run git from the site root; paths include the `app/public/` prefix (no more `git -C app/public …`)
- Until 2026-07-22 this was two nested repos linked by a broken gitlink; unified via subtree merge (`ae0cf3a`), full history preserved, `master` retired
- `.claude/settings.local.json` is personal (per machine) and git-ignored
- Full workflow: see `app/public/HANDOFF.md`

## How the Local Site Was Set Up
1. Exported live site from Hostinger WP Admin using All-in-One WP Migration plugin (.wpress file)
2. Created blank site in Local by Flywheel
3. Increased PHP upload limit via `php.ini` to allow 330MB import
4. Imported .wpress file (files restored, DB partially failed at 93%)
5. Fixed MySQL startup issues (stale PID file, file lock from crashed import)
6. Disabled Hostinger-specific plugins that crash outside Hostinger servers
7. Ran WordPress DB update after environment change
8. Reset admin password via MySQL CLI

## Docs
Current docs live in `app/public/docs/`: `README.md` (index and the "code flows up, content stays in prod" rule), `development.md` (local dev loop), `deploy-hostinger.md` (production deploy), `content-editing.md` (staff guide), `content-enrichment.md` (places copy EN/RO and photo credits, ready to paste into production), `IMPROVEMENT-PLAN.md` (what to improve next). Where they disagree with the Workflow section above or with older READMEs, the docs win.

**Never import an All-in-One WP Migration file or a local database into production.** Deploys are theme-folder FTP, plus an SSH inject for `sections/` and `pages/` changes. Content is created only in production WP Admin.

## Claude tooling
Project-scoped, in `.claude/`. Plugins are enabled in `.claude/settings.json`.

**Skills** (`.claude/skills/`)
- `nonla-dev-loop`: any local code change. Which file to edit, when inject + flush is needed, WP-CLI through `scripts/wp-local.sh`, curl checks.
- `nonla-deploy-checklist`: when the user wants to deploy. Builds the upload list and step-by-step sheet. Claude never uploads or connects to production; the user does each step.
- `nonla-content-copy`: any copy in EN/RO. Brand voice, verified facts, calls to action for joining, volunteering, partnering, donating.
- `nonla-site-audit`: accessibility, SEO, schema.org NGO markup and performance checks. Read-only; includes `scripts/contrast.sh`.

**Subagents** (`.claude/agents/`)
- `nonla-theme-developer`: builds or fixes theme code and layout files, verifies with curl. No database writes (reports when inject is needed), no deploys.
- `nonla-content-writer`: drafts EN/RO copy and returns it as text. Read-only.
- `nonla-site-auditor`: runs the audit and returns findings with evidence. Read-only.

**Plugins** (`claude-plugins-official`)
- `frontend-design`: new sections or visual redesigns. Stay within the tokens and fonts in `style.css` unless a redesign is asked for.
- `playwright`: browser checks and screenshots of the local site (desktop and 390 px mobile). Needs Node.js (installed); drives Brave, configured via `env` in `.claude/settings.json`.
- `security-guidance`: pattern warnings on edits plus an automatic security review of diffs and commits. Needs Python (3.13 installed).
- `php-lsp`: PHP diagnostics and go-to-definition through Intelephense (installed globally with npm).
- `context7`: current WordPress, Elementor and ACF documentation. Send only generic technical questions to it.
- `claude-md-management`: `/revise-claude-md` at the end of a session to keep this file current.

Rules for every session: no writes to production, no deploy without the user doing the steps, never run `populate-content.php`, and local inject + flush only after `sections/`, `pages/` or `inject-elementor.php` changed.
