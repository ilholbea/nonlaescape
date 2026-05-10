<?php
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('nonla-style', get_stylesheet_uri(), ['parent-style'], '1.0');
});

// Force our custom templates past Elementor Pro's theme builder
add_filter('template_include', function ($template) {
    if (is_front_page()) {
        $custom = get_stylesheet_directory() . '/front-page.php';
        if (file_exists($custom)) return $custom;
    }
    if (is_page('projects')) {
        $custom = get_stylesheet_directory() . '/page-projects.php';
        if (file_exists($custom)) return $custom;
    }
    if (is_page('legal')) {
        $custom = get_stylesheet_directory() . '/page-legal.php';
        if (file_exists($custom)) return $custom;
    }
    if (is_page('team')) {
        $custom = get_stylesheet_directory() . '/page-team.php';
        if (file_exists($custom)) return $custom;
    }
    return $template;
}, 99999);

// Auto-create required pages on first load
add_action('init', function () {
    $pages = [
        'projects' => 'Projects',
        'legal'    => 'Legal Notice',
        'team'     => 'Team',
    ];
    foreach ($pages as $slug => $title) {
        $opt = 'nonla_page_created_' . $slug;
        if (get_option($opt)) continue;
        if (!get_page_by_path($slug)) {
            wp_insert_post([
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => '',
            ]);
        }
        update_option($opt, true);
    }
});

// Nonla Settings — registration URL configurable from WP Admin
add_action('admin_menu', function () {
    add_options_page(
        'Nonla Settings',
        'Nonla Settings',
        'manage_options',
        'nonla-settings',
        'nonla_settings_page'
    );
});

add_action('admin_init', function () {
    register_setting('nonla_settings', 'nonla_registration_url', [
        'sanitize_callback' => 'esc_url_raw',
        'default'           => 'https://docs.google.com/forms/d/e/1FAIpQLScaiRzBL7j7vd7o2TVD7zEXSXCG7ObDLJvfEtXsygLuM_dCIQ/viewform',
    ]);
    register_setting('nonla_settings', 'nonla_contact_email', [
        'sanitize_callback' => 'sanitize_email',
        'default'           => 'contact@nonlaescape.com',
    ]);
});

function nonla_settings_page() {
    if (!current_user_can('manage_options')) return;
    if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'nonla_settings-options')) {
        update_option('nonla_registration_url', esc_url_raw($_POST['nonla_registration_url'] ?? ''));
        update_option('nonla_contact_email', sanitize_email($_POST['nonla_contact_email'] ?? ''));
        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
    }
    $reg_url   = get_option('nonla_registration_url', 'https://docs.google.com/forms/d/e/1FAIpQLScaiRzBL7j7vd7o2TVD7zEXSXCG7ObDLJvfEtXsygLuM_dCIQ/viewform');
    $contact   = get_option('nonla_contact_email', 'contact@nonlaescape.com');
    ?>
    <div class="wrap">
        <h1>Nonla Settings</h1>
        <form method="post">
            <?php wp_nonce_field('nonla_settings-options'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="nonla_registration_url">Registration Form URL</label></th>
                    <td>
                        <input type="url" id="nonla_registration_url" name="nonla_registration_url"
                               value="<?php echo esc_attr($reg_url); ?>" class="regular-text">
                        <p class="description">Google Forms (or any URL) used for the registration button on project pages.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="nonla_contact_email">Contact Email</label></th>
                    <td>
                        <input type="email" id="nonla_contact_email" name="nonla_contact_email"
                               value="<?php echo esc_attr($contact); ?>" class="regular-text">
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
    <?php
}
