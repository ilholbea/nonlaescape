<?php

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    // ── PLACES (attached to the front page) ────────────────────────
    acf_add_local_field_group([
        'key'      => 'group_nl_places',
        'title'    => 'Places That Stay With You',
        'location' => [[['param' => 'page_type', 'operator' => '==', 'value' => 'front_page']]],
        'fields'   => [
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
                    [
                        'key'           => 'field_nl_place_image',
                        'label'         => 'Photo',
                        'name'          => 'image',
                        'type'          => 'image',
                        'return_format' => 'url',
                        'preview_size'  => 'medium',
                        'column_width'  => 20,
                    ],
                    [
                        'key'          => 'field_nl_place_tag',
                        'label'        => 'Tag (city / region)',
                        'name'         => 'tag',
                        'type'         => 'text',
                        'placeholder'  => 'e.g. Hanoi',
                        'column_width' => 20,
                    ],
                    [
                        'key'          => 'field_nl_place_name',
                        'label'        => 'Place Name',
                        'name'         => 'name',
                        'type'         => 'text',
                        'placeholder'  => 'e.g. Old Quarter',
                        'column_width' => 20,
                    ],
                    [
                        'key'          => 'field_nl_place_desc',
                        'label'        => 'Short Description',
                        'name'         => 'description',
                        'type'         => 'text',
                        'placeholder'  => 'e.g. Ancient streets, street food & coffee',
                        'column_width' => 40,
                    ],
                ],
            ],
        ],
    ]);

    // ── TEAM MEMBER FIELDS ──────────────────────────────────────────
    acf_add_local_field_group([
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
    ]);

    // ── PROJECT FIELDS ──────────────────────────────────────────────
    acf_add_local_field_group([
        'key'      => 'group_nl_project',
        'title'    => 'Project Details',
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'nl_project']]],
        'fields'   => [
            ['key' => 'field_nl_proj_series',       'label' => 'Series',                  'name' => 'series',         'type' => 'select', 'choices' => ['vietnam-escape' => 'Vietnam Escape', 'vietnam-undiscovered' => 'Vietnam Undiscovered'], 'default_value' => 'vietnam-escape'],
            ['key' => 'field_nl_proj_edition',      'label' => 'Edition',                 'name' => 'edition',        'type' => 'text', 'placeholder' => 'e.g. Vol. 6'],
            ['key' => 'field_nl_proj_year',         'label' => 'Year',                    'name' => 'year',           'type' => 'text'],
            ['key' => 'field_nl_proj_status',       'label' => 'Status',                  'name' => 'status',         'type' => 'select', 'choices' => ['upcoming' => 'Upcoming', 'completed' => 'Completed'], 'default_value' => 'upcoming'],
            ['key' => 'field_nl_proj_dates_en',     'label' => 'Dates (English)',          'name' => 'dates_en',       'type' => 'text'],
            ['key' => 'field_nl_proj_dates_ro',     'label' => 'Dates (Romanian)',         'name' => 'dates_ro',       'type' => 'text'],
            ['key' => 'field_nl_proj_duration_en',  'label' => 'Duration (English)',       'name' => 'duration_en',    'type' => 'text', 'default_value' => '15 days'],
            ['key' => 'field_nl_proj_duration_ro',  'label' => 'Duration (Romanian)',      'name' => 'duration_ro',    'type' => 'text', 'default_value' => '15 zile'],
            ['key' => 'field_nl_proj_price',        'label' => 'Price',                   'name' => 'price',          'type' => 'text'],
            ['key' => 'field_nl_proj_price_note_en','label' => 'Price Note (English)',     'name' => 'price_note_en',  'type' => 'text'],
            ['key' => 'field_nl_proj_price_note_ro','label' => 'Price Note (Romanian)',    'name' => 'price_note_ro',  'type' => 'text'],
            ['key' => 'field_nl_proj_destinations', 'label' => 'Destinations',            'name' => 'destinations',   'type' => 'text'],
            ['key' => 'field_nl_proj_cover',        'label' => 'Cover Image',             'name' => 'cover_image',    'type' => 'image', 'return_format' => 'url', 'preview_size' => 'medium'],
            ['key' => 'field_nl_proj_desc_en',      'label' => 'Description (English)',   'name' => 'desc_en',        'type' => 'textarea', 'rows' => 4],
            ['key' => 'field_nl_proj_desc_ro',      'label' => 'Description (Romanian)',  'name' => 'desc_ro',        'type' => 'textarea', 'rows' => 4],
            ['key' => 'field_nl_proj_register_form_url',      'label' => 'Register Form URL',  'name' => 'register_form_url',        'type' => 'text'],
        ],
    ]);
});
