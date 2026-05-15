# nonla-local — WordPress Development Environment

## Overview
This is a local copy of the Hostinger WordPress site for **nonlaescape.com**, running via **Local by Flywheel**.

## Local Environment
- **Tool:** Local by Flywheel
- **Local URL:** http://nonla-local.local
- **WP Admin:** http://nonla-local.local/wp-admin
- **Login:** `nonlaescape@gmail.com` / *(see local .env or Local by Flywheel app)*
- **MySQL:** 127.0.0.1:10005 (user: root, database: local) — *(password in Local by Flywheel app)*
- **PHP:** 8.2.29
- **MySQL:** 8.4.0

## Site Files
- WordPress root: `C:\Users\ionut\Local Sites\nonla-local\app\public\`
- Theme/plugins: `wp-content/themes/` and `wp-content/plugins/`

## Disabled Plugins (Hostinger-specific, crash locally)
These were disabled by renaming to `.disabled` — do NOT re-enable locally:
- `wp-content/plugins/hostinger.disabled`
- `wp-content/plugins/hostinger-ai-assistant.disabled`
- `wp-content/plugins/hostinger-easy-onboarding.disabled`

They require Hostinger's server infrastructure and will fatal error locally.

## Workflow
- All changes here are **local only** — the live Hostinger site is untouched
- To deploy changes to production, use a migration plugin (e.g. All-in-One WP Migration) or manual FTP + DB export

## How the Local Site Was Set Up
1. Exported live site from Hostinger WP Admin using All-in-One WP Migration plugin (.wpress file)
2. Created blank site in Local by Flywheel
3. Increased PHP upload limit via `php.ini` to allow 330MB import
4. Imported .wpress file (files restored, DB partially failed at 93%)
5. Fixed MySQL startup issues (stale PID file, file lock from crashed import)
6. Disabled Hostinger-specific plugins that crash outside Hostinger servers
7. Ran WordPress DB update after environment change
8. Reset admin password via MySQL CLI
