<?php
// Registers all Nonla custom post types.
// Previously managed via CPTUI plugin — now code-only.

add_action('init', function () {
    // 6th element = public rewrite slug (null = private, string = publicly queryable with that slug)
    $cpts = [
        'nl_team_member'    => ['Team Members',    'Team Member',    'dashicons-groups',        25, ['title', 'thumbnail'], null],
        'nl_project'        => ['Projects',        'Project',        'dashicons-location-alt',  26, ['title', 'thumbnail'], 'project'],
        'nl_itinerary_day'  => ['Itinerary Days',  'Itinerary Day',  'dashicons-calendar-alt',  27, ['title'],             null],
        'nl_accommodation'  => ['Accommodations',  'Accommodation',  'dashicons-building',       28, ['title', 'thumbnail'], null],
        'nl_experience_stop' => ['Experience Stops', 'Experience Stop', 'dashicons-palmtree',     28, ['title'],             null],
        'nl_series'         => ['Series',          'Series',         'dashicons-book',           29, ['title'],             null],
        'nl_place'          => ['Places',          'Place',          'dashicons-location',       30, ['title'],             null],
        'nl_why_item'       => ['Why Items',       'Why Item',       'dashicons-star-filled',    31, ['title'],             null],
        'nl_testimonial'    => ['Testimonials',    'Testimonial',    'dashicons-format-quote',   32, ['title'],             null],
        'nl_gallery_item'   => ['Gallery',         'Gallery Item',   'dashicons-format-gallery', 33, ['title'],             null],
    ];

    foreach ($cpts as $slug => $cfg) {
        [$label, $singular, $icon, $pos, $supports] = $cfg;
        $pub_slug = $cfg[5] ?? null;
        register_post_type($slug, [
            'labels'             => ['name' => $label, 'singular_name' => $singular, 'menu_name' => $label, 'add_new_item' => "Add $singular", 'edit_item' => "Edit $singular"],
            'public'             => (bool) $pub_slug,
            'publicly_queryable' => (bool) $pub_slug,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_nav_menus'  => false,
            'show_in_rest'       => false,
            'has_archive'        => false,
            'rewrite'            => $pub_slug ? ['slug' => $pub_slug, 'with_front' => false] : false,
            'menu_position'      => $pos,
            'menu_icon'          => $icon,
            'supports'           => $supports,
            'capability_type'    => 'post',
        ]);
    }
});
