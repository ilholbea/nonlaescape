<?php
// ACF field groups — registered in code so they're version-controlled.
// Editing happens through WP Admin on each post, not here.

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    // ── TEAM MEMBER FIELDS ──────────────────────────────────────────
    acf_add_local_field_group([
        'key'      => 'group_nl_team_member',
        'title'    => 'Team Member Details',
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'nl_team_member']]],
        'menu_order' => 0,
        'fields'   => [
            [
                'key'           => 'field_nl_tm_role_en',
                'label'         => 'Role (English)',
                'name'          => 'role_en',
                'type'          => 'text',
                'placeholder'   => 'e.g. Founder · Cultural Ambassador',
            ],
            [
                'key'           => 'field_nl_tm_role_ro',
                'label'         => 'Role (Romanian)',
                'name'          => 'role_ro',
                'type'          => 'text',
                'placeholder'   => 'e.g. Fondator · Ambasador Cultural',
            ],
            [
                'key'           => 'field_nl_tm_city',
                'label'         => 'City',
                'name'          => 'city',
                'type'          => 'text',
                'placeholder'   => 'e.g. Ho Chi Minh City',
            ],
            [
                'key'           => 'field_nl_tm_bio_en',
                'label'         => 'Bio (English)',
                'name'          => 'bio_en',
                'type'          => 'textarea',
                'rows'          => 3,
            ],
            [
                'key'           => 'field_nl_tm_bio_ro',
                'label'         => 'Bio (Romanian)',
                'name'          => 'bio_ro',
                'type'          => 'textarea',
                'rows'          => 3,
            ],
            [
                'key'           => 'field_nl_tm_photo_url',
                'label'         => 'Photo URL',
                'name'          => 'photo_url',
                'type'          => 'url',
                'instructions'  => 'Paste a direct image URL, or upload a photo below.',
            ],
            [
                'key'           => 'field_nl_tm_photo',
                'label'         => 'Photo (upload)',
                'name'          => 'photo',
                'type'          => 'image',
                'return_format' => 'url',
                'preview_size'  => 'medium',
                'instructions'  => 'Upload a photo here, OR paste a URL above. Uploaded photo takes priority.',
            ],
            [
                'key'           => 'field_nl_tm_email',
                'label'         => 'Email',
                'name'          => 'email',
                'type'          => 'email',
            ],
            [
                'key'           => 'field_nl_tm_order',
                'label'         => 'Display Order',
                'name'          => 'display_order',
                'type'          => 'number',
                'default_value' => 10,
                'instructions'  => 'Lower numbers appear first. Use 1 for the founder.',
            ],
        ],
    ]);

    // ── PROJECT FIELDS ──────────────────────────────────────────────
    acf_add_local_field_group([
        'key'      => 'group_nl_project',
        'title'    => 'Project Details',
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'nl_project']]],
        'menu_order' => 0,
        'fields'   => [
            [
                'key'           => 'field_nl_proj_series',
                'label'         => 'Series',
                'name'          => 'series',
                'type'          => 'select',
                'choices'       => [
                    'vietnam-escape'      => 'Vietnam Escape',
                    'vietnam-undiscovered'=> 'Vietnam Undiscovered',
                ],
                'default_value' => 'vietnam-escape',
            ],
            [
                'key'           => 'field_nl_proj_edition',
                'label'         => 'Edition',
                'name'          => 'edition',
                'type'          => 'text',
                'placeholder'   => 'e.g. Vol. 6',
            ],
            [
                'key'           => 'field_nl_proj_year',
                'label'         => 'Year',
                'name'          => 'year',
                'type'          => 'text',
                'placeholder'   => 'e.g. 2026',
            ],
            [
                'key'           => 'field_nl_proj_status',
                'label'         => 'Status',
                'name'          => 'status',
                'type'          => 'select',
                'choices'       => [
                    'upcoming'  => 'Upcoming',
                    'completed' => 'Completed',
                ],
                'default_value' => 'upcoming',
            ],
            [
                'key'           => 'field_nl_proj_dates_en',
                'label'         => 'Dates (English)',
                'name'          => 'dates_en',
                'type'          => 'text',
                'placeholder'   => 'e.g. Oct 27 – Nov 11, 2026',
            ],
            [
                'key'           => 'field_nl_proj_dates_ro',
                'label'         => 'Dates (Romanian)',
                'name'          => 'dates_ro',
                'type'          => 'text',
                'placeholder'   => 'e.g. 27 Oct – 11 Nov 2026',
            ],
            [
                'key'           => 'field_nl_proj_duration_en',
                'label'         => 'Duration (English)',
                'name'          => 'duration_en',
                'type'          => 'text',
                'placeholder'   => 'e.g. 15 days',
                'default_value' => '15 days',
            ],
            [
                'key'           => 'field_nl_proj_duration_ro',
                'label'         => 'Duration (Romanian)',
                'name'          => 'duration_ro',
                'type'          => 'text',
                'placeholder'   => 'e.g. 15 zile',
                'default_value' => '15 zile',
            ],
            [
                'key'           => 'field_nl_proj_price',
                'label'         => 'Price',
                'name'          => 'price',
                'type'          => 'text',
                'placeholder'   => 'e.g. €1,895 or TBA',
            ],
            [
                'key'           => 'field_nl_proj_price_note_en',
                'label'         => 'Price Note (English)',
                'name'          => 'price_note_en',
                'type'          => 'text',
                'placeholder'   => 'e.g. all-inclusive · flights · hotels',
            ],
            [
                'key'           => 'field_nl_proj_price_note_ro',
                'label'         => 'Price Note (Romanian)',
                'name'          => 'price_note_ro',
                'type'          => 'text',
                'placeholder'   => 'e.g. all-inclusive · zboruri · hoteluri',
            ],
            [
                'key'           => 'field_nl_proj_destinations',
                'label'         => 'Destinations',
                'name'          => 'destinations',
                'type'          => 'text',
                'instructions'  => 'Comma-separated list. e.g. Hanoi, Ha Long Bay, Ninh Binh, Phu Quoc, Ho Chi Minh City',
            ],
            [
                'key'           => 'field_nl_proj_cover',
                'label'         => 'Cover Image (upload)',
                'name'          => 'cover_image',
                'type'          => 'image',
                'return_format' => 'url',
                'preview_size'  => 'medium',
            ],
            [
                'key'           => 'field_nl_proj_cover_url',
                'label'         => 'Cover Image URL (alternative)',
                'name'          => 'cover_url',
                'type'          => 'url',
                'instructions'  => 'Use this if the cover is a local theme photo (e.g. type the filename like hero.jpg). Uploaded image above takes priority.',
            ],
            [
                'key'           => 'field_nl_proj_desc_en',
                'label'         => 'Description (English)',
                'name'          => 'desc_en',
                'type'          => 'textarea',
                'rows'          => 4,
            ],
            [
                'key'           => 'field_nl_proj_desc_ro',
                'label'         => 'Description (Romanian)',
                'name'          => 'desc_ro',
                'type'          => 'textarea',
                'rows'          => 4,
            ],
        ],
    ]);
});
