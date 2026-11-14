# nonla-local

Local dev for **nonlaescape.com** — Local by Flywheel, http://nonla-local-import.local

---

## What to change and where

### Page HTML (headlines, copy, structure)
Edit the relevant file, then run `wp eval-file inject-elementor.php && wp elementor flush-css`.

```
sections/hero.php          ← hero video, headline, stats
sections/ticker.php        ← scrolling ticker items + countdown
sections/about.php         ← Who We Are text + photos
sections/series.php        ← Two Series block
sections/places.php        ← Destinations grid
sections/why.php           ← Why Nonla grid
sections/testimonials.php  ← Stories / testimonials
sections/gallery.php       ← Gallery row
sections/cta.php           ← Ready to Escape? CTA

pages/projects.php         ← Projects page header
pages/team.php             ← Team page header + bottom CTA
pages/legal.php            ← Legal page header + legal text
```

### Styles
`wp-content/themes/nonla-theme/style.css` — all CSS, design tokens at the top.

### Nav / Footer
`wp-content/themes/nonla-theme/header.php` — nav links, logo
`wp-content/themes/nonla-theme/footer.php` — footer columns, tagline

### Shortcode logic (how CPT data renders)
Each shortcode is its own file — edit and save, no inject needed:

```
shortcodes/team.php              ← [nonla_team]
shortcodes/projects.php          ← [nonla_projects] — project cards list
shortcodes/places.php            ← [nl_places]
shortcodes/series.php            ← [nl_series]
shortcodes/gallery.php           ← [nl_gallery_items]
shortcodes/why.php               ← [nl_why_items]
shortcodes/testimonials.php      ← [nl_testimonials]
shortcodes/countdown.php         ← [nl_countdown]
shortcodes/cpt-registration.php  ← CPT definitions (rarely touched)
shortcodes/project-acf-fields.php ← ACF fields for itinerary + accommodations
```

### Project detail pages
Each `nl_project` gets its own URL (`/project/{slug}/`) rendered by `single-nl_project.php`.

Two child CPTs feed the detail sections — add entries in WP Admin:
- **Itinerary Days** → post title = EN day title, set Project + Day # + Date + RO title + descriptions
- **Accommodations** → post title = hotel name, Featured Image = main photo, set Project + City + Order + descriptions + Booking URL + extra photos

### Content (places, team, projects, testimonials…)
WP Admin → the relevant post type. Changes are live immediately.

### Site settings (contact email, registration URL)
WP Admin → Settings → Nonla Settings.

---

## Commands

```bash
wp eval-file inject-elementor.php   # rebuild all 4 Elementor pages
wp elementor flush-css              # flush CSS cache (run after any inject)
wp eval-file populate-content.php   # repopulate CPT content from scratch
```

> Photos for populate-content.php must be at `C:\Users\micro-local\Desktop\Photos\modified\`

---

## Dev loop

1. Make changes locally
2. `wp eval-file inject-elementor.php` — rebuild Elementor pages
3. `wp elementor flush-css` — flush CSS cache
4. Test on http://nonla-local-import.local

---

## Deploy to Hostinger

Follow **[docs/deploy-hostinger.md](docs/deploy-hostinger.md)**. In short: code goes up by FTP (the `wp-content/themes/nonla-theme/` folder, then Elementor → Tools → Clear Files & Data), layout changes in `sections/` or `pages/` also need the inject run over SSH, and content is created only in production WP Admin.

> **Never import an All-in-One WP Migration file (or any local database) into production.** It replaces the live content with the local test copy. The full-AIWPM deploy that used to be described here is retired.
