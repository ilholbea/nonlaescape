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

## Deploy

1. Export via All-in-One WP Migration (exclude media to stay under the 768MB limit)
2. Import on Hostinger
3. **Reset WP admin password immediately** — local uses `admin123`
4. Run `wp elementor flush-css` on production
5. Re-activate Hostinger plugins (hostinger, hostinger-ai-assistant, hostinger-easy-onboarding)
