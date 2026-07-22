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
  confirmed by fetching its live page — renders **no itinerary days and no accommodation cards**. Checked
  directly against the DB: there is exactly **one** `nl_itinerary_day` post (ID 267, "Test itinerary Day 1")
  and exactly **one** `nl_accommodation` post (ID 268, "Accommodation Test 1") in the entire database — and
  both have their `project` post-object field pointing at post ID 257, which **no longer exists** (hard-deleted
  — almost certainly the old "Vol. 100" test project). Both are orphaned test records that match no
  `meta_query` for any currently-existing project, which is why they render on neither Vol. 5 nor Vol. 6.
  `HANDOFF.md`'s backlog items "Populate Vol. 5 itinerary" and "Populate Vol. 5 accommodations" are still
  outstanding. **This does not mean the itinerary/accommodation CPTs are unused or safe to remove** — see the
  governing constraint below and §2's revised recommendation. It does mean the two orphaned posts are dead
  weight that should be deleted as a cleanup step (§7) so they don't confuse an owner browsing the Itinerary
  Days / Accommodations admin screens.
- **Governing constraint (from the site owner, overrides implementation convenience throughout this plan):**
  everything this page needs must remain addable/editable from wp-admin by a non-technical owner — that's the
  entire reason the CPT/ACF architecture exists. Any recommendation that trades away admin-editability for
  less code (e.g., hardcoding copy, deleting a CPT's rendering path) is rejected by default unless there's no
  admin-editable alternative. This reshapes §2 (itinerary/accommodations must stay editable, not be retired)
  and §3 (Purpose section must be field-driven, not static markup) versus an earlier draft of this plan.
- **This also escalates the ACF-fields-only-in-the-DB problem (see §7).** If wp-admin is the load-bearing
  editing surface, a DB-only ACF field group isn't a background data-loss risk anymore — it's a single point
  of failure for the owner's ability to use the site at all. If that field-group post is lost on a bad
  re-import, the edit screen for `series`, `price`, `dates_en/ro`, etc. disappears from wp-admin entirely
  (front-end reads still work via raw postmeta, but nobody can *edit* those fields without a developer). §7
  now treats migrating this into PHP as effectively required, not optional.
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
  Do not attach project Purpose content to it — editing it would change the home page's "Why Nonla" section
  too, which is a different piece of content the owner did not ask to link together. Per the governing
  admin-editability constraint, this section must still be a wp-admin-editable field, not template markup —
  §3 recommends project-scoped numbered fields on `nl_project` (distinct from `nl_why_item`) so Purpose can
  be edited per volume without touching the sitewide CPT.
- The spec says the hero should show Duration / Location / Group Size and explicitly **not** price. The
  current template puts price directly in the same meta row as dates/duration (`single-nl_project.php:114–126`).
  This is a real conflict, not just phrasing — the spec's whole thesis is "logistics second." Plan below
  moves price out of the hero entirely.

**Risky / would be wasted effort if done wrong:**
- Do NOT register a new ACF field group in PHP that redeclares existing `nl_project` field names (`series`,
  `price`, `desc_en`, etc.) under a **different** `key` than the DB-stored group already uses. ACF matches
  groups by `key`, not `name`; a second local group targeting the same post type with the same field `name`s
  but a different `key` will render **duplicate edit boxes** in wp-admin for the same underlying postmeta,
  not replace the DB-registered group. Given the governing admin-editability constraint (§0), this plan no
  longer treats the full DB→PHP migration of the existing fields as optional or deferred — §7 now specifies
  it as part of this task, using the same `key` the DB group already has (found via ACF's own export tool)
  specifically so it supersedes the DB copy instead of duplicating it. All the genuinely **new** fields this
  plan introduces (§3) still get their own new names/keys with no collision either way.
- Building the Experience/Purpose data model as ACF Pro-style repeaters is not possible — ACF free has no
  repeater field. See §3 for the concrete workaround and why a different tool is picked for each section.

---

## 2. The core tension — itinerary accordion & accommodations grid

The spec explicitly rejects "hotel galleries" and wants logistics de-emphasized; the current page is built
around exactly that (`single-nl_project.php:178–229` itinerary accordion, `:231–293` accommodations grid with
booking-rating copy: *"rated 8.5 or higher on Booking.com"*, `:242–246`).

An earlier draft of this plan read §0's finding (neither live project currently renders any itinerary/
accommodation content) as "nothing would be lost by deleting the rendering path." **That reasoning is
rejected under the governing admin-editability constraint (§0).** The correct reading of "not populated" is
"not populated *yet*" — `nl_itinerary_day` and `nl_accommodation` exist specifically so a non-technical owner
can add day-by-day detail and lodging info per project without a developer, and removing the template's
rendering path would silently take that capability away even though the CPTs stayed in the admin menu
(an owner would add itinerary days that then go nowhere — worse than not having the feature at all).

| Option | What happens | Cost | Recommendation |
|---|---|---|---|
| A. Retire both sections from the template entirely | Owner-entered itinerary/accommodation data would have no front-end rendering path at all. | Removes ~115 lines of template code and all Booking.com-style framing. | **Rejected** — violates the governing admin-editability constraint; owners must be able to add these per project. |
| **B. Keep both sections, move them below the new narrative sections, restyle to de-emphasize** | Sections remain wired to their existing `get_posts()`/`meta_query` logic and the `if ($itinerary)`/`if ($accomm)` conditionals (`single-nl_project.php:179`, `:232`) so they stay silently hidden until an owner actually populates them — exactly as they behave today. | Some CSS/markup rework (below) to change *how* the populated state looks, no rework of the underlying data path. | **Recommended.** Reconciles the spec's "no hotel galleries / logistics second" objection with the admin-editability requirement: the fix is ordering and visual weight, not deletion. |
| C. Hide via conditional only, no repositioning or restyling | Sections stay exactly where they are today (near the top, styled as a hotel grid) the moment any project's data is populated. | Cheapest, but doesn't address the spec's actual objection — the travel-agency framing is a styling/ordering problem, and doing nothing here just defers it to whenever an owner first fills in accommodation data. | Not recommended on its own — Option B does everything this does plus what the spec is actually asking for. |

**This plan proceeds with Option B.** Concretely:

- **Reposition:** move both sections out of their current position (immediately after the hero) to sit after
  Project Support (§4.5) and before Participation Contribution (§4.6) — see the full section order in §4.
  This matches the spec's own stated flow (*"Mission → Purpose → Experience → Practical Information →
  Participation"*, PDF p.6–7): itinerary/accommodation detail is exactly the kind of "practical information"
  the spec places after Experience and before Participation, not a rejection of the content, a rejection of
  where the old build put it (directly under the hero, ahead of any mission/purpose framing).
- **Keep the existing conditionals untouched** — `if ($itinerary): … endif;` (`:179`, `:229`) and
  `if ($accomm): … endif;` (`:232`, `:293`) already do exactly what's needed (nothing renders until an owner
  adds a day or a hotel). No change to the `get_posts()`/`meta_query` logic in either block.
- **Remove the Booking.com-rating framing.** The hardcoded note *"All accommodations are hand-picked and
  tested — rated 8.5 or higher on Booking.com. Rooms accommodate 2–4 people."* (`:242–246`) is itself a
  hardcoded string an owner can't edit or turn off today — doubly worth fixing under the governing
  constraint. Replace it with a new optional field, `accomm_note_en/ro` (§3), left blank by default; if an
  owner wants a framing line here, they write it themselves rather than inheriting hotel-rating language a
  developer hardcoded.
- **De-emphasize the hotel-gallery visual treatment.** The current `.nl-accomm-photos` 4-photo mosaic
  (`style.css:808–816`, a fixed 260px-tall 2-column photo grid per card) is the most "travel agency" element
  on the page visually, independent of copy. Replace it with a single, smaller cover photo per card (reuse
  the existing `get_the_post_thumbnail_url()` call already at `:255`, drop the `photo_2/3/4` mosaic slots
  from the card layout — the fields can stay registered in ACF for now, just unused by this card style, so no
  data is destroyed) plus text-forward body content. This keeps accommodation entries informative without
  presenting them as a booking-site gallery.
- **Soften section framing, don't remove it.** Keep the "Day by Day" / "Where You Stay" eyebrows (neutral,
  factual) but consider a shared lead-in sentence introducing both as supporting detail (e.g. *"For those who
  want the full practical picture:"*) — spec's objection is to *leading* with logistics, not to logistics
  existing on the page at all.

CPT registrations (`cpt-registration.php`), the ACF field group in `project-acf-fields.php`, and the wp-admin
menu entries for Itinerary Days / Accommodations are all left fully intact and functional — this is the
entire point of Option B.

---

## 3. Data model

### New ACF fields on `nl_project`

Register these in `shortcodes/project-fields.php` — **not** suffixed `-v6`, since per §7 this file becomes
the single canonical PHP registration for *all* `nl_project` fields, old and new, once the existing DB-only
group is migrated in. Use a **new** field group key (`group_nl_project_v6_new`, or similar) for the fields
below specifically, separate from the migrated group's key (§7) so the two `acf_add_local_field_group()`
calls stay independently editable in code — they can live in the same file. Do not reuse or redeclare any
existing field name (`series`, `edition`, `year`, `status`, `dates_en/ro`, `duration_en/ro`, `price`,
`price_note_en/ro`, `desc_en/ro`, `cover_image`, `destinations`, `register_form_url`) under a new key — all
names below are new, so there is no duplicate-edit-box collision (see §1) regardless.

| Field name | Type | Purpose |
|---|---|---|
| `group_size` | text | e.g. `"20 participants"`. Rendered in hero meta row in place of price. |
| `about_en` / `about_ro` | textarea | About the Project body copy (philosophy-first framing). Reuse `nl_format_desc()` from `shortcodes/projects.php:5` for bullet-friendly rendering, same as `desc_en/ro` already does. |
| `purpose_intro_en` / `purpose_intro_ro` | textarea | Purpose section intro sentence — owner-editable, not static (see below). |
| `purpose_1_icon` … `purpose_4_icon` | text | Icon glyph/emoji for each of the 4 Purpose cards, same convention as `nl_why_item`'s `icon` field (`shortcodes/why.php:18`, rendered as plain text/emoji, not an image field). |
| `purpose_1_title_en/ro` … `purpose_4_title_en/ro` | text | Card title, e.g. "Cultural Exchange" — owner-editable per volume. |
| `purpose_1_desc_en/ro` … `purpose_4_desc_en/ro` | textarea | Card description. |
| `experience_intro_en` / `experience_intro_ro` | textarea | The Experience section intro sentence — owner-editable, not static. |
| `support_intro_en` / `support_intro_ro` | textarea | 1–2 sentence intro for Project Support section. |
| `support_items_en` / `support_items_ro` | textarea | Line-delimited list (`- ` prefix per line, same convention `nl_format_desc()` already parses into `<ul>`) — flights, accommodation, transport, transfers, coordinators, breakfast, activities. |
| `accomm_note_en` / `accomm_note_ro` | textarea, not required | Optional framing line above the (relocated) accommodations grid — replaces the hardcoded Booking.com-rating sentence (§2), left blank unless an owner fills it in. |
| `participation_note_en` / `participation_note_ro` | textarea | "What the contribution covers" explanation. *(Do not reuse `price_note_en/ro` — that field is still needed for Register Now cards on `/projects` and is written in a different voice ("Flights included · 13 nights…"); repurposing it would change copy on the projects listing too.)* |
| `presentation_pdf_url` | url, not required | Link to the Project Presentation PDF, referenced from Participation Contribution section per spec §6. |
| `audience_intro_en` / `audience_intro_ro` | textarea | "Who Is This For" section intro sentence — owner-editable, not static. |
| `audience_items_en` / `audience_items_ro` | textarea | Line-delimited "Who Is This For" characteristics, same bullet convention. |
| `cta_heading_en` / `cta_heading_ro` | text | Final CTA heading (spec wants this distinct/emotional per project, not the generic site CTA). |
| `cta_body_en` / `cta_body_ro` | textarea | Final CTA supporting copy. |

`price` (existing) is kept and reused, just moved out of the hero into the new Participation Contribution
section only (§4.6) — no schema change needed there.

Every field above is deliberately a **field**, not template copy, per the governing admin-editability
constraint — including the four section intro sentences (Purpose, Experience, Support already had one,
Audience), which an earlier draft of this plan left as hardcoded boilerplate. If a section reads acceptably
with an empty intro, wrap its output in the same `if ($field): … endif;` pattern used everywhere else in
this template rather than falling back to hardcoded text.

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
the path of least surprise for whoever maintains this next, and it's fully wp-admin editable (owners add/
remove/reorder destination posts exactly like they already add itinerary days). Register it in
`cpt-registration.php` (add one row to the `$cpts` array, `null` slug since it's not publicly routed, same as
`nl_itinerary_day`) and add its field group to `project-acf-fields.php` (same file, since it already owns
this exact pattern) rather than `project-fields.php` (which is for `nl_project`-level fields only).

### Purpose section — project-scoped numbered fields, not a sitewide CPT

The four Purpose cards must be wp-admin-editable per the governing constraint (§0) — an earlier draft of this
plan hardcoded them as static template markup, which is now rejected for the same reason §2's Option A was
rejected: it would silently remove an owner's ability to change this content without a developer.

Two owner-editable mechanisms were weighed:

- **Sitewide CPT** (extend `nl_why_item`, or a new `nl_purpose_item` CPT without a `project` link): one set of
  four cards edited once, shown identically on every project page. Simplest to build, but means Vol. 6 and
  Vol. 7 can never have different Purpose framing even if a future project's outcomes genuinely differ (e.g.
  a volunteering-heavy edition vs. a homestay-heavy one) — and it's the same "different content model wearing
  the same CPT" trap flagged for `nl_why_item` in §1.
- **Project-scoped numbered fields on `nl_project`** (`purpose_1_icon/title_en/ro/desc_en/ro` … `purpose_4_*`,
  §3 table above): edited per project, on the same edit screen as the rest of that project's content.

**Recommendation: project-scoped numbered fields, not a sitewide CPT.** The deciding factor is the same one
used for Experience vs. Purpose in this plan generally: Purpose's cardinality is fixed at exactly 4 by the
spec's own suggested format ("four key outcomes"), so — unlike Experience's destination count, which
genuinely varies and justifies a child CPT — there's no open-ended list to manage, and numbered fields avoid
the overhead of a new CPT (extra admin-menu entry, a `post_object` link a human must remember to set,
`display_order` bookkeeping) for something that will never need reordering or an arbitrary count. Keeping it
project-scoped (on `nl_project` itself, not sitewide) preserves the ability for a future volume's Purpose
copy to diverge from Vol. 6's, which a sitewide CPT would foreclose.

---

## 4. Section-by-section implementation

All new sections go inside `<div class="nl-project-detail">…</div>` in `single-nl_project.php`. Keep the
existing page-header block (`:60–176`) as the skeleton for Hero but edit its contents per §4.1. The existing
itinerary block (`:178–229`) and accommodations block (`:231–293`) are **not deleted** — they're relocated
(unchanged data logic, restyled per §2) to sit between §4.5 and §4.6 below. Keep the back-CTA block
(`:295–303`) or fold it into the new Final CTA (§4.8).

Full section order (top to bottom): Hero (4.1) → About (4.2) → Purpose (4.3) → The Experience (4.4) →
Project Support (4.5) → **Day-by-Day Itinerary + Accommodations** (4.5a, relocated/restyled, §2) →
Participation Contribution (4.6) → Who Is This For (4.7) → Final CTA (4.8).

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
- Eyebrow "Purpose" / section title. Intro sentence: `purpose_intro_en/ro` (§3), rendered `if ($purpose_intro_en)`.
- 4-item icon-card grid, **field-driven** per §3: loop `purpose_1`…`purpose_4`, reading `_icon`, `_title_en/ro`,
  `_desc_en/ro` for each (a plain PHP loop over `[1,2,3,4]` building field names like `"purpose_{$i}_icon"` is
  simplest — skip an index entirely if its title is empty, so an owner can leave unused slots blank rather
  than being forced to fill all 4). Each rendered exactly like `nl_why_item`'s markup in
  `shortcodes/why.php:23–27` (icon, title, desc), just sourced from `nl_project` fields instead of a separate
  CPT's posts.
- CSS: reuse `.nl-why-grid`/`.nl-why-item`/`.nl-why-icon`/`.nl-why-title`/`.nl-why-desc` (`style.css:361–370`)
  directly — visually identical pattern (icon + title + short desc, 3-col responsive grid). Since this is 4
  items not 3, either add a `.nl-why-grid--4` modifier (`grid-template-columns: repeat(4, 1fr)`, collapsing to
  2 at the existing 960px breakpoint, 1 at 540px — see `:691–692` for the existing responsive precedent) or
  reuse `.nl-why-grid` unmodified at 3 columns (4 items will wrap to a 3+1 layout, acceptable). Recommend the
  modifier for a cleaner 2×2 tablet layout.

### 4.4 The Experience (new section)
- Eyebrow "The Experience" / title. Intro sentence: `experience_intro_en/ro` (§3), rendered `if
  ($experience_intro_en)`.
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
  same helper as `desc_en`, no new parsing code needed. This is the high-level summary (flights, transport,
  coordinators, breakfast) — the detailed, day-by-day and hotel-by-hotel version immediately follows in 4.5a
  for anyone who wants it.
- CSS: reuse `.nl-detail-section`; the `<ul>` from `nl_format_desc()` needs a checklist-style treatment —
  add a small new rule (e.g. `.nl-support-list li { padding-left: 28px; position: relative; }` with a check
  SVG or `::before` bullet in `--terra`) since no existing list-styling class fits (the itinerary/accommodation
  descriptions never render bulleted lists in production content today).

### 4.5a Day-by-Day Itinerary + Accommodations (relocated, restyled — §2 Option B)
- Move `single-nl_project.php:178–229` (itinerary) and `:231–293` (accommodations) here, immediately after
  Project Support and before Participation Contribution. Data-fetching logic (`:38–56`), the two
  `if ($itinerary): … endif;` / `if ($accomm): … endif;` conditionals, and `nlToggleDay()` are unchanged.
- Accommodation card changes only (§2): drop the `.nl-accomm-photos` 4-photo mosaic in favor of a single
  cover photo (existing `get_the_post_thumbnail_url($acc->ID, 'large')` call, `:255`) plus text-forward body;
  render the new `accomm_note_en/ro` field (§3) in place of the deleted hardcoded Booking.com-rating string
  (`:242–246`), wrapped in `if ($accomm_note_en): … endif;` since it's optional.
- Itinerary accordion markup/behavior otherwise unchanged (still collapsed by default via `aria-expanded="false"`,
  `:199`).
- Consider one shared lead-in line above both (e.g. *"For those who want the full practical picture:"*) to
  visually mark this as supporting detail rather than a new top-level narrative beat — optional polish, not a
  new field.

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
- Intro sentence: `audience_intro_en/ro` (§3), rendered `if ($audience_intro_en)`.
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
- Accommodation card restyle (§4.5a/§2): replace/override `.nl-accomm-photos`'s 4-photo mosaic
  (`:808–816`) with a single-photo variant — either edit the existing rule directly (since this is the only
  consumer of `.nl-accomm-card` in the codebase) or add `.nl-accomm-photos--single` and switch the markup to
  use it. Simplest: edit in place, since keeping the old 4-photo grid rule around unused would be dead CSS.
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
   this redesign — this plan's entire new field set (§3: `about_en/ro`, `purpose_intro_en/ro` +
   `purpose_1..4_*`, `experience_intro_en/ro`, `support_intro_en/ro`, `support_items_en/ro`,
   `accomm_note_en/ro`, `participation_note_en/ro`, `presentation_pdf_url`, `audience_intro_en/ro`,
   `audience_items_en/ro`, `cta_heading_en/ro`, `cta_body_en/ro`, `group_size`) and every
   `nl_experience_stop` post start from zero, plus real `nl_itinerary_day`/`nl_accommodation` entries if
   §4.5a's relocated sections are to show anything on Vol. 6.
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
   a. **Migrate the existing DB-only `nl_project` ACF field group into PHP first, safely** (see the dedicated
      procedure below) — this now precedes everything else because it's a prerequisite for the
      admin-editability guarantee this whole plan depends on, not just a nice-to-have.
   b. Register the new fields — add a second `acf_add_local_field_group()` call in the same
      `shortcodes/project-fields.php` file for the genuinely new `nl_project` fields (§3), and the
      `nl_experience_stop` CPT + field group in `cpt-registration.php` / `project-acf-fields.php`. Verify in
      wp-admin that no duplicate field boxes appear anywhere on the `nl_project` edit screen (confirms both
      the migration and the new fields avoided the name/key collision described in §1).
   c. **Delete the two orphaned test posts** — `nl_itinerary_day` #267 ("Test itinerary Day 1") and
      `nl_accommodation` #268 ("Accommodation Test 1"), both linked to the deleted project #257 (§0). They
      match no current project and will only clutter the Itinerary Days / Accommodations admin list screens
      once owners start using them for real. Delete via wp-admin (Itinerary Days → Trash, Accommodations →
      Trash) rather than direct DB surgery, to go through WordPress's normal post-deletion hooks.
   d. Populate placeholder/lorem content on Vol. 6 for every new field, including one real `nl_itinerary_day`
      and one real `nl_accommodation` linked to Vol. 6 (mirrors how Team/Testimonials already carry lorem
      ipsum per `HANDOFF.md`) — needed to visually verify both the new sections and the relocated/restyled
      itinerary+accommodation sections before real copy exists.
   e. Rewrite `single-nl_project.php` per §4 — hero edits, relocate + restyle itinerary/accommodation blocks,
      add the new sections in the order given in §4.
   f. Add CSS per §5.
   g. Verify on `http://nonla-local.local/project/vietnam-escape-vol-6/` (no injection step required, per the
      confirmed architecture fact above).
   h. Verify on `http://nonla-local.local/project/vietnam-escape-vol-5/` — see backward-compatibility note
      below.

2. **Safe migration procedure for the existing DB-only `nl_project` fields (step 1a):**
   - In wp-admin, go to Custom Fields → the `nl_project` field group → **Tools → Export → Generate PHP**.
     This produces an `acf_add_local_field_group([...])` call using the group's **actual existing `key`**
     (e.g. `group_5f...`) and each field's actual existing `key` — copy these verbatim, do not regenerate or
     rename them.
   - Paste that generated code into `shortcodes/project-fields.php` as its own `acf_add_local_field_group()`
     call (alongside, not merged with, the new-fields group from step 1b — two separate calls in one file is
     fine and keeps the diff between "migrated" and "new" easy to review).
   - ACF resolves same-`key` collisions by letting the **local (PHP) definition take precedence** over the
     DB-stored one — this is the intended, documented mechanism for moving a field group from the database
     into code, not a special trick. Reload the `nl_project` edit screen and confirm: same fields, same
     order, same labels, and — critically — **existing data still populates** (postmeta is keyed by field
     `name`, which doesn't change, so no data migration is needed, only the field *definition*).
   - Only after confirming the PHP version renders identically and data round-trips correctly: delete the
     original DB-stored field-group post (Custom Fields → the group → Move to Trash). This does **not**
     delete any `nl_project` postmeta — field-group posts only store the field *definitions* (labels, types,
     validation), never the answers. Deleting it removes the now-redundant DB copy so there's exactly one
     source of truth going forward, matching the governing constraint's spirit (owners should never be one
     bad DB import away from losing the ability to edit `series`/`price`/`dates_en`/etc.).
   - If there's no time to do this properly before the rest of the build, at minimum **export and commit the
     PHP** without deleting the DB group yet — a redundant-but-present PHP copy is a strict improvement over
     today's DB-only state, since it becomes the disaster-recovery source even if the DB group is still live.

3. **What breaks the existing Vol. 5 page:** Vol. 5 has none of the new fields populated (all new ACF fields
   will read empty via `get_field()`). Every new section in §4 must be wrapped in the same
   `if ($field_value): … endif;` conditional style already used throughout the current template (e.g. `:94`,
   `:104`, `:114`, `:128`) so sections silently don't render rather than showing empty headers/broken layout.
   The Hero's meta row (§4.1) needs the same treatment: if `group_size` is empty, drop that meta item, don't
   render a blank slot. **Concretely: do not assume every project will have every new field populated — Vol.
   5 won't, until someone backfills it, and it should degrade gracefully, not break.** This includes the
   relocated itinerary/accommodation sections (§4.5a) — Vol. 5 currently has neither, so both stay hidden
   there too until an owner adds them, which is the whole point of keeping the existing conditionals intact.

4. **Verification checklist:**
   - Both EN and RO render correctly for every new string (toggle via the nav's EN/RO buttons, confirm no
     English leaks into RO mode and vice versa) — the most common mistake with this pattern is forgetting the
     `data-ro` attribute and leaving the RO fallback equal to EN, which is acceptable per CLAUDE.md's own
     fallback convention but should be a deliberate choice, not an oversight.
   - `nl-reveal` scroll animations fire on all new sections (class already used throughout; just confirm it's
     added to the new section wrappers).
   - Mobile breakpoints (960px, 540px) checked for the new grids specifically — Experience (4→2→1) and Purpose
     (4→2→1) are new grid configurations not covered by existing breakpoint rules until §5's additions land.
   - Confirm no duplicate "Project" field boxes appear in wp-admin's `nl_project` editor after both the
     migration (step 2) and the new-fields registration (step 1b) — validates the no-name-collision claim in
     §1/§3 for both.
   - In wp-admin, as a non-technical owner would: add a second `nl_itinerary_day` and a second
     `nl_accommodation` to Vol. 6, confirm they appear on the front end in the correct order without a
     developer touching code — this is the actual acceptance test for the governing constraint, not just "the
     page looks right."

5. **`HANDOFF.md` backlog cross-reference:** This plan does not address "Team bios/photos" or "Testimonials"
   — out of scope here. "Remove Vol. 100" is effectively addressed by step 1c above (the orphaned test posts
   are deleted) though the Vol. 6 post itself, which §0 suggests may be that same repurposed test project, is
   left in place and built out rather than deleted — confirm this reading is correct (§8). It also does not
   populate Vol. 5's itinerary/accommodation data — that remains a content task for an owner to do in
   wp-admin whenever they're ready, which is now possible again since §2 keeps the rendering path.

---

## 8. Out of scope / open questions for the user

1. **Confirm the Vol. 6 test project's identity.** Is `vietnam-escape-vol-6` the repurposed "Vol. 100" test
   project from the `HANDOFF.md` backlog, or a separate placeholder someone created? This plan assumes Opus
   builds/verifies directly on this existing post rather than creating a fresh one — confirm that's correct.
2. ~~§2 decision (retire vs. keep itinerary/accommodation)~~ — **resolved by the governing admin-editability
   constraint**: Option B (keep, relocate, restyle) is mandatory, not optional. No longer an open question.
3. **Full ACF migration to PHP — recommendation given, but confirm scope/timeline.** §7 now specifies this as
   part of the task (step 1a), using the safe same-`key` export procedure, because the governing constraint
   makes it a functional prerequisite rather than background cleanup. This is real added scope beyond what
   "redesign the project page" would otherwise imply — worth confirming the user wants it bundled into this
   piece of work versus split into its own ticket, given it touches every `nl_project` field, not just the
   ones this redesign adds.
4. **Photography sourcing** (§6.1) — who is producing/selecting the people-first photography, and by when?
   This blocks a faithful implementation of Hero/About/Experience regardless of how well the code is built.
5. **Copy document** (§6.5) — the "Final copy document" the spec references as a supporting asset was not
   supplied to this planning task. Confirm whether Opus should draft placeholder EN/RO copy from the spec's
   own example bullets (acceptable per the site's existing lorem-ipsum precedent) or wait for real copy.
6. ~~Purpose section data model~~ — **resolved**: project-scoped numbered fields on `nl_project` (§3), not
   static markup and not a sitewide CPT. No longer an open question, but flagging that this is 4×5 = 20 new
   field rows (icon + title_en + title_ro + desc_en + desc_ro, ×4) on an edit screen that's about to also
   gain ~15 more fields from §3's table plus the migrated legacy group (step 2 above) — worth a quick check
   with the user that a long `nl_project` edit screen is acceptable, or whether ACF free's field-group
   `position`/tabs (if available) should be used to organize it.
7. **`nl_experience_stop` vs. numbered fields** (§3) — this plan recommends the child-CPT approach. If the
   user wants to minimize new CPTs/admin-menu clutter over long-term flexibility, numbered fields on
   `nl_project` are the documented fallback — confirm preference.
8. **Vol. 6 launch readiness** is gated on §6 (content gaps) regardless of how well this plan's code is
   executed — worth setting expectations that "page redesign done" and "Vol. 6 page launch-ready" are
   different milestones.
