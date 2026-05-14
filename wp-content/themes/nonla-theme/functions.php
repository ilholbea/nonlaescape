<?php

// ── SHORTCODES & CPTs ─────────────────────────────────────────────────────────

require_once __DIR__ . '/shortcodes/cpt-registration.php';
require_once __DIR__ . '/shortcodes/project-acf-fields.php';
require_once __DIR__ . '/shortcodes/team.php';
require_once __DIR__ . '/shortcodes/projects.php';
require_once __DIR__ . '/shortcodes/places.php';
require_once __DIR__ . '/shortcodes/series.php';
require_once __DIR__ . '/shortcodes/gallery.php';
require_once __DIR__ . '/shortcodes/why.php';
require_once __DIR__ . '/shortcodes/testimonials.php';
require_once __DIR__ . '/shortcodes/countdown.php';

// ── ENQUEUE ───────────────────────────────────────────────────────────────────

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('nonla-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=Inter:wght@300;400;500;600&family=Bebas+Neue&display=swap', [], null);
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('nonla-style', get_stylesheet_uri(), ['parent-style'], '1.0');
});

// ── FOOTER SCRIPTS ────────────────────────────────────────────────────────────

add_action('wp_footer', function () { ?>
<script>
(function () {
  var obs = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (e.isIntersecting) { e.target.classList.add('on'); obs.unobserve(e.target); }
    });
  }, { threshold: 0.08, rootMargin: '0px 0px -32px 0px' });
  document.querySelectorAll('.nl-reveal').forEach(function (el) { obs.observe(el); });

  window.nlFilterProjects = function (btn, filter) {
    document.querySelectorAll('.nl-filter-btn').forEach(function (b) { b.classList.remove('active'); });
    btn.classList.add('active');
    document.querySelectorAll('.nl-project-card').forEach(function (card) {
      var show = filter === 'all' || card.dataset.series === filter || card.dataset.status === filter;
      card.style.display = show ? '' : 'none';
    });
  };
})();
</script>
<?php }, 20);

// ── CLEAN UP ADMIN MENU ───────────────────────────────────────────────────────

add_action('admin_menu', function () {
    remove_menu_page('edit.php');           // Posts
    remove_menu_page('edit-comments.php'); // Comments
}, 999);

// ── NONLA SETTINGS ────────────────────────────────────────────────────────────

add_action('admin_menu', function () {
    add_options_page('Nonla Settings', 'Nonla Settings', 'manage_options', 'nonla-settings', 'nonla_settings_page');
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
    $reg_url = get_option('nonla_registration_url', 'https://docs.google.com/forms/d/e/1FAIpQLScaiRzBL7j7vd7o2TVD7zEXSXCG7ObDLJvfEtXsygLuM_dCIQ/viewform');
    $contact = get_option('nonla_contact_email', 'contact@nonlaescape.com');
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
                        <p class="description">URL used for the "Register Now" button on project cards.</p>
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
