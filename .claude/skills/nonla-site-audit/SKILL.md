---
name: nonla-site-audit
description: Audit the Nonla Escape NGO website for accessibility (WCAG 2.2 AA, colour contrast, keyboard, focus, alt text, reduced motion), SEO (titles, meta descriptions, Open Graph, canonical, hreflang, headings), schema.org NGO / Organization structured data, and performance (asset weight, images, video, fonts, request count). Use when asked to audit, review, check or improve accessibility, a11y, contrast, SEO, Google ranking, social sharing previews, structured data, page speed, Lighthouse or Core Web Vitals. Read-only - it reports findings and proposed fixes.
allowed-tools: Bash(bash .claude/skills/nonla-site-audit/scripts/contrast.sh *)
---

# Nonla site audit

Audit the local site at http://nonla-local-import.local (pages: `/`, `/projects/`, one `/project/<slug>/`, `/team/`, `/gallery/`, `/legal/`). Read-only GETs to https://nonlaescape.com are fine for comparison. Do not change files or the database during an audit. Check `app/public/docs/IMPROVEMENT-PLAN.md` first so known items are not reported as new.

Fetch each page once into the scratch directory and grep the saved HTML. Use the Playwright plugin, if loaded, for screenshots (1440 and 390 px), keyboard walks and the accessibility snapshot.

## Accessibility

- **Contrast.** Tokens are at the top of `wp-content/themes/nonla-theme/style.css`. Check any pair:
  ```bash
  bash .claude/skills/nonla-site-audit/scripts/contrast.sh '#B85520' '#FAF5EC'
  ```
  Needs 4.5:1 for body text, 3:1 for large text (24 px, or 18.66 px bold) and UI parts. Pairs worth checking first: `--terra` text on `--cream` or `--parch`, `--gold` and `--gold-l` on light backgrounds, white on `--terra-l` (button hover), and any text over the hero video, place cards or CTA photo (check the overlay, not the token).
- **Keyboard and focus.** Grep `style.css` for `:focus` and `outline`. Every link, button, filter tab, language button and accordion must show a visible focus state and work with Enter or Space. Destination cards reveal text on hover or tap: check there is a keyboard path. The mobile menu should trap focus and close on Escape.
- **Structure.** One `<h1>` per page, headings in order, a `<main>` landmark, a skip link. Pages on the default template can output the theme's `entry-title` h1 next to the custom one.
- **Images and media.** Meaningful `alt` on `<img>`. CSS background images that carry meaning need a text alternative. Autoplaying hero video needs a pause control if it runs over 5 seconds.
- **Language.** `<html lang>` is set once by PHP and switched by JS. Mixed-language text needs `lang` on the element.
- **Motion.** Grep for `prefers-reduced-motion`. Ticker, reveal animations and autoplay video should respect it.
- **Emoji as icons** (👤, 📍, Why Item icons) need `aria-hidden="true"` or a text label.

## SEO

- `<title>` and `<meta name="description">` unique per page. Canonical present. `blogdescription` set.
- Open Graph and Twitter card tags (`og:title`, `og:description`, `og:image`, `og:url`, `og:type`).
- The EN/RO switch runs in the browser only, so search engines see English. Report this as a limitation and note that real bilingual SEO needs separate URLs with `hreflang`.
- Project detail pages (`/project/<slug>/`): title, description, and that they are linked and indexable.
- `robots.txt`, XML sitemap, and that production does not have "Discourage search engines" on (`<meta name="robots">`).
- Internal links: the nav points to `/home`, which redirects to `/`.

## Structured data

Look for `application/ld+json`. The home page should carry an `NGO` (subtype of `Organization`) block. Only use facts from the site:

```json
{
  "@context": "https://schema.org",
  "@type": "NGO",
  "name": "Nonla Cultural Escape",
  "legalName": "Asociația Nonla Cultural Escape",
  "url": "https://nonlaescape.com/",
  "logo": "https://nonlaescape.com/wp-content/themes/nonla-theme/assets/photos/nonla-logo.png",
  "email": "nonlaescape@gmail.com",
  "taxID": "49845083",
  "address": { "@type": "PostalAddress", "addressLocality": "Câmpulung Muscel", "addressRegion": "Argeș", "addressCountry": "RO" },
  "sameAs": ["https://www.instagram.com/nonlaescape/", "https://www.facebook.com/nonlaescape/"]
}
```

Build URLs with `home_url()` in code. Do not add ratings, reviews, founding date or awards without a source. Upcoming projects can carry `Event` or `TouristTrip` markup from their real fields. Validate proposals against schema.org and Google's Rich Results Test (the user runs that).

## Performance

```bash
cd "app/public/wp-content/themes/nonla-theme/assets" && find . -type f -size +300k -exec ls -la {} \;   # heavy theme assets
curl -s -o /dev/null -w "%{size_download} bytes, %{time_total}s\n" http://nonla-local-import.local/
```

- For every image and video URL in the page HTML, get its size (`curl -sI` → `Content-Length`). Total the page weight. Flag images over 300 KB and any original camera file.
- `<img>` needs `width`, `height`, `loading="lazy"` below the fold, and responsive sizes. The hero poster should be small and the video `preload` conservative.
- Count stylesheets, scripts and font families. Look for fonts that are loaded but not used by the theme (Elementor's defaults next to the theme's Google Fonts).
- Fixed `?ver=1.0` on `style.css` and `ig-carousel.js` defeats cache busting after deploys.
- Local timings say little about production. Recommend PageSpeed Insights on the live URL for Core Web Vitals.

## Report

Group by area. For each finding: severity (blocker, high, medium, low), page, evidence (the tag, selector, ratio or byte count you measured), the file to change, and whether the fix is theme-only or needs inject. State what you could not check without a browser or a person. Finish with the five fixes that give the most for the least work, and offer to add the findings to `app/public/docs/IMPROVEMENT-PLAN.md`.
