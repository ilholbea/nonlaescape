<?php
/**
 * Injects Elementor page data for all 4 Nonla pages.
 * Run via: wp eval-file inject-elementor.php
 *
 * Section HTML lives in sections/  (home page blocks)
 * Page HTML lives in   pages/      (inner pages)
 */

// ── HELPERS ──────────────────────────────────────────────────────────────────

function nl_id($n) { return 'nl' . str_pad($n, 5, '0', STR_PAD_LEFT); }

function nl_section($id, $settings, $columns) {
    return ['id' => $id, 'elType' => 'section', 'settings' => $settings, 'elements' => $columns, 'isInner' => false];
}

function nl_column($id, $size, $widgets, $settings = []) {
    $settings['_column_size'] = $size;
    return ['id' => $id, 'elType' => 'column', 'settings' => $settings, 'elements' => $widgets];
}

function nl_widget($id, $type, $settings) {
    return ['id' => $id, 'elType' => 'widget', 'widgetType' => $type, 'settings' => $settings, 'elements' => []];
}

function nl_html($id, $html) {
    return nl_widget($id, 'html', ['html' => $html]);
}

function nl_sc($id, $code) {
    return nl_widget($id, 'shortcode', ['shortcode' => $code]);
}

function nl_sec($settings = []) {
    return array_merge([
        'layout'  => 'full_width',
        'padding' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => true],
    ], $settings);
}

function nl_col_settings() {
    return ['padding' => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => true]];
}

function nl_get_or_create($slug, $title) {
    $page = get_page_by_path($slug);
    if ($page) return $page;
    $id = wp_insert_post([
        'post_title'   => $title,
        'post_name'    => $slug,
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '',
    ]);
    return get_post($id);
}

function nl_inject($page, $data) {
    if (!$page) return;
    $json = wp_json_encode($data);
    update_post_meta($page->ID, '_elementor_data', wp_slash($json));
    update_post_meta($page->ID, '_elementor_edit_mode', 'builder');
    update_post_meta($page->ID, '_elementor_template_type', 'wp-page');
    update_post_meta($page->ID, '_elementor_version', '4.0.5');
    wp_cache_delete($page->ID, 'post_meta');
    WP_CLI::success("Injected → '{$page->post_title}' (ID {$page->ID})");
}

// ── SHARED VARIABLES ─────────────────────────────────────────────────────────

$photos        = get_stylesheet_directory_uri() . '/assets/photos/';
$videos        = get_stylesheet_directory_uri() . '/assets/videos/';
$home_url      = home_url('/');
$projects_url  = home_url('/projects');
$contact_email = get_option('nonla_contact_email', 'contact@nonlaescape.com');

$arrow    = '<svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M2.5 7.5h10M8.5 3.5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$arrow_sm = '<svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 7h10M7.5 2.5l4.5 4.5-4.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

$i = 1;

// ── SECTION INCLUDES (home page) ─────────────────────────────────────────────

require __DIR__ . '/sections/hero.php';
require __DIR__ . '/sections/ticker.php';
require __DIR__ . '/sections/about.php';
require __DIR__ . '/sections/series.php';
require __DIR__ . '/sections/places.php';
require __DIR__ . '/sections/why.php';
require __DIR__ . '/sections/testimonials.php';
require __DIR__ . '/sections/gallery.php';
require __DIR__ . '/sections/cta.php';

// ── PAGE INCLUDES (inner pages) ───────────────────────────────────────────────

require __DIR__ . '/pages/projects.php';
require __DIR__ . '/pages/team.php';
require __DIR__ . '/pages/legal.php';

// ═══════════════════════════════════════════════════════════════════════════════
// ASSEMBLE HOME PAGE
// ═══════════════════════════════════════════════════════════════════════════════

$home_data = [
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $hero_html)],         nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $ticker_html)],       nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $about_html)],        nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $series_html)],       nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $dest_html)],         nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $why_html)],          nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $testimonials_html)], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $gallery_html)],      nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $cta_html)],          nl_col_settings())]),
];

// ═══════════════════════════════════════════════════════════════════════════════
// INJECT ALL PAGES
// ═══════════════════════════════════════════════════════════════════════════════

$home_page = nl_get_or_create('home', 'Home');
update_option('page_on_front', $home_page->ID);
update_option('show_on_front', 'page');
nl_inject($home_page, $home_data);

nl_inject(nl_get_or_create('projects', 'Projects'), $proj_data);
nl_inject(nl_get_or_create('team', 'Team'), $team_data);
nl_inject(nl_get_or_create('legal', 'Legal Notice'), $legal_data);

WP_CLI::success("All pages injected. Run: wp elementor flush-css");
