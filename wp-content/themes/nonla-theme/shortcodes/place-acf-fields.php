<?php
// ACF field group for nl_place CPT — hover detail fields.

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    acf_add_local_field_group([
        'key'    => 'group_nl_place_hover',
        'title'  => 'Place — Hover Details',
        'fields' => [
            [
                'key'          => 'field_nl_place_hover_en',
                'label'        => 'Hover Details (EN)',
                'name'         => 'hover_details_en',
                'type'         => 'textarea',
                'rows'         => 3,
                'wrapper'      => ['width' => '50'],
                'instructions' => 'Extra info shown on hover/tap — keep it to 1-2 sentences.',
            ],
            [
                'key'          => 'field_nl_place_hover_ro',
                'label'        => 'Hover Details (RO)',
                'name'         => 'hover_details_ro',
                'type'         => 'textarea',
                'rows'         => 3,
                'wrapper'      => ['width' => '50'],
                'instructions' => 'Romanian version. Leave blank to fall back to EN.',
            ],
        ],
        'location' => [[
            ['param' => 'post_type', 'operator' => '==', 'value' => 'nl_place'],
        ]],
        'menu_order' => 10,
        'position'   => 'normal',
    ]);
});