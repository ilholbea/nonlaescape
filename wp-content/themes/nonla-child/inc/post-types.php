<?php
// Register Custom Post Types for Nonla

add_action('init', function () {

    // Team Members
    register_post_type('nl_team_member', [
        'labels' => [
            'name'               => 'Team Members',
            'singular_name'      => 'Team Member',
            'add_new_item'       => 'Add Team Member',
            'edit_item'          => 'Edit Team Member',
            'new_item'           => 'New Team Member',
            'view_item'          => 'View Team Member',
            'search_items'       => 'Search Team Members',
            'not_found'          => 'No team members found',
            'menu_name'          => 'Team',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-groups',
        'menu_position'=> 25,
        'supports'     => ['title', 'thumbnail'],
        'rewrite'      => false,
    ]);

    // Projects
    register_post_type('nl_project', [
        'labels' => [
            'name'               => 'Projects',
            'singular_name'      => 'Project',
            'add_new_item'       => 'Add Project',
            'edit_item'          => 'Edit Project',
            'new_item'           => 'New Project',
            'view_item'          => 'View Project',
            'search_items'       => 'Search Projects',
            'not_found'          => 'No projects found',
            'menu_name'          => 'Projects',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-location-alt',
        'menu_position'=> 26,
        'supports'     => ['title', 'thumbnail'],
        'rewrite'      => false,
    ]);

});
