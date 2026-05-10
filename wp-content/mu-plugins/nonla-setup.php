<?php
/**
 * Nonla setup — activates child theme and static homepage on first load.
 * Auto-removes itself once done.
 */
add_action('init', function () {
    if (get_option('nonla_setup_done')) return;

    // Activate child theme
    switch_theme('nonla-child');

    // Create or find the homepage
    $home_id = get_page_by_path('home');
    if (!$home_id) {
        $home_id = wp_insert_post([
            'post_title'   => 'Home',
            'post_name'    => 'home',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ]);
    } else {
        $home_id = $home_id->ID;
    }

    // Set Reading settings: static front page
    update_option('show_on_front', 'page');
    update_option('page_on_front', $home_id);

    update_option('nonla_setup_done', true);
});
