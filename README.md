# nonla-local

Local dev for **nonlaescape.com** — Local by Flywheel, http://nonla-local.local

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
shortcodes/team.php          ← [nonla_team]
shortcodes/projects.php      ← [nonla_projects]
shortcodes/places.php        ← [nl_places]
shortcodes/series.php        ← [nl_series]
shortcodes/gallery.php       ← [nl_gallery_items]
shortcodes/why.php           ← [nl_why_items]
shortcodes/testimonials.php  ← [nl_testimonials]
shortcodes/countdown.php     ← [nl_countdown]
shortcodes/cpt-registration.php  ← CPT definitions (rarely touched)
```

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

> Photos for populate-content.php must be at `C:\Users\ionut\Desktop\Photos\modified\`

---

## Dev loop

1. Make changes locally
2. `wp eval-file inject-elementor.php` — rebuild Elementor pages
3. `wp elementor flush-css` — flush CSS cache
4. Test on http://nonla-local.local

---

## Deploy to Hostinger

### Full site deploy (AIWPM)

1. WP Admin (local) → All-in-One WP Migration → Export → Advanced Options
   - ✅ Exclude media library
   - ✅ Exclude plugins
2. Download the `.wpress` file (~158MB, comfortably under the 256MB limit)
3. WP Admin (Hostinger) → All-in-One WP Migration → Import → upload `.wpress`
4. Hostinger WP Admin → Elementor → Tools → **Clear Files & Data**
5. **Reset admin password** — local uses `<local admin password — see Local by Flywheel app>`

> Plugins and media don't need re-uploading — they're already on Hostinger from the original site.
> Only new assets (videos, photos added locally) need FTP upload after import.

### New assets only (FileZilla)

Upload directly to `public_html/wp-content/themes/nonla-theme/assets/` — no export needed.

### DB changes only

```bash
wp db export ~/nonla-export.sql   # export locally
# import via Hostinger phpMyAdmin
wp search-replace 'nonla-local.local' 'nonlaescape.com' --all-tables  # run on Hostinger via SSH
```
