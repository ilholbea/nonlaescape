# Nonla Escape — WordPress Local Dev

Local development copy of **nonlaescape.com** running via Local by Flywheel.

---

## Local Environment

| | |
|---|---|
| Local URL | http://nonla-local.local |
| WP Admin | http://nonla-local.local/wp-admin |
| Login | `admin` / `admin123` |
| PHP | 8.2.29 |
| MySQL | 8.4.0 (port 10005) |
| WP root | `app/public/` |

> ⚠️ Reset the admin password on production after any DB deploy — local uses `admin123`.

---

## Git Branches

| Branch | Purpose |
|--------|---------|
| `main` | Live site baseline |
| `manual-build` | Current active build — Elementor-based, this is what you work on |
| `v1-demo` | Old PHP template version (reference only, do not delete) |

## Tags

| Tag | Description |
|-----|-------------|
| `v0.1-alpha` | First complete Elementor build — all 4 pages, CPTs, ACF fields, custom nav |

---

## Project Structure

```
app/public/
├── wp-content/
│   └── themes/
│       └── nonla-theme/          ← our custom theme (child of hello-elementor)
│           ├── style.css         ← theme registration
│           ├── functions.php     ← fonts, global CSS, shortcodes
│           ├── header.php        ← custom nav + language toggle
│           ├── inc/
│           │   ├── post-types.php   ← registers Team + Projects CPTs
│           │   └── acf-fields.php   ← defines all custom fields
│           └── assets/photos/    ← Vietnam photos used on the site
└── inject-elementor.php          ← script to rebuild all pages from scratch
```

---

## Pages

| Page | URL | Template |
|------|-----|---------|
| Home | `/` | Full Elementor build — hero, about, series, destinations, why, gallery, CTA, footer |
| Projects | `/projects/` | Header + series cards + auto-list from WP Admin |
| Team | `/team/` | Header + auto-list from WP Admin |
| Legal | `/legal/` | Legal text |

---

## How to Add Content (No Code Needed)

### Add a Team Member

1. Go to **WP Admin → Team → Add New**
2. Fill in the title (person's name)
3. Fill in the fields on the right: Role, City, Bio, Photo, Display Order
4. Click **Publish**
5. Visit `/team/` — they appear automatically

### Add a Project / Edition

1. Go to **WP Admin → Projects → Add New**
2. Fill in the title (e.g. `Vietnam Escape — Vol. 6`)
3. Fill in the fields: Series, Edition, Status (Upcoming/Completed), Dates, Duration, Price, Destinations, Cover Image, Description
4. Click **Publish**
5. Visit `/projects/` — it appears automatically

> **Status matters:** `Upcoming` shows a **Register Interest** button. `Completed` shows a greyed-out **Completed** badge.

---

## How to Edit Page Content (No Code Needed)

1. Go to **WP Admin → Pages**
2. Click the page you want → **Edit with Elementor**
3. Click any section or widget to edit it
4. Click the **green arrow** (bottom left) to save and publish

---

## How to Translate Content (EN / RO)

The nav has EN / RO toggle buttons. To make any piece of text translatable, wrap it like this inside an HTML widget in Elementor:

```html
<span class="lang-en">This is English</span>
<span class="lang-ro">Aceasta este română</span>
```

When the user clicks **EN**, the `.lang-ro` span hides. When they click **RO**, the `.lang-en` span hides. Both versions sit in the same place in the HTML.

---

## Design Tokens (Colors & Fonts)

All colors and fonts are defined as CSS variables in `functions.php` → `nonla_global_css()`.

| Variable | Value | Used for |
|----------|-------|---------|
| `--bg` | `#FAF5EC` | Page background (warm cream) |
| `--bg-alt` | `#F2E8D4` | Alternate section background (warmer parchment) |
| `--card` | `#FFF8EE` | Card surfaces |
| `--accent` | `#B85520` | Primary brand — buttons, labels, links |
| `--gold` | `#D4A030` | Display accent — hero numbers, destination tags |
| `--text` | `#1A0E06` | Primary body text |
| `--text-2` | `#4A3318` | Secondary / descriptive text |
| `--subtle` | `#7A6040` | Muted metadata, labels |
| `--border` | `rgba(221,208,176,.6)` | Card borders, dividers |

**Fonts** (loaded from Google Fonts):
- **Cormorant Garamond** — headings, display text (400 + 600, with italic)
- **Inter** — body text (300 + 400 + 600)
- **Bebas Neue** — logo and hero stats only

To change a color site-wide, edit the variable value in `functions.php`.

> **Note on full-bleed sections:** The gallery and "Where We Go" photo grids use `width:100vw; margin-left:calc(50% - 50vw)` to break out of Elementor's content column. They're wrapped in `.nl-bleed-outer { overflow-x: hidden }` to contain the overflow at the section level — no `overflow-x: hidden` needed on `body`.

---

## Rebuilding Pages from Scratch

If you ever need to reset a page back to the original built layout:

```bash
# In Local by Flywheel → Site Shell
wp eval-file inject-elementor.php
```

This rewrites the Elementor data for all 4 pages (Home, Projects, Team, Legal). Your team members and projects data in WP Admin are **not affected**.

---

## Development Commands (Site Shell)

```bash
# Activate the theme
wp theme activate nonla-theme

# Install / activate a plugin
wp plugin install <plugin-slug> --activate

# Reset the database (WARNING: deletes all content)
wp db reset --yes

# Reinstall WordPress after a DB reset
wp core install --url=http://nonla-local.local --title="Nonla Escape" --admin_user=admin --admin_password=admin123 --admin_email=nonlaescape@gmail.com --skip-email

# Set all pages to use Elementor template
wp post meta update <page-id> _wp_page_template elementor_header_footer
```

---

## Disabled Plugins

These are Hostinger-specific and crash outside Hostinger servers. Do **not** re-enable locally:

- `hostinger.disabled`
- `hostinger-ai-assistant.disabled`
- `hostinger-easy-onboarding.disabled`

---

## Deploying to Production

1. Export the local DB via **All-in-One WP Migration** (or Adminer → Export)
2. Upload files via FTP or Git
3. Import DB on Hostinger
4. ⚠️ **Reset the admin password immediately** — local uses `admin123`
5. Re-activate Hostinger plugins after deploy
