<?php

add_action('init', function () {

    register_post_type('nl_team_member', [
        'labels' => [
            'name'          => 'Team Members',
            'singular_name' => 'Team Member',
            'add_new_item'  => 'Add Team Member',
            'edit_item'     => 'Edit Team Member',
            'menu_name'     => 'Team',
        ],
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_icon'     => 'dashicons-groups',
        'menu_position' => 25,
        'supports'      => ['title', 'thumbnail'],
        'rewrite'       => false,
    ]);

    register_post_type('nl_project', [
        'labels' => [
            'name'          => 'Projects',
            'singular_name' => 'Project',
            'add_new_item'  => 'Add Project',
            'edit_item'     => 'Edit Project',
            'menu_name'     => 'Projects',
        ],
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_icon'     => 'dashicons-location-alt',
        'menu_position' => 26,
        'supports'      => ['title', 'thumbnail'],
        'rewrite'       => false,
    ]);

});
