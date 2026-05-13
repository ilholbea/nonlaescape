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
| `main` | Stable baseline |
| `development` | Active development branch |

---

## Project Structure

```
app/public/
├── inject-elementor.php              ← rebuild all Elementor pages from scratch
└── wp-content/themes/
    ├── nonla-theme/                  ← active theme (child of hello-elementor)
    │   ├── style.css                 ← all CSS with nl- prefix design system
    │   ├── functions.php             ← enqueue, shortcodes, Nonla Settings
    │   ├── header.php                ← nav + applyLang() JS
    │   ├── footer.php                ← site footer HTML
    │   └── inc/
    │       ├── post-types.php        ← Team + Projects CPTs
    │       └── acf-fields.php        ← custom fields for Team + Projects
    └── nonla-child/                  ← v1 PHP template reference (do not activate)
```

---

## Pages

| Page | URL | Sections (draggable in Elementor) |
|------|-----|----------------------------------|
| Home | `/` | Hero · Ticker · About · Series · Destinations · Why · Gallery · CTA |
| Projects | `/projects/` | Page header · `[nonla_projects]` shortcode |
| Team | `/team/` | Page header · `[nonla_team]` shortcode · Values CTA |
| Legal | `/legal/` | Page header · Legal content |

Each section is a separate Elementor HTML or shortcode widget — drag to reorder in the Elementor editor.

---

## How to Add Content (No Code Needed)

### Add a Team Member

1. Go to **WP Admin → Team → Add New**
2. Fill in: title (person's name), Role EN/RO, City, Bio EN/RO, Photo, Display Order
3. Click **Publish** → appears automatically on `/team/`

### Add a Project / Edition

1. Go to **WP Admin → Projects → Add New**
2. Fill in: Series, Edition, Status (Upcoming/Completed), Dates, Duration, Price, Destinations, Cover Image, Description EN/RO
3. Click **Publish** → appears automatically on `/projects/`

> `Upcoming` shows a **Register Now** button. `Completed` shows a **Completed** badge.

### Configure Registration URL & Contact Email

Go to **WP Admin → Settings → Nonla Settings** to update:
- **Registration Form URL** — the link behind "Register Now" on project cards
- **Contact Email** — used in the footer and project email links

---

## How to Edit Page Content (No Code Needed)

1. Go to **WP Admin → Pages** → click the page → **Edit with Elementor**
2. Click any section or widget to edit it, drag sections to reorder
3. Click the **green arrow** (bottom left) to save and publish

---

## EN / RO Language System

The nav has **EN / RO** toggle buttons. All bilingual text uses `data-en` / `data-ro` attributes:

```html
<span class="t" data-en="About" data-ro="Despre">About</span>
```

The `applyLang(l)` function (defined in `header.php`) switches all `.t[data-en]` elements on click. Language preference is saved to `localStorage`.

---

## Design Tokens

All colors are CSS variables in `style.css`:

| Variable | Value | Used for |
|----------|-------|---------|
| `--cream` | `#FAF5EC` | Page background |
| `--parch` | `#F2E8D4` | Alternate section background |
| `--card` | `#FFF8EE` | Card surfaces |
| `--terra` | `#B85520` | Primary brand — buttons, links |
| `--gold-l` | `#D4A030` | Display accent — hero numbers, dest tags |
| `--text` | `#1A0E06` | Primary body text |
| `--text2` | `#4A3318` | Secondary text |
| `--muted` | `#7A6040` | Labels, metadata |
| `--border` | `#DDD0B0` | Card borders, dividers |

**Fonts** (Google Fonts):
- **Cormorant Garamond** — headings (400 + 600, with italic)
- **Inter** — body text (300 + 400 + 600)
- **Bebas Neue** — logo and hero stats only

---

## Rebuilding Pages from Scratch

```bash
# In Local by Flywheel → Site Shell
wp eval-file inject-elementor.php
wp elementor flush-css
```

This rewrites Elementor data for all 4 pages. WP Admin content (team members, projects) is **not affected**.

---

## Development Commands (Site Shell)

```bash
# Activate the theme
wp theme activate nonla-theme

# Rebuild pages + flush CSS
wp eval-file inject-elementor.php && wp elementor flush-css

# Flush rewrite rules (after adding new pages)
wp rewrite flush
```

---

## Disabled Plugins

Hostinger-specific — do **not** re-enable locally:
- `hostinger.disabled`
- `hostinger-ai-assistant.disabled`
- `hostinger-easy-onboarding.disabled`

---

## Deploying to Production

1. Export the local DB via **All-in-One WP Migration**
2. Upload files via FTP or Git
3. Import DB on Hostinger
4. ⚠️ **Reset the admin password immediately** — local uses `admin123`
5. Re-activate Hostinger plugins after deploy
