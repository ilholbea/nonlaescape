<?php
/**
 * Migrate ACF local field groups to the database.
 * Run via: wp eval-file migrate-acf-to-db.php
 *
 * After running, all field groups appear in WP Admin → ACF → Field Groups.
 * Remove acf_add_local_field_group() calls from inc/acf-fields.php once verified.
 */

if (!function_exists('acf_import_field_group')) {
    WP_CLI::error('ACF is not active. Activate Advanced Custom Fields and try again.');
    return;
}

$groups = [

    // ── PLACES (front page repeater) ────────────────────────────────
    [
        'key'        => 'group_nl_places',
        'title'      => 'Places That Stay With You',
        'location'   => [[['param' => 'page_type', 'operator' => '==', 'value' => 'front_page']]],
        'menu_order' => 30,
        'fields'     => [
            [
                'key'           => 'field_nl_places_max',
                'label'         => 'Number of Places to Show',
                'name'          => 'places_max_items',
                'type'          => 'number',
                'default_value' => 4,
                'min'           => 1,
                'max'           => 12,
                'instructions'  => 'How many place cards to display on the homepage.',
            ],
            [
                'key'          => 'field_nl_places_items',
                'label'        => 'Places',
                'name'         => 'places_items',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add Place',
                'sub_fields'   => [
                    ['key' => 'field_nl_place_image', 'label' => 'Photo',             'name' => 'image',       'type' => 'image',   'return_format' => 'url', 'preview_size' => 'medium', 'column_width' => 20],
                    ['key' => 'field_nl_place_tag',   'label' => 'Tag (city/region)', 'name' => 'tag',         'type' => 'text',    'placeholder' => 'e.g. Hanoi',        'column_width' => 20],
                    ['key' => 'field_nl_place_name',  'label' => 'Place Name',        'name' => 'name',        'type' => 'text',    'placeholder' => 'e.g. Old Quarter',  'column_width' => 20],
                    ['key' => 'field_nl_place_desc',  'label' => 'Short Description', 'name' => 'description', 'type' => 'text',    'placeholder' => 'e.g. Ancient streets, street food & coffee', 'column_width' => 40],
                ],
            ],
        ],
    ],

    // ── HERO (front page) ───────────────────────────────────────────
    [
        'key'        => 'group_nl_hero',
        'title'      => 'Hero Section',
        'location'   => [[['param' => 'page_type', 'operator' => '==', 'value' => 'front_page']]],
        'menu_order' => 10,
        'fields'     => [
            ['key' => 'field_nl_hero_eyebrow',   'label' => 'Eyebrow Text',     'name' => 'hero_eyebrow',    'type' => 'text', 'default_value' => 'Escape the ordinary. Discover the unknown.'],
            ['key' => 'field_nl_hero_headline',  'label' => 'Headline',         'name' => 'hero_headline',   'type' => 'text', 'default_value' => 'Cultural Escapes Through Vietnam'],
            ['key' => 'field_nl_hero_subtext',   'label' => 'Subtext',          'name' => 'hero_subtext',    'type' => 'textarea', 'rows' => 3],
            ['key' => 'field_nl_hero_bg_image',  'label' => 'Background Image', 'name' => 'hero_bg_image',   'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
            ['key' => 'field_nl_hero_btn1_text', 'label' => 'Button 1 Text',    'name' => 'hero_btn1_text',  'type' => 'text', 'default_value' => 'See Our Projects'],
            ['key' => 'field_nl_hero_btn1_url',  'label' => 'Button 1 URL',     'name' => 'hero_btn1_url',   'type' => 'url'],
            ['key' => 'field_nl_hero_btn2_text', 'label' => 'Button 2 Text',    'name' => 'hero_btn2_text',  'type' => 'text', 'default_value' => 'Learn About Us'],
            ['key' => 'field_nl_hero_btn2_url',  'label' => 'Button 2 URL',     'name' => 'hero_btn2_url',   'type' => 'url'],
            [
                'key'          => 'field_nl_hero_stats',
                'label'        => 'Stats',
                'name'         => 'hero_stats',
                'type'         => 'repeater',
                'layout'       => 'table',
                'button_label' => 'Add Stat',
                'sub_fields'   => [
                    ['key' => 'field_nl_hero_stat_number', 'label' => 'Number', 'name' => 'number', 'type' => 'text', 'column_width' => 30],
                    ['key' => 'field_nl_hero_stat_label',  'label' => 'Label',  'name' => 'label',  'type' => 'text', 'column_width' => 70],
                ],
            ],
        ],
    ],

    // ── ABOUT (front page) ──────────────────────────────────────────
    [
        'key'        => 'group_nl_about',
        'title'      => 'About Section',
        'location'   => [[['param' => 'page_type', 'operator' => '==', 'value' => 'front_page']]],
        'menu_order' => 20,
        'fields'     => [
            ['key' => 'field_nl_about_eyebrow',    'label' => 'Eyebrow Text',  'name' => 'about_eyebrow',    'type' => 'text', 'default_value' => 'Who We Are'],
            ['key' => 'field_nl_about_heading',    'label' => 'Heading',       'name' => 'about_heading',    'type' => 'text', 'default_value' => 'More Than a Trip.'],
            ['key' => 'field_nl_about_body',       'label' => 'Body',          'name' => 'about_body',       'type' => 'wysiwyg', 'toolbar' => 'basic', 'media_upload' => 0],
            ['key' => 'field_nl_about_quote',      'label' => 'Quote',         'name' => 'about_quote',      'type' => 'textarea', 'rows' => 2],
            ['key' => 'field_nl_about_photo_main', 'label' => 'Main Photo',    'name' => 'about_photo_main', 'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
            ['key' => 'field_nl_about_photo_float','label' => 'Float Photo',   'name' => 'about_photo_float','type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
            ['key' => 'field_nl_about_cta_text',   'label' => 'CTA Text',      'name' => 'about_cta_text',   'type' => 'text'],
            ['key' => 'field_nl_about_cta_url',    'label' => 'CTA URL',       'name' => 'about_cta_url',    'type' => 'url'],
        ],
    ],

    // ── TEAM MEMBER ─────────────────────────────────────────────────
    [
        'key'      => 'group_nl_team_member',
        'title'    => 'Team Member Details',
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'nl_team_member']]],
        'fields'   => [
            ['key' => 'field_nl_tm_role_en',   'label' => 'Role (English)',    'name' => 'role_en',       'type' => 'text'],
            ['key' => 'field_nl_tm_role_ro',   'label' => 'Role (Romanian)',   'name' => 'role_ro',       'type' => 'text'],
            ['key' => 'field_nl_tm_city',      'label' => 'City',              'name' => 'city',          'type' => 'text'],
            ['key' => 'field_nl_tm_bio_en',    'label' => 'Bio (English)',     'name' => 'bio_en',        'type' => 'textarea', 'rows' => 3],
            ['key' => 'field_nl_tm_bio_ro',    'label' => 'Bio (Romanian)',    'name' => 'bio_ro',        'type' => 'textarea', 'rows' => 3],
            ['key' => 'field_nl_tm_photo',     'label' => 'Photo',             'name' => 'photo',         'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
            ['key' => 'field_nl_tm_email',     'label' => 'Email',             'name' => 'email',         'type' => 'email'],
            ['key' => 'field_nl_tm_order',     'label' => 'Display Order',     'name' => 'display_order', 'type' => 'number', 'default_value' => 10],
        ],
    ],

    // ── PROJECT ─────────────────────────────────────────────────────
    [
        'key'      => 'group_nl_project',
        'title'    => 'Project Details',
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'nl_project']]],
        'fields'   => [
            ['key' => 'field_nl_proj_series',            'label' => 'Series',                 'name' => 'series',              'type' => 'select', 'choices' => ['vietnam-escape' => 'Vietnam Escape', 'vietnam-undiscovered' => 'Vietnam Undiscovered'], 'default_value' => 'vietnam-escape'],
            ['key' => 'field_nl_proj_edition',           'label' => 'Edition',                'name' => 'edition',             'type' => 'text', 'placeholder' => 'e.g. Vol. 6'],
            ['key' => 'field_nl_proj_year',              'label' => 'Year',                   'name' => 'year',                'type' => 'text'],
            ['key' => 'field_nl_proj_status',            'label' => 'Status',                 'name' => 'status',              'type' => 'select', 'choices' => ['upcoming' => 'Upcoming', 'completed' => 'Completed'], 'default_value' => 'upcoming'],
            ['key' => 'field_nl_proj_dates_en',          'label' => 'Dates (English)',         'name' => 'dates_en',            'type' => 'text'],
            ['key' => 'field_nl_proj_dates_ro',          'label' => 'Dates (Romanian)',        'name' => 'dates_ro',            'type' => 'text'],
            ['key' => 'field_nl_proj_duration_en',       'label' => 'Duration (English)',      'name' => 'duration_en',         'type' => 'text', 'default_value' => '15 days'],
            ['key' => 'field_nl_proj_duration_ro',       'label' => 'Duration (Romanian)',     'name' => 'duration_ro',         'type' => 'text', 'default_value' => '15 zile'],
            ['key' => 'field_nl_proj_price',             'label' => 'Price',                  'name' => 'price',               'type' => 'text'],
            ['key' => 'field_nl_proj_price_note_en',     'label' => 'Price Note (English)',    'name' => 'price_note_en',       'type' => 'text'],
            ['key' => 'field_nl_proj_price_note_ro',     'label' => 'Price Note (Romanian)',   'name' => 'price_note_ro',       'type' => 'text'],
            ['key' => 'field_nl_proj_destinations',      'label' => 'Destinations',           'name' => 'destinations',        'type' => 'text'],
            ['key' => 'field_nl_proj_cover',             'label' => 'Cover Image',            'name' => 'cover_image',         'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
            ['key' => 'field_nl_proj_desc_en',           'label' => 'Description (English)',  'name' => 'desc_en',             'type' => 'textarea', 'rows' => 4],
            ['key' => 'field_nl_proj_desc_ro',           'label' => 'Description (Romanian)', 'name' => 'desc_ro',             'type' => 'textarea', 'rows' => 4],
            ['key' => 'field_nl_proj_register_form_url', 'label' => 'Register Form URL',      'name' => 'register_form_url',   'type' => 'text'],
        ],
    ],

    // ── SERIES CPT ──────────────────────────────────────────────────
    [
        'key'      => 'group_nl_series',
        'title'    => 'Series Details',
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'nl_series']]],
        'fields'   => [
            ['key' => 'field_nl_series_label',       'label' => 'Label (tag)',    'name' => 'label',         'type' => 'text', 'placeholder' => 'e.g. Annual Group Escape'],
            ['key' => 'field_nl_series_description', 'label' => 'Description',   'name' => 'description',   'type' => 'wysiwyg', 'toolbar' => 'basic', 'media_upload' => 0],
            ['key' => 'field_nl_series_cover',       'label' => 'Cover Image',   'name' => 'cover_image',   'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
            ['key' => 'field_nl_series_link_url',    'label' => 'Link URL',      'name' => 'link_url',      'type' => 'url'],
            ['key' => 'field_nl_series_order',       'label' => 'Display Order', 'name' => 'display_order', 'type' => 'number', 'default_value' => 10],
        ],
    ],

    // ── PLACE CPT ───────────────────────────────────────────────────
    [
        'key'      => 'group_nl_place',
        'title'    => 'Place Details',
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'nl_place']]],
        'fields'   => [
            ['key' => 'field_nl_place_photo',       'label' => 'Photo',             'name' => 'photo',         'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
            ['key' => 'field_nl_place_tag',         'label' => 'Tag (city/region)', 'name' => 'tag',           'type' => 'text', 'placeholder' => 'e.g. Hanoi'],
            ['key' => 'field_nl_place_description', 'label' => 'Description',       'name' => 'description',   'type' => 'text', 'placeholder' => 'e.g. Ancient streets, street food & coffee'],
            ['key' => 'field_nl_place_order',       'label' => 'Display Order',     'name' => 'display_order', 'type' => 'number', 'default_value' => 10],
        ],
    ],

    // ── WHY ITEM CPT ─────────────────────────────────────────────────
    [
        'key'      => 'group_nl_why_item',
        'title'    => 'Why Item Details',
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'nl_why_item']]],
        'fields'   => [
            ['key' => 'field_nl_why_icon',        'label' => 'Icon',           'name' => 'icon',          'type' => 'text', 'placeholder' => 'e.g. 🧭'],
            ['key' => 'field_nl_why_description', 'label' => 'Description',   'name' => 'description',   'type' => 'textarea', 'rows' => 3],
            ['key' => 'field_nl_why_order',       'label' => 'Display Order', 'name' => 'display_order', 'type' => 'number', 'default_value' => 10],
        ],
    ],

    // ── TESTIMONIAL CPT ──────────────────────────────────────────────
    [
        'key'      => 'group_nl_testimonial',
        'title'    => 'Testimonial Details',
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'nl_testimonial']]],
        'fields'   => [
            ['key' => 'field_nl_testi_quote',        'label' => 'Quote',         'name' => 'quote',         'type' => 'wysiwyg', 'toolbar' => 'basic', 'media_upload' => 0],
            ['key' => 'field_nl_testi_author_name',  'label' => 'Author Name',   'name' => 'author_name',   'type' => 'text'],
            ['key' => 'field_nl_testi_author_role',  'label' => 'Author Role',   'name' => 'author_role',   'type' => 'text', 'placeholder' => 'e.g. Romania, 2024'],
            ['key' => 'field_nl_testi_author_photo', 'label' => 'Author Photo',  'name' => 'author_photo',  'type' => 'image', 'return_format' => 'url', 'preview_size' => 'thumbnail'],
            ['key' => 'field_nl_testi_order',        'label' => 'Display Order', 'name' => 'display_order', 'type' => 'number', 'default_value' => 10],
        ],
    ],

    // ── GALLERY ITEM CPT ─────────────────────────────────────────────
    [
        'key'      => 'group_nl_gallery_item',
        'title'    => 'Gallery Item Details',
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'nl_gallery_item']]],
        'fields'   => [
            ['key' => 'field_nl_gallery_photo',   'label' => 'Photo',          'name' => 'photo',         'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
            ['key' => 'field_nl_gallery_caption', 'label' => 'Caption',        'name' => 'caption',       'type' => 'text'],
            ['key' => 'field_nl_gallery_order',   'label' => 'Display Order',  'name' => 'display_order', 'type' => 'number', 'default_value' => 10],
        ],
    ],
];

$imported = 0;
$updated  = 0;

foreach ($groups as $group) {
    $existing = acf_get_field_group($group['key']);
    acf_import_field_group($group);
    if ($existing) {
        $updated++;
        WP_CLI::log("Updated: {$group['title']}");
    } else {
        $imported++;
        WP_CLI::log("Imported: {$group['title']}");
    }
}

WP_CLI::success("Done. {$imported} imported, {$updated} updated.");
WP_CLI::log('');
WP_CLI::log('Next steps:');
WP_CLI::log('  1. Verify all groups appear in WP Admin → ACF → Field Groups');
WP_CLI::log('  2. Remove acf_add_local_field_group() calls from inc/acf-fields.php');
WP_CLI::log('  3. Remove inc/post-types.php once CPTs are set up in CPTUI');
