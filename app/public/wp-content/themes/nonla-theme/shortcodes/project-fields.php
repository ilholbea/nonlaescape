<?php
// Narrative ACF fields for the nl_project detail page (the "adjustment" redesign).
//
// These are the EMPTY field definitions only — all content is entered per project
// from wp-admin. They live in PHP (not the ACF DB UI) purely so the field STRUCTURE
// travels with the theme on deploy; the itinerary/accommodation fields in
// project-acf-fields.php follow the same convention.
//
// This file deliberately does NOT touch the existing DB-registered nl_project fields
// (series, edition, year, status, dates_en/ro, duration_en/ro, price, price_note_en/ro,
// desc_en/ro, cover_image, destinations, register_form_url). Every field name below is
// new, so there is no key/name collision with that group. Free ACF field types only.

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    // Small builders to keep 40 fields readable and consistent.
    $tab = fn($key, $label) => ['key' => "field_nl_p_tab_$key", 'label' => $label, 'type' => 'tab', 'placement' => 'top'];
    $text = fn($name, $label, $width = 100, $extra = []) => array_merge([
        'key' => "field_nl_p_$name", 'label' => $label, 'name' => $name, 'type' => 'text',
        'wrapper' => ['width' => (string) $width],
    ], $extra);
    $area = fn($name, $label, $width = 100, $rows = 4, $extra = []) => array_merge([
        'key' => "field_nl_p_$name", 'label' => $label, 'name' => $name, 'type' => 'textarea',
        'rows' => $rows, 'wrapper' => ['width' => (string) $width],
    ], $extra);
    // An EN/RO textarea pair rendered side by side.
    $pair = fn($base, $label, $rows = 4, $extra = []) => [
        $area("{$base}_en", "$label (EN)", 50, $rows, $extra),
        $area("{$base}_ro", "$label (RO)", 50, $rows, $extra),
    ];

    $fields = [];

    // ── Hero ────────────────────────────────────────────────────────────────
    $fields[] = $tab('hero', 'Hero');
    $fields[] = $text('group_size', 'Group Size', 100, [
        'placeholder'  => '20 participants',
        'instructions' => 'Shown in the hero meta row (Duration · Location · Group Size). Price is intentionally not shown in the hero.',
    ]);

    // ── About & Purpose ───────────────────────────────────────────────────────
    $fields[] = $tab('about', 'About & Purpose');
    $fields = array_merge($fields, $pair('about', 'About the Project', 5, [
        'instructions' => 'Philosophy-first framing. Use blank lines for paragraphs and "- " lines for bullets.',
    ]));
    $fields = array_merge($fields, $pair('purpose_intro', 'Purpose — Intro sentence', 3));
    for ($i = 1; $i <= 4; $i++) {
        $fields[] = ['key' => "field_nl_p_tab_purpose_$i", 'label' => "Purpose card $i", 'type' => 'message',
            'message' => "— Purpose card $i (leave the title blank to hide this card) —"];
        $fields[] = $text("purpose_{$i}_icon", "Card $i · Icon", 20, ['placeholder' => '🌏', 'instructions' => 'Emoji or glyph.']);
        $fields[] = $text("purpose_{$i}_title_en", "Card $i · Title (EN)", 40);
        $fields[] = $text("purpose_{$i}_title_ro", "Card $i · Title (RO)", 40);
        $fields[] = $area("purpose_{$i}_desc_en", "Card $i · Description (EN)", 50, 3);
        $fields[] = $area("purpose_{$i}_desc_ro", "Card $i · Description (RO)", 50, 3);
    }

    // ── Experience & Support ──────────────────────────────────────────────────
    $fields[] = $tab('exp', 'Experience & Support');
    $fields = array_merge($fields, $pair('experience_intro', 'The Experience — Intro sentence', 3, [
        'instructions' => 'Destination cards themselves are managed under "Experience Stops".',
    ]));
    $fields = array_merge($fields, $pair('support_intro', 'Project Support — Intro sentence', 3));
    $fields = array_merge($fields, $pair('support_items', 'Project Support — Items', 6, [
        'instructions' => 'One item per line, each prefixed with "- " (e.g. "- Domestic flights"). Rendered as a checklist.',
    ]));
    $fields = array_merge($fields, $pair('accomm_note', 'Accommodations — Optional intro line', 3, [
        'instructions' => 'Optional framing line above the accommodations grid. Leave blank to hide it.',
    ]));

    // ── Participation & Audience ──────────────────────────────────────────────
    $fields[] = $tab('participation', 'Participation & Audience');
    $fields = array_merge($fields, $pair('participation_note', 'Participation Contribution — Note', 4, [
        'instructions' => 'What the contribution covers. The amount itself reuses the existing Price field.',
    ]));
    $fields[] = [
        'key' => 'field_nl_p_presentation_pdf_url', 'label' => 'Project Presentation PDF', 'name' => 'presentation_pdf_url',
        'type' => 'url', 'wrapper' => ['width' => '100'],
        'instructions' => 'Optional. Shown as a download link in the Participation Contribution section.',
    ];
    $fields = array_merge($fields, $pair('audience_intro', 'Who Is This For — Intro sentence', 3));
    $fields = array_merge($fields, $pair('audience_items', 'Who Is This For — Items', 6, [
        'instructions' => 'One characteristic per line, each prefixed with "- ".',
    ]));

    // ── Final CTA ─────────────────────────────────────────────────────────────
    $fields[] = $tab('cta', 'Final CTA');
    $fields[] = $text('cta_heading_en', 'CTA Heading (EN)', 50);
    $fields[] = $text('cta_heading_ro', 'CTA Heading (RO)', 50);
    $fields = array_merge($fields, $pair('cta_body', 'CTA Body', 4));

    acf_add_local_field_group([
        'key'      => 'group_nl_project_narrative',
        'title'    => 'Project Page — Narrative Sections',
        'fields'   => $fields,
        'location' => [[
            ['param' => 'post_type', 'operator' => '==', 'value' => 'nl_project'],
        ]],
        'menu_order' => 5,
        'position'   => 'normal',
    ]);
});
