# Editing content (for NGO staff)

All content is edited on the live site: **https://nonlaescape.com/wp-admin**. Changes appear straight away. Nothing needs to be "deployed", and nothing should be typed into the developer's local copy.

Field names below were read from the local copy. If production shows something different, tell the developer.

## General rules

- **Title** is the box at the top of each entry. What it is used for differs per type (see below).
- **Display Order** is a number. Lower numbers come first. Always fill it in.
- Fields marked **EN** and **RO** hold the English and Romanian versions. If RO is left empty the site shows the English text to Romanian visitors.
- Resize photos before uploading: about 2000 px on the long side, under 500 KB, JPG.
- After saving, open the public page and check it in both EN and RO.

## Projects (trips)

Sidebar → **Projects**. Each project gets a card on `/projects/` and its own page at `/project/<title-as-slug>/`.

| Field | Notes |
|---|---|
| Title | Used for the admin list and the page address, for example "Vietnam Escape Vol. 6". The card itself shows Series + Edition + Year |
| Series | Vietnam Escape or Vietnam Undiscovered |
| Edition, Year | For example `Vol. 6`, `2027` |
| Status | **Upcoming** or **Completed**. Change it to Completed when the trip is over |
| Dates (EN / RO) | Write EN as `October 14 – 29, 2026` (month, day, year). The home page countdown reads the start date from this text |
| Duration (EN / RO), Price, Price Note (EN / RO) | Free text |
| Destinations | Separated by commas. Each becomes a pill |
| Cover Image | Card photo |
| Description (EN / RO) | Plain text. Leave an empty line between paragraphs. Lines that start with `- ` become a bullet list |
| Register Form URL | Optional. If empty, the button uses the form from Nonla Settings |

How Status changes the card: Upcoming shows **Register Now**. Completed shows **Register for Next Edition** when another upcoming project exists, otherwise Instagram and Facebook buttons. Upcoming projects are listed first. The countdown shows only while an Upcoming project has a start date in the future.

### Itinerary Days and Accommodations

These appear on the project's own page. Create them under **Itinerary Days** and **Accommodations**, and pick the project in the **Project** field.

- **Itinerary Day:** Title = day title in English. Day # (0 = departure day), Date (`14 April`), Title (RO), Description (EN / RO).
- **Accommodation:** Title = hotel name. Featured Image = main photo. City, Display Order, Description (EN / RO), Booking.com URL, Photo 2 to 4.

## Places

Sidebar → **Places**. Title = place name. Photo, Tag (city or region), Description (one line), Display Order, Hover Details (EN / RO, one or two sentences shown on hover or tap).

## Team Members

Sidebar → **Team Members**. Title = full name. Role (EN / RO), City, Bio (EN / RO), Photo, Email, Display Order.

Email is optional and is shown publicly on the Team page when filled in.

## Testimonials

Sidebar → **Testimonials**. Title = a label for the admin list. Quote, Author Name, Author Role (for example `Cluj-Napoca, 2026`), Author Photo, Display Order.

There is one Quote field, not an EN and RO pair, so the quote shows as written in both languages. Publish a name, photo or quote only with that person's agreement.

## Gallery

Sidebar → **Gallery**. Photo, Caption (also used as the photo's description for screen readers), Display Order. The item with the lowest Display Order is the large photo.

## Series and Why Items

Rarely change. **Series:** Title, Label, Description, Link URL, Display Order. **Why Items:** Title, Icon (an emoji), Description, Display Order.

## Nonla Settings

Settings → **Nonla Settings**.

- **Registration Form URL:** where "Register Now" goes when a project has no form of its own.
- **Contact Email:** shown in the footer and on the Team page. The email in the home page "Ready to Escape?" block and on the Legal page changes only after the developer re-runs the page build, so tell the developer when you change it.

## What cannot be changed here

The hero headline, "Who We Are" text, section headings, the menu, the footer and the Legal text are part of the site's code. Ask the developer.

Please do not:

- open Home, Projects, Team, Gallery or Legal with **Edit with Elementor**. Changes made there are overwritten the next time the developer updates the layout;
- use Appearance → Theme File Editor;
- import anything with All-in-One WP Migration. Export (a backup) is fine.

Posts and Comments are hidden from the sidebar on purpose.

If a change does not show on the public site, try a private browser window first. If it is still missing, tell the developer (a cache may need clearing).
