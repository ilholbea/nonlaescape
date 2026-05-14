<?php
// Registers all Nonla custom post types.
// Previously managed via CPTUI plugin — now code-only.

add_action('init', function () {
    $cpts = [
        'nl_team_member' => ['Team Members', 'Team Member',  'dashicons-groups',        25, ['title', 'thumbnail']],
        'nl_project'     => ['Projects',     'Project',      'dashicons-location-alt',  26, ['title', 'thumbnail']],
        'nl_series'      => ['Series',       'Series',       'dashicons-book',           27, ['title']],
        'nl_place'       => ['Places',       'Place',        'dashicons-location',       28, ['title']],
        'nl_why_item'    => ['Why Items',    'Why Item',     'dashicons-star-filled',    29, ['title']],
        'nl_testimonial' => ['Testimonials', 'Testimonial',  'dashicons-format-quote',   30, ['title']],
        'nl_gallery_item'=> ['Gallery',      'Gallery Item', 'dashicons-format-gallery', 31, ['title']],
    ];

    foreach ($cpts as $slug => [$label, $singular, $icon, $pos, $supports]) {
        register_post_type($slug, [
            'labels'             => ['name' => $label, 'singular_name' => $singular, 'menu_name' => $label, 'add_new_item' => "Add $singular", 'edit_item' => "Edit $singular"],
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_nav_menus'  => false,
            'show_in_rest'       => false,
            'has_archive'        => false,
            'rewrite'            => false,
            'menu_position'      => $pos,
            'menu_icon'          => $icon,
            'supports'           => $supports,
            'capability_type'    => 'post',
        ]);
    }
});
