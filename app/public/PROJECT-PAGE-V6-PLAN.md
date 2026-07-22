# Project Page V6 — Implementation Plan

Target template: `app/public/wp-content/themes/nonla-theme/single-nl_project.php`
Target post: `nl_project` slug `vietnam-escape-vol-6` (confirmed live at `/project/vietnam-escape-vol-6/`)

This template is a normal WP template resolved by the template hierarchy — **not** Elementor-injected.
Editing it takes effect on page reload; no `wp eval-file inject-elementor.php` run is needed. (Verified:
`sections/*.php` and `pages/*.php` are the only things that go through `inject-elementor.php`; this file
isn't referenced there.)

---

## 0. Ground truth checked before writing this plan

- **A "Vol. 6" project already exists** in the DB (`vietnam-escape-vol-6`, status `upcoming`), but it is
  scaffolding/test data, not real content: cover image `goku.jpeg`, description `<ul><li>1</li><li>2</li></ul><p>best</p>`,
  destinations `Dest1, Dest2, Dest3`, dates "October 14–29, 2026" (spec says 27 Oct–11 Nov), price €1,895
  (spec says €2,085), register URL `http://www.google.com`. This is almost certainly the "Vol. 100 test
  project" from `HANDOFF.md`'s backlog, renamed/repurposed rather than deleted — it was never removed.
- **Vol. 5** (`vietnam-escape-vol-5`, status `completed`) has real hero copy/dates/price/destinations, but —
  confirmed by fetching its live page — **zero itinerary days and zero accommodation posts attached**. The
  `HANDOFF.md` backlog items "Populate Vol. 5 itinerary" and "Populate Vol. 5 accommodations" are still
  outstanding. This matters a lot for §2 below: the itinerary accordion and accommodations grid currently
  render **nothing** on either live project page today. There is no real data to migrate or lose.
- **`wp-content/themes/nonla-theme/assets/photos/`** contains 8 images (`boats.jpg`, `garden.jpg`, `halong.jpg`,
  `hanoi.jpg`, `hero.jpg`, `ninhbinh.jpg`, `phuquoc.jpg`, `tamcoc.jpg`) plus the logo. Inspected two
  (`hanoi.jpg`, `garden.jpg`): both are empty architecture/landscape shots — exactly what the spec's
  "Visual Direction" section says to avoid (no close-up people, no shared meals/conversation/craft shots).
  The other six are named after places, not people, so the same is almost certainly true of them. **None of
  the assets currently in the repo satisfy the spec's photography direction.** See §6.
- `shortcodes/project-acf-fields.php` registers ACF fields for `nl_itinerary_day` and `nl_accommodation`
  only — despite its name, it does **not** register any `nl_project` fields. Every `nl_project` field read
  in `single-nl_project.php` (`series`, `edition`, `year`, `status`, `dates_en/ro`, `duration_en/ro`, `price`,
  `price_note_en/ro`, `desc_en/ro`, `cover_image`, `destinations`, `register_form_url`) exists **only as DB
  state** — this was independently reconfirmed by grep, matching the known data-loss risk.

---

## 1. Assessment — is the feedback sound?

**Sound, and worth doing:**
- The core critique (page currently reads like a travel-package listing: dates/duration/price front and
  center, itinerary accordion, hotel-rating grid) is accurate and the reframe to "mission → purpose →
  experience → practical info → participation" is a legitimate, well-scoped content strategy change, not
  scope creep.
- Splitting "what participants will experience" (destinations as learning environments) from "what's
  logistically covered" (Project Support) is a real improvement — it's currently conflated into one
  itinerary accordion that mixes both.
- Renaming "What's Included" → "Project Support" and reframing the price block as "Participation
  Contribution" are copy changes with no structural risk.
- The EN/RO `.t`/`data-en`/`data-ro` pattern and the `nl-reveal` scroll-in convention extend cleanly to all
  new sections — no new JS infrastructure is required.

**Ambiguous / needs a call before Opus builds it:**
- The spec's "Experience" section (4 destination cards, each needing *why selected / what you experience /
  cultural value*) is materially different from the existing sitewide `nl_place` CPT used in `sections/places.php`
  (home page "Where We Go"), which only carries `photo`, `tag`, `description`, `hover_details_en/ro` and is
  **global, not project-scoped** — the same Hanoi place-card is reused on every page that includes it. Reusing
  `nl_place` directly for the project page would either (a) require adding project-specific "why/value" copy
  to a CPT that's supposed to be a generic sitewide gazetteer, silently coupling two unrelated content models,
  or (b) fork the fields per-consumer, which ACF free UI can't scope cleanly. **Recommendation: don't reuse
  `nl_place`; give the project its own experience-stop data (see §3).**
- The spec's Purpose section ("four icon cards: Cultural Exchange, Personal Growth, Empathy, Human
  Connection") looks structurally identical to the existing `nl_why_item` CPT + `[nl_why_items]` shortcode
  used in `sections/why.php` for the *site's* "Why Nonla" section. Same trap as above: `nl_why_item` is a
  global, sitewide CPT (one set of items shown on the home page for the whole brand), not project-scoped.
  Do not attach project Purpose content to it. Recommendation in §3 is to hardcode these four items as static
  bilingual markup in the template (justified below).
- The spec says the hero should show Duration / Location / Group Size and explicitly **not** price. The
  current template puts price directly in the same meta row as dates/duration (`single-nl_project.php:114–126`).
  This is a real conflict, not just phrasing — the spec's whole thesis is "logistics second." Plan below
  moves price out of the hero entirely.

**Risky / would be wasted effort if done wrong:**
- Do NOT register a new ACF field group in PHP that redeclares existing `nl_project` field names (`series`,
  `price`, `desc_en`, etc.). ACF matches groups by `key`, not `name`; a second local group targeting the same
  post type with the same field `name`s but a different `key` will render **duplicate edit boxes** in
  wp-admin for the same underlying postmeta, not replace the DB-registered group. Full migration of the
  existing DB-only fields into PHP is valuable (it's the fix for the data-loss risk) but is a separate,
  larger task — out of scope for this plan except where explicitly noted as an open question in §8. This
  plan only adds **new** field names, chosen to not collide with anything already in the DB group.
- Building the Experience/Purpose data model as ACF Pro-style repeaters is not possible — ACF free has no
  repeater field. See §3 for the concrete workaround and why a different tool is picked for each section.

---

## 2. The core tension — itinerary accordion & accommodations grid

The spec explicitly rejects "hotel galleries" and wants logistics de-emphasized; the current page is built
around exactly that (`single-nl_project.php:178–229` itinerary accordion, `:231–293` accommodations grid with
booking-rating copy: *"rated 8.5 or higher on Booking.com"*, `:242–246`).

Given §0's finding that **neither Vol. 5 nor Vol. 6 has any itinerary/accommodation data attached today**,
the practical cost of each option is very different from what it would be if this data existed:

| Option | What happens | Cost | Recommendation |
|---|---|---|---|
| **A. Retire both sections from the template entirely** (remove the two `<section>` blocks, the two `get_posts()` queries, and the `nlToggleDay()` script) | Itinerary/accommodation CPTs stay registered in `cpt-registration.php` (harmless — private, not routed) but are never rendered anywhere. Content Support section now carries "what's included" (flights, transport, coordinators) as compact prose/list instead. | Zero data loss (nothing populated). Removes ~115 lines of template + the Booking.com-rating framing that's the most travel-agency-coded copy on the page. | **Recommended.** Matches the spec's design philosophy for Vol. 6 with the least implementation weight, and no live content regresses since there is none. |
| **B. Keep both sections, move them below the new narrative sections, restyle to de-emphasize** (e.g. collapse accommodations into a single-line-per-city list, keep itinerary accordion as an optional "Full Day-by-Day" disclosure) | Sections still exist for whenever Vol. 5's real itinerary/accommodation data eventually gets populated (per `HANDOFF.md` backlog). | More CSS/markup work now for content that doesn't exist yet on either live project. | Reasonable if the org wants to keep a "Full itinerary available on request" pattern per project long-term — but that's a product decision, not an implementation one (see §8). |
| **C. Hide via conditional (`if ($itinerary)`/`if ($accomm)` — already present) and do nothing else** | Sections silently vanish for Vol. 6 (no data) but would still render, unchanged, the moment someone *does* populate Vol. 5's data — reintroducing the exact hotel-grid/day-accordion UI the spec objects to. | Cheapest short-term, but leaves a landmine: the day someone fills in Vol. 5's itinerary, the "old" page reappears without anyone deciding that was wanted. | Not recommended — false economy. |

**This plan proceeds with Option A** (retire from the template) and folds "what's included" into the new
Project Support section (§4.5) as a bullet list, which is the spec's own suggested replacement content.
CPT registrations, the ACF field group in `project-acf-fields.php`, and the WP-admin menu entries for
Itinerary Days / Accommodations are left untouched — deleting the CPTs themselves is out of scope and
irreversible for no benefit (no code currently depends on them existing, and future projects may still want
a light-touch "detailed itinerary on request" flow via option B).

---

## 3. Data model

### New ACF fields on `nl_project`

Register these in a **new** file, `shortcodes/project-fields-v6.php`, using a **new** field group key
(`group_nl_project_v6`) and the `acf_add_local_field_group` style already used in `project-acf-fields.php`.
Do not reuse or redeclare any existing field name (`series`, `edition`, `year`, `status`, `dates_en/ro`,
`duration_en/ro`, `price`, `price_note_en/ro`, `desc_en/ro`, `cover_image`, `destinations`,
`register_form_url`) — all names below are new, so there is no duplicate-edit-box collision (see §1).

| Field name | Type | Purpose |
|---|---|---|
| `group_size` | text | e.g. `"20 participants"`. Rendered in hero meta row in place of price. |
| `about_en` / `about_ro` | textarea | About the Project body copy (philosophy-first framing). Reuse `nl_format_desc()` from `shortcodes/projects.php:5` for bullet-friendly rendering, same as `desc_en/ro` already does. |
| `support_intro_en` / `support_intro_ro` | textarea | 1–2 sentence intro for Project Support section. |
| `support_items_en` / `support_items_ro` | textarea | Line-delimited list (`- ` prefix per line, same convention `nl_format_desc()` already parses into `<ul>`) — flights, accommodation, transport, transfers, coordinators, breakfast, activities. |
| `participation_note_en` / `participation_note_ro` | textarea | "What the contribution covers" explanation. *(Do not reuse `price_note_en/ro` — that field is still needed for Register Now cards on `/projects` and is written in a different voice ("Flights included · 13 nights…"); repurposing it would change copy on the projects listing too.)* |
| `presentation_pdf_url` | url, not required | Link to the Project Presentation PDF, referenced from Participation Contribution section per spec §6. |
| `audience_items_en` / `audience_items_ro` | textarea | Line-delimited "Who Is This For" characteristics, same bullet convention. |
| `cta_heading_en` / `cta_heading_ro` | text | Final CTA heading (spec wants this distinct/emotional per project, not the generic site CTA). |
| `cta_body_en` / `cta_body_ro` | textarea | Final CTA supporting copy. |

`price` (existing) is kept and reused, just moved out of the hero into the new Participation Contribution
section only (§4.6) — no schema change needed there.

### Experience destinations — repeater workaround

**ACF free has no repeater field.** This section (spec §4: 4 destination cards, each needing why-selected /
what-you-experience / cultural-value copy, i.e. genuinely structured, multi-field, ordered content) is where
that limitation actually bites — unlike Purpose (below), the content varies per project and per destination.

Two workable options; recommendation given below:

- **Numbered fields on `nl_project`** (`exp_1_name`, `exp_1_photo`, `exp_1_why_en/ro`, `exp_1_experience_en/ro`,
  `exp_1_value_en/ro`, repeated ×4). Simple, no new CPT, but hard-caps at 4 and clutters one edit screen with
  ~24 fields.
- **New child CPT `nl_experience_stop`**, following the exact precedent already established by
  `nl_itinerary_day` and `nl_accommodation` in `project-acf-fields.php`: a `post_object` field `project`
  (required, points at `nl_project`), a `number` field `display_order`, plus `photo` (image, return url),
  `tag` (text — short label like "5-day homestay"), `why_en/ro`, `experience_en/ro`, `value_en/ro`. Post
  title = destination name (Hanoi, Ha Giang, …), matching how `nl_accommodation`'s title is the hotel name.

**Recommendation: the child-CPT approach.** It costs one more file (a field-group registration mirroring the
existing itinerary/accommodation block almost line-for-line) but keeps `nl_project`'s own edit screen from
becoming a 30+ field wall, isn't hard-capped at exactly 4 destinations if a future volume visits 3 or 5
places, and is the pattern this codebase has already chosen twice for exactly this kind of problem — it's
the path of least surprise for whoever maintains this next. Register it in `cpt-registration.php` (add one
row to the `$cpts` array, `null` slug since it's not publicly routed, same as `nl_itinerary_day`) and add its
field group to `project-acf-fields.php` (same file, since it already owns this exact pattern) rather than the
new `project-fields-v6.php` file (which is for genuinely new `nl_project`-level fields only).

### Purpose section — no new data model needed

The four Purpose outcomes (Cultural Exchange / Personal Growth / Empathy / Human Connection) are, per the
spec itself, a fixed suggested list, not project-varying data — Vol. 7, Vol. 8 etc. would very plausibly use
the identical four outcomes since they describe Nonla's mission, not this specific trip. Making these
ACF-driven (via numbered fields or a CPT) buys flexibility nobody asked for at the cost of extra admin
surface. **Recommendation: hardcode as static bilingual markup in the template** (four `<div class="t"
data-en="…" data-ro="…">` blocks), matching how `nl_format_desc()`-independent static section headers are
already written throughout `single-nl_project.php` (e.g. the "Day by Day" eyebrow, `:182–188`). If a future
project genuinely needs different Purpose copy, promote to ACF fields at that time — don't build it now.

---

## 4. Section-by-section implementation

All new sections go inside `<div class="nl-project-detail">…</div>` in `single-nl_project.php`, replacing the
itinerary block (`:178–229`) and accommodations block (`:231–293`). Keep the existing page-header block
(`:60–176`) as the skeleton for Hero but edit its contents per §4.1. Keep the back-CTA block (`:295–303`) or
fold it into the new Final CTA (§4.8).

### 4.1 Hero (edits to existing header block, `:60–176`)
- Keep breadcrumb, status badge, `<h1>` title, `nl_format_desc()`-rendered lead paragraph — all already present
  and already correct per spec (title + short description).
- **Meta row (`:93–127`)**: replace the 3-item grid (Dates / Duration / Price) with **Duration, Location,
  Group Size** — drop Price entirely from the hero (spec explicitly separates practical info from
  mission-first hero). Location = existing `$dests` array (already computed at `:27–28` from the
  `destinations` field), rendered as plain text joined by `" · "` instead of pills, to match the wireframe's
  `<p>Hanoi · Ha Giang · Phu Quoc · Lan Ha Bay (UNESCO Heritage)</p>` — the parenthetical UNESCO note is just
  additional text in the `destinations` field value, no schema change. Group Size = new `group_size` field
  (§3).
- **Move `$dests` pill row (`:128–134`)** out of the hero — either delete (location is now covered by the
  meta-row line above) or relocate into the Experience section as a quick-reference strip. Recommend delete
  to avoid saying the same thing twice in the hero.
  - Move the existing `$price`/`$note_en` block (`:114–126`) down into the new Participation Contribution
    section (§4.6) instead of deleting it — same markup, new location.
- **CTA (`:135–165`)**: keep the existing Register Now / Register for Next Edition / Follow-on-Instagram
  conditional logic verbatim — it already matches spec's "Primary CTA (Apply)" requirement functionally (button
  copy can be reworded to "Apply for the Programme" as a `data-en` string, still linking to `register_form_url`).
  No functional change, copy-only.
- CSS: no new classes needed for the meta-row content swap; reuse `.nl-detail-meta-item`, `.nl-meta-label`,
  `.nl-meta-value` (`style.css:733` and surrounding, already used for Dates/Duration/Price).

### 4.2 About the Project (new section)
- New `<section class="nl-detail-section nl-about-section">` placed immediately after the hero header block.
- Eyebrow + `nl-section-title` (same pattern as Itinerary/Accommodations sections used, `:182–188` /
  `:235–241`) — e.g. `data-en="About"` / `data-en="the Project."`.
- Body: `nl_format_desc($about_en)` / `nl_format_desc($about_ro)`, rendered exactly like `$fmt_en`/`$fmt_ro`
  are at `:83–92`.
- CSS: reuse `.nl-detail-section`, `.nl-detail-inner` (`style.css:745–747`); body text can reuse whatever class
  `.nl-page-subtitle`/`.nl-project-desc` uses (`:87`) or a new `.nl-about-body` with the same
  `font-size/line-height/font-weight` as `.nl-accomm-desc` (`:826`) for consistency (17px / 1.75 / 300).

### 4.3 Purpose (new section)
- Eyebrow "Purpose" / section title. Intro sentence (can be a fixed static string per spec, no field needed —
  it's boilerplate: "This project promotes intercultural dialogue…").
- 4-item icon-card grid, **hardcoded** per §3: Cultural Exchange, Personal Growth, Empathy, Human Connection —
  each with a short static description translated via `.t`/`data-en`/`data-ro`.
- CSS: reuse `.nl-why-grid`/`.nl-why-item`/`.nl-why-icon`/`.nl-why-title`/`.nl-why-desc` (`style.css:361–370`)
  directly — visually identical pattern (icon + title + short desc, 3-col responsive grid). Since this is 4
  items not 3, either add a `.nl-why-grid--4` modifier (`grid-template-columns: repeat(4, 1fr)`, collapsing to
  2 at the existing 960px breakpoint, 1 at 540px — see `:691–692` for the existing responsive precedent) or
  reuse `.nl-why-grid` unmodified at 3 columns (4 items will wrap to a 3+1 layout, acceptable). Recommend the
  modifier for a cleaner 2×2 tablet layout.

### 4.4 The Experience (new section)
- Eyebrow "The Experience" / title. Intro sentence field or static copy (spec's intro line is generic
  boilerplate — can be static like Purpose).
- Grid of `nl_experience_stop` posts (query pattern identical to itinerary/accommodation, `:38–56`: `post_object`
  meta_query on `project` = current post ID, ordered by `display_order`). Each card: photo background, name
  (post title), `tag` as small label, then `why_en/ro`, `experience_en/ro`, `value_en/ro` as three short
  labeled blocks (or one flowing paragraph — content-editor's choice at population time).
- CSS: adapt the visual language of `.nl-dest-card`/`.nl-dest-grid`/`.nl-dest-overlay`/`.nl-dest-tag`/
  `.nl-dest-name`/`.nl-dest-desc` (`style.css:333–354`) but under **new class names**
  (`.nl-experience-grid`/`.nl-experience-card`/…) rather than reusing the `.nl-dest-*` classes directly —
  those classes are also used by the sitewide `[nl_places]` shortcode (`shortcodes/places.php`) on the home
  page; sharing classes would couple styling changes across two independent content sources. Grid: 4 columns
  desktop → 2 tablet → 1 mobile (mirror `:690`/`:850` breakpoints). Since each card here needs 3 text blocks
  (why/experience/value) versus places' single `desc`, this isn't a hover-reveal card like `.nl-dest-hover` —
  render all three always-visible, stacked, since the spec wants this to read as substantive content, not a
  hover teaser.

### 4.5 Project Support (replaces "What's Included" framing)
- Eyebrow "Project Support" / title (e.g. `data-en="Project"` / `data-en="Support."`).
- `support_intro_en/ro` rendered as a paragraph.
- `support_items_en/ro` rendered via `nl_format_desc()` (already produces a `<ul>` from `- `-prefixed lines) —
  same helper as `desc_en`, no new parsing code needed.
- This is also where the retired itinerary/accommodations' "what's covered" content effectively lives now
  (flights, transport, coordinators, breakfast — content that used to imply itself via the accommodation grid
  and the day-by-day activities).
- CSS: reuse `.nl-detail-section`; the `<ul>` from `nl_format_desc()` needs a checklist-style treatment —
  add a small new rule (e.g. `.nl-support-list li { padding-left: 28px; position: relative; }` with a check
  SVG or `::before` bullet in `--terra`) since no existing list-styling class fits (the itinerary/accommodation
  descriptions never render bulleted lists in production content today).

### 4.6 Participation Contribution
- Eyebrow / title "Participation Contribution".
- `participation_note_en/ro` paragraph.
- Amount: reuse the existing `$price` value and markup pattern from the old hero meta block (`.nl-meta-price`
  styling, `style.css` — search `nl-meta-price`), but presented larger/standalone here, e.g. an `<h3>` per the
  wireframe (`<h3>€2,085</h3>`).
- Optional PDF link: render only `if ($presentation_pdf_url)`, styled like `.nl-accomm-link` (`:827–834`,
  arrow-suffixed text link) — e.g. "Download Project Presentation".
- CSS: mostly reuse; one new rule for the large standalone price treatment if `.nl-meta-price`'s existing
  size (used inline in a meta grid) is too small for a section centerpiece.

### 4.7 Who Is This Project For?
- Eyebrow / title.
- Intro sentence (static or field — reuse `about_en`-style single field if wanted, or keep static since spec's
  intro is boilerplate; not adding a field for this to keep the field count down).
- `audience_items_en/ro` via `nl_format_desc()`, same bulleted-list pattern as Project Support (§4.5). Reuse
  `.nl-support-list` styling or a differently-named alias — same visual treatment either way.

### 4.8 Final CTA
- Replace or merge with the existing back-CTA block (`:295–303`).
- `cta_heading_en/ro`, `cta_body_en/ro` fields, then the same Register Now / Instagram-fallback button logic
  already built for the hero (§4.1) — don't duplicate the conditional, factor it into a small local PHP
  function or just repeat the existing markup block, since it's only used twice.
- CSS: can reuse `.nl-cta-inner`/`.nl-eyebrow` treatment from the sitewide CTA (`style.css:391–392` references
  `.nl-cta-inner .nl-eyebrow`) if a matching wrapper class exists in `sections/cta.php` — check that file's
  markup for the class name to mirror before inventing a new one.

---

## 5. CSS additions (`wp-content/themes/nonla-theme/style.css`)

All additions should sit in the existing "PROJECT DETAIL" block (`:720` onward) since that's where all
single-project styling already lives. New rules needed, all built from existing tokens (`--terra`, `--gold-l`,
`--text2`, `--muted`, `--border`, `--card`, `--shadow` — no new custom properties required):

- `.nl-why-grid--4` (or equivalent 4-col modifier) — §4.3.
- `.nl-experience-grid`, `.nl-experience-card`, `.nl-experience-overlay` (or non-overlay stacked-text
  variant), `.nl-experience-tag`, `.nl-experience-name`, plus three text-block classes for why/experience/value
  — §4.4. Base off `.nl-dest-*` (`:333–354`) but as new declarations, not shared selectors.
- `.nl-support-list` (checklist bullet styling) — §4.5, reused for §4.7.
- `.nl-participation-amount` (large standalone price) — §4.6, if `.nl-meta-price`'s existing size doesn't work
  standalone.
- Responsive: extend the existing `@media (max-width: 960px)` block (`:838–847`) with the new grid's
  collapse rules rather than creating a new breakpoint block.

No new design tokens are needed — the existing palette (terra/gold/forest/cream/parch) covers every section's
requirements; this is a copy/structure change, not a visual-identity change.

---

## 6. Content gaps — what a human must produce

1. **Photography.** None of the 8 images in `assets/photos/` fit the spec's people-first direction (verified:
   `hanoi.jpg` and `garden.jpg` are empty-of-people architecture/landscape shots; the other six are named for
   places, not people, strongly suggesting the same). The spec explicitly wants: people interacting, shared
   meals, conversations, homestays, traditional crafts, genuine emotion — and explicitly rejects empty
   landscapes. **This is the single biggest content gap.** Someone needs to source or shoot new photography
   (from a past Vol. 5 trip, presumably) before the Hero, About, and Experience sections can look like the
   spec intends — pushing new landscape-only photos into an "experience card" would visually contradict the
   copy sitting next to it.
2. **Vol. 6 real data.** Every current field on the `vietnam-escape-vol-6` post is placeholder (§0): cover
   image, description, destinations, dates, price, register URL. All of it needs real content regardless of
   this redesign — this plan's new fields (`about_en/ro`, `support_items_en/ro`, `participation_note_en/ro`,
   `audience_items_en/ro`, `cta_heading_en/ro`, `cta_body_en/ro`, `group_size`, and every `nl_experience_stop`
   post) start from zero.
3. **Project Presentation PDF.** Spec §6 references this as the canonical source for "complete practical
   details" and as an optional download link. No such asset currently exists in the repo or was found during
   this investigation — needs to be produced/uploaded before `presentation_pdf_url` can be populated.
4. **Application form.** `register_form_url` already exists as a field and Vol. 6 currently points it at
   `http://www.google.com` (placeholder) — needs the real Google Form URL before launch, independent of this
   redesign.
5. **Purpose/Audience/Support copy voice.** The wireframe and spec give example bullet points, but final,
   Nonla-voiced EN+RO copy for About, Purpose intro, Project Support intro, Participation note, and Audience
   intro doesn't exist yet — spec explicitly calls out a "Final copy document" as a supporting asset this
   implementation should reference, which wasn't provided to this planning task. Flagging as a hard
   dependency before Opus can populate real strings (placeholders/lorem ipsum can carry the build, per the
   existing pattern already used for Team bios/Testimonials per `HANDOFF.md`'s backlog table).

---

## 7. Migration / rollout

1. **Order of operations:**
   a. Register new ACF field groups first — `shortcodes/project-fields-v6.php` (new `nl_project` fields, §3)
      and the `nl_experience_stop` additions to `project-acf-fields.php` + `cpt-registration.php`. Verify in
      wp-admin that no duplicate field boxes appear on the existing `nl_project` edit screen (confirms no name
      collision with the DB-only group).
   b. Populate placeholder/lorem content on Vol. 6 for every new field (mirrors how Team/Testimonials already
      carry lorem ipsum per `HANDOFF.md`) — needed to visually verify the template before real copy exists.
   c. Rewrite `single-nl_project.php` per §4 — hero edits, remove itinerary/accommodation blocks, add the six
      new sections.
   d. Add CSS per §5.
   e. Verify on `http://nonla-local.local/project/vietnam-escape-vol-6/` (no injection step required, per the
      confirmed architecture fact above).
   f. Verify on `http://nonla-local.local/project/vietnam-escape-vol-5/` — see backward-compatibility note
      below.

2. **What breaks the existing Vol. 5 page:** Vol. 5 has none of the new fields populated (all new ACF fields
   will read empty via `get_field()`). Every new section in §4 must be wrapped in the same
   `if ($field_value): … endif;` conditional style already used throughout the current template (e.g. `:94`,
   `:104`, `:114`, `:128`) so sections silently don't render rather than showing empty headers/broken layout.
   The Hero's meta row (§4.1) needs the same treatment: if `group_size` is empty, drop that meta item, don't
   render a blank slot. **Concretely: do not assume every project will have every new field populated — Vol.
   5 won't, until someone backfills it, and it should degrade gracefully, not break.**

3. **Backward compatibility for other/future projects:** Since itinerary/accommodation sections are removed
   from the template entirely (§2 Option A), any future project that *does* get itinerary/accommodation data
   populated will have that data silently orphaned (still in the DB, `nl_itinerary_day`/`nl_accommodation`
   posts still exist and are still editable in wp-admin, but nothing on the front end reads them anymore).
   This is intentional per §2 but should be a conscious, documented decision — flagged again in §8 as
   something the user should explicitly confirm before Opus deletes ~115 lines of working template code.

4. **Verification checklist:**
   - Both EN and RO render correctly for every new string (toggle via the nav's EN/RO buttons, confirm no
     English leaks into RO mode and vice versa) — the most common mistake with this pattern is forgetting the
     `data-ro` attribute and leaving the RO fallback equal to EN, which is acceptable per CLAUDE.md's own
     fallback convention but should be a deliberate choice, not an oversight.
   - `nl-reveal` scroll animations fire on all new sections (class already used throughout; just confirm it's
     added to the new section wrappers).
   - Mobile breakpoints (960px, 540px) checked for the new grids specifically — Experience (4→2→1) and Purpose
     (4→2→1) are new grid configurations not covered by existing breakpoint rules until §5's additions land.
   - Confirm no duplicate "Project" field boxes appear in wp-admin's `nl_project` editor after adding
     `project-fields-v6.php` (validates the no-name-collision claim in §1/§3).

5. **`HANDOFF.md` backlog cross-reference:** This plan does not address "Remove Vol. 100" (§0 suggests this
   *is* the current Vol. 6 post, repurposed rather than deleted — confirm with the user before assuming so),
   "Team bios/photos", or "Testimonials" — all out of scope here. It also does not populate Vol. 5's
   itinerary/accommodation data (§0/§2) — since this plan removes the rendering path for that data from the
   template, populating it is now moot unless §2 Option B is chosen instead (open question, §8).

---

## 8. Out of scope / open questions for the user

1. **Confirm the Vol. 6 test project's identity.** Is `vietnam-escape-vol-6` the repurposed "Vol. 100" test
   project from the `HANDOFF.md` backlog, or a separate placeholder someone created? Should Opus build/verify
   directly on this post, or create a fresh one?
2. **§2 decision required:** retire itinerary/accommodation entirely (Option A, this plan's assumption) vs.
   keep a de-emphasized version (Option B) vs. something else. This is a product decision about whether
   Nonla ever wants a detailed day-by-day view anywhere on the site, not an implementation detail — Opus
   should not decide this alone.
3. **Full ACF migration to PHP.** This plan only registers *new* fields in PHP, per CLAUDE.md's instruction,
   and deliberately does not touch the existing DB-only `nl_project` field group to avoid the duplicate-box
   collision described in §1. Migrating the old fields into PHP too (closing the data-loss risk documented in
   the project's own memory notes) is valuable but separate work — confirm whether it should be bundled into
   this task or scheduled independently.
4. **Photography sourcing** (§6.1) — who is producing/selecting the people-first photography, and by when?
   This blocks a faithful implementation of Hero/About/Experience regardless of how well the code is built.
5. **Copy document** (§6.5) — the "Final copy document" the spec references as a supporting asset was not
   supplied to this planning task. Confirm whether Opus should draft placeholder EN/RO copy from the spec's
   own example bullets (acceptable per the site's existing lorem-ipsum precedent) or wait for real copy.
6. **Purpose section data model** (§3) — this plan recommends hardcoding the four outcome cards as static
   markup rather than making them ACF-driven, on the assumption they're mission-level, not trip-level content.
   Confirm this assumption; if Purpose copy should vary by volume, this needs numbered fields or a CPT instead
   before Opus builds it.
7. **`nl_experience_stop` vs. numbered fields** (§3) — this plan recommends the child-CPT approach. If the
   user wants to minimize new CPTs/admin-menu clutter over long-term flexibility, numbered fields on
   `nl_project` are the documented fallback — confirm preference.
8. **Vol. 6 launch readiness** is gated on §6 (content gaps) regardless of how well this plan's code is
   executed — worth setting expectations that "page redesign done" and "Vol. 6 page launch-ready" are
   different milestones.
