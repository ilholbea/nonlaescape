<?php
// ACF field groups for nl_itinerary_day and nl_accommodation CPTs.
// Uses only free ACF field types (no repeater/gallery — those require Pro).

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    // ── Itinerary Day fields ────────────────────────────────────────────────
    acf_add_local_field_group([
        'key'    => 'group_nl_itinerary_day',
        'title'  => 'Itinerary Day',
        'fields' => [
            [
                'key'           => 'field_nl_iday_project',
                'label'         => 'Project',
                'name'          => 'project',
                'type'          => 'post_object',
                'post_type'     => ['nl_project'],
                'return_format' => 'id',
                'ui'            => 1,
                'allow_null'    => 0,
                'required'      => 1,
                'wrapper'       => ['width' => '100'],
                'instructions'  => 'Which project does this day belong to?',
            ],
            [
                'key'     => 'field_nl_iday_day',
                'label'   => 'Day #',
                'name'    => 'day',
                'type'    => 'number',
                'min'     => 0,
                'required' => 1,
                'wrapper' => ['width' => '20'],
                'instructions' => 'Day 0 = departure day.',
            ],
            [
                'key'     => 'field_nl_iday_date',
                'label'   => 'Date',
                'name'    => 'date',
                'type'    => 'text',
                'wrapper' => ['width' => '30'],
                'placeholder' => '14 April',
            ],
            [
                'key'     => 'field_nl_iday_title_ro',
                'label'   => 'Title (RO)',
                'name'    => 'title_ro',
                'type'    => 'text',
                'wrapper' => ['width' => '50'],
                'instructions' => 'EN title is the post title above.',
            ],
            [
                'key'     => 'field_nl_iday_desc_en',
                'label'   => 'Description (EN)',
                'name'    => 'desc_en',
                'type'    => 'textarea',
                'rows'    => 5,
                'wrapper' => ['width' => '50'],
            ],
            [
                'key'     => 'field_nl_iday_desc_ro',
                'label'   => 'Description (RO)',
                'name'    => 'desc_ro',
                'type'    => 'textarea',
                'rows'    => 5,
                'wrapper' => ['width' => '50'],
            ],
        ],
        'location' => [[
            ['param' => 'post_type', 'operator' => '==', 'value' => 'nl_itinerary_day'],
        ]],
        'menu_order' => 0,
        'position'   => 'normal',
    ]);

    // ── Accommodation fields ────────────────────────────────────────────────
    acf_add_local_field_group([
        'key'    => 'group_nl_accommodation',
        'title'  => 'Accommodation',
        'fields' => [
            [
                'key'           => 'field_nl_acc_project',
                'label'         => 'Project',
                'name'          => 'project',
                'type'          => 'post_object',
                'post_type'     => ['nl_project'],
                'return_format' => 'id',
                'ui'            => 1,
                'allow_null'    => 0,
                'required'      => 1,
                'wrapper'       => ['width' => '100'],
                'instructions'  => 'Which project does this accommodation belong to?',
            ],
            [
                'key'     => 'field_nl_acc_city',
                'label'   => 'City',
                'name'    => 'city',
                'type'    => 'text',
                'required' => 1,
                'wrapper' => ['width' => '50'],
            ],
            [
                'key'     => 'field_nl_acc_order',
                'label'   => 'Display Order',
                'name'    => 'display_order',
                'type'    => 'number',
                'default_value' => 1,
                'wrapper' => ['width' => '50'],
                'instructions' => 'Order within the project (1 = first city).',
            ],
            [
                'key'     => 'field_nl_acc_desc_en',
                'label'   => 'Description (EN)',
                'name'    => 'desc_en',
                'type'    => 'textarea',
                'rows'    => 3,
                'wrapper' => ['width' => '50'],
                'instructions' => 'Hotel name is the post title. Main photo = Featured Image.',
            ],
            [
                'key'     => 'field_nl_acc_desc_ro',
                'label'   => 'Description (RO)',
                'name'    => 'desc_ro',
                'type'    => 'textarea',
                'rows'    => 3,
                'wrapper' => ['width' => '50'],
            ],
            [
                'key'     => 'field_nl_acc_booking',
                'label'   => 'Booking.com URL',
                'name'    => 'booking_url',
                'type'    => 'url',
                'wrapper' => ['width' => '100'],
            ],
            [
                'key'           => 'field_nl_acc_photo2',
                'label'         => 'Photo 2',
                'name'          => 'photo_2',
                'type'          => 'image',
                'return_format' => 'url',
                'preview_size'  => 'medium',
                'wrapper'       => ['width' => '33'],
            ],
            [
                'key'           => 'field_nl_acc_photo3',
                'label'         => 'Photo 3',
                'name'          => 'photo_3',
                'type'          => 'image',
                'return_format' => 'url',
                'preview_size'  => 'medium',
                'wrapper'       => ['width' => '33'],
            ],
            [
                'key'           => 'field_nl_acc_photo4',
                'label'         => 'Photo 4',
                'name'          => 'photo_4',
                'type'          => 'image',
                'return_format' => 'url',
                'preview_size'  => 'medium',
                'wrapper'       => ['width' => '33'],
            ],
        ],
        'location' => [[
            ['param' => 'post_type', 'operator' => '==', 'value' => 'nl_accommodation'],
        ]],
        'menu_order' => 0,
        'position'   => 'normal',
    ]);
});
