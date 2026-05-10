<?php
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('nonla-style', get_stylesheet_uri(), ['parent-style'], '1.0');
});

// Force our front-page template past Elementor Pro's theme builder
add_filter('template_include', function ($template) {
    if (is_front_page()) {
        $custom = get_stylesheet_directory() . '/front-page.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $template;
}, 99999);
