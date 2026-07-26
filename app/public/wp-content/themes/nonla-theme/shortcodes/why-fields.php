<?php
// Adds Romanian counterparts to nl_why_item so the "Why Nonla" cards can be
// bilingual like the rest of the site. The EN title is the post title; the EN
// body is the existing `description` field. These two new fields hold the RO.
// Free ACF field types only. New names → no collision with the DB-registered
// icon/description/display_order fields.

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    acf_add_local_field_group([
        'key'    => 'group_nl_why_item_ro',
        'title'  => 'Why Item — Romanian',
        'fields' => [
            [
                'key'          => 'field_nl_why_title_ro',
                'label'        => 'Title (RO)',
                'name'         => 'title_ro',
                'type'         => 'text',
                'wrapper'      => ['width' => '100'],
                'instructions' => 'EN title is the post title above. Leave blank to fall back to EN.',
            ],
            [
                'key'          => 'field_nl_why_description_ro',
                'label'        => 'Description (RO)',
                'name'         => 'description_ro',
                'type'         => 'textarea',
                'rows'         => 3,
                'wrapper'      => ['width' => '100'],
                'instructions' => 'Romanian version of the description. Leave blank to fall back to EN.',
            ],
        ],
        'location' => [[
            ['param' => 'post_type', 'operator' => '==', 'value' => 'nl_why_item'],
        ]],
        'menu_order' => 5,
        'position'   => 'normal',
    ]);
});
