---
name: nonla-content-copy
description: Write or edit copy for the Nonla Escape NGO website in its brand voice, in English and Romanian. Use for headlines, section text, project and trip descriptions, team bios, testimonials, place blurbs, FAQ, meta descriptions, and calls to action for joining a programme or trip, volunteering, partnering or sponsoring, and donating. Also use when asked to translate site text EN/RO or to make the site more engaging.
---

# Nonla copy

Read the current copy before writing: `app/public/sections/*.php`, `app/public/pages/*.php`, `app/public/wp-content/themes/nonla-theme/footer.php`. Match it.

## Who is speaking

Asociația Nonla Cultural Escape, a Romanian NGO that runs small-group cultural trips to Vietnam, led by coordinators who speak Romanian, English and Vietnamese. Readers are mostly Romanian adults considering a first trip to Asia, plus possible partners and supporters.

Facts that are in the code today (do not alter; do not add others without a source from the user):

- Legal name: Asociația Nonla Cultural Escape. CIF 49845083. Câmpulung Muscel, Argeș County, Romania.
- Contact: nonlaescape@gmail.com. Instagram and Facebook: `nonlaescape`.
- Series: Vietnam Escape (annual group trip), Vietnam Undiscovered (off the beaten path).
- Claims on the site: "Since 2020", "5+ years", "5 editions", "100+ travelers".

Never invent numbers, prices, dates, impact figures, partner names, awards, quotes or testimonials. If a fact is missing, write `[TO CONFIRM: ...]` and ask. Team bios and testimonials on the site are still lorem ipsum: real ones must come from the people themselves.

## Voice

- Warm, direct, second person. An invitation from someone who will be on the trip with you, not a brochure.
- Headlines are two short lines, the second in italics and often a turn: "More Than / *a Trip.*", "Places That Stay / *With You.*", "We Travel / *With You.*".
- Eyebrows are 2 to 4 words: "Who We Are", "Where We Go", "Ready to Escape?".
- Body text: one or two sentences. Concrete and sensory (limestone karsts, coffee workshops, rice paddies) over abstract ("unforgettable experiences").
- Recurring ideas: connection, small group, real Vietnam, everything handled, coordinators as companions.
- "Escape" is the brand word for a trip. Use it as a noun.
- Avoid: hard sell, urgency tricks, pity or saviour framing, "exotic", "hidden gem" piles, exclamation marks, emoji in body text.
- Vietnamese people and places are hosts and partners, never scenery. Spell place names as the site does (Hanoi, Ninh Binh, Phu Quoc, Ho Chi Minh City).

Romanian: informal singular "tu" ("Găsește-o pe cea care te cheamă"). Write natural Romanian, not a word-for-word translation ("Gata să Evadezi?" for "Ready to Escape?"). Correct diacritics: ă â î ș ț (comma below, not cedilla). Headline words are capitalised on the site in both languages.

## Calls to action

Today the site has three: see the projects, register through a Google Form (Nonla Settings → Registration Form URL, or per project), and email. There is no volunteer, partner or donation flow yet. Before writing one, ask the user which is really open and where the button should lead. Do not ship a button without a destination.

| Goal | Button (EN / RO) | Supporting line should say |
|---|---|---|
| Join a trip or programme | Register Now / Înregistrează-te · See Our Escapes / Vezi Călătoriile Noastre | dates, group size, what is included, who travels with you |
| Volunteer | Volunteer With Us / Fă Voluntariat cu Noi | the actual role, time needed, where (Romania or Vietnam), what the volunteer gains |
| Partner or sponsor | Partner With Us / Devino Partener | who is a fit (schools, companies, cultural bodies), one concrete form of partnership, a named contact |
| Donate | Support Nonla / Susține Nonla | exactly what the money pays for, the legal entity and CIF, how to give |

For donations in Romania two routes are common: redirecting 3.5% of income tax with Form 230, and company sponsorship. Offer them as options only, marked `[TO CONFIRM with the NGO's accountant]`. Eligibility and wording are legal matters.

One primary action per block. The label says what happens next. Put the reassurance (no payment now, reply within a few days) beside it only if it is true.

## Delivering copy

Text is stored in two places, and the format differs.

- **In code** (`sections/`, `pages/`, theme files): give ready-to-paste markup with both languages.
  ```html
  <span class="t" data-en="Partner With Us" data-ro="Devino Partener">Partner With Us</span>
  ```
  Escape `"` inside attributes as `&quot;`. Say which file it belongs in. Changes in `sections/` or `pages/` need inject + flush (see `nonla-dev-loop`).
- **In WP Admin fields** (projects, team, places, testimonials): give plain text per field, labelled with the field name and language, for staff to paste into production WP Admin. See `app/public/docs/content-editing.md` for the fields. Project descriptions are plain text: blank line between paragraphs, `- ` for bullets. Testimonials have one Quote field, so keep the traveller's own language.

Always give EN and RO together, and list every `[TO CONFIRM]` at the end. Do not type content into the local database: it never reaches the live site.
