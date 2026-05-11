<?php
/**
 * Injects Elementor page data for all 4 Nonla pages.
 * Run via: wp eval-file inject-elementor.php
 */

$photos = get_stylesheet_directory_uri() . '/assets/photos/';

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

function nl_heading($id, $text, $tag = 'h2', $extra = []) {
    return nl_widget($id, 'heading', array_merge(['title' => $text, 'header_size' => $tag], $extra));
}

function nl_text($id, $html) {
    return nl_widget($id, 'text-editor', ['editor' => $html]);
}

function nl_html($id, $html) {
    return nl_widget($id, 'html', ['html' => $html]);
}

function nl_button($id, $text, $url, $extra = []) {
    return nl_widget($id, 'button', array_merge(['text' => $text, 'link' => ['url' => $url], 'button_type' => 'info'], $extra));
}

function nl_spacer($id, $px = 40) {
    return nl_widget($id, 'spacer', ['space' => ['size' => $px, 'unit' => 'px']]);
}

function nl_inject($slug, $data) {
    $page = get_page_by_path($slug);
    if (!$page) { WP_CLI::warning("Page '$slug' not found, skipping."); return; }
    $json = wp_json_encode($data);
    update_post_meta($page->ID, '_elementor_data', wp_slash($json));
    update_post_meta($page->ID, '_elementor_edit_mode', 'builder');
    update_post_meta($page->ID, '_elementor_template_type', 'wp-page');
    update_post_meta($page->ID, '_elementor_version', '3.21.0');
    wp_cache_delete($page->ID, 'post_meta');
    WP_CLI::success("Injected Elementor data → '{$page->post_title}' (ID {$page->ID})");
}

$i = 1; // ID counter

// ═══════════════════════════════════════════════════════════════════
// LEGAL PAGE
// ═══════════════════════════════════════════════════════════════════

$legal_data = [
    nl_section(nl_id($i++), ['background_background' => 'classic', 'background_color' => '#FAF5EC', 'padding' => ['top' => '80', 'bottom' => '80', 'unit' => 'px', 'isLinked' => false]], [
        nl_column(nl_id($i++), 100, [
            nl_html(nl_id($i++), '<div class="nl-eyebrow">Legal Notice · Mențiuni Legale</div>'),
            nl_heading(nl_id($i++), 'Legal Information', 'h1'),
            nl_html(nl_id($i++), '<div class="nl-legal-wrap">
<h2>About the Organization</h2>
<p><strong>Asociația Nonla Cultural Escape</strong> is a Romanian non-governmental organization (NGO) registered under Romanian law, dedicated to cultural exchange and sustainable travel.</p>
<ul>
  <li><strong>Legal name:</strong> Asociația Nonla Cultural Escape</li>
  <li><strong>CIF (Tax ID):</strong> 49845083</li>
  <li><strong>Registered address:</strong> Câmpulung Muscel, Argeș County, Romania</li>
  <li><strong>Contact:</strong> contact@nonlaescape.com</li>
</ul>

<h2>Services</h2>
<p>Nonla Cultural Escape organizes small-group cultural travel experiences to Vietnam. All trips are all-inclusive packages covering flights, accommodation, transfers, and guided experiences.</p>

<h2>Booking & Payments</h2>
<p>Reservations are confirmed upon receipt of a deposit. Full payment terms are communicated at the time of booking. Nonla reserves the right to cancel or modify itineraries due to force majeure events.</p>

<h2>Privacy & Data</h2>
<p>We collect only the personal data necessary to organize your trip (name, contact details, travel documents). Data is stored securely and never sold to third parties. You may request access to or deletion of your data at any time by contacting us at contact@nonlaescape.com.</p>
<p>This site uses cookies for basic analytics only. No advertising cookies are used.</p>

<h2>Liability</h2>
<p>Nonla Cultural Escape acts as an organizer and coordinator. We work with vetted local partners for accommodation and transport. While every care is taken, Nonla is not liable for events beyond our control including weather, political events, or acts of force majeure.</p>

<h2>Intellectual Property</h2>
<p>All content on this website — text, photos, and design — is the property of Asociația Nonla Cultural Escape unless otherwise stated. Reproduction without written permission is prohibited.</p>

<h2>Governing Law</h2>
<p>These terms are governed by Romanian law. Any disputes shall be resolved in the courts of Argeș County, Romania.</p>
</div>'),
        ]),
    ]),
];

nl_inject('legal', $legal_data);

// ═══════════════════════════════════════════════════════════════════
// TEAM PAGE
// ═══════════════════════════════════════════════════════════════════

$team_data = [
    // Header section
    nl_section(nl_id($i++), [
        'background_background' => 'classic',
        'background_color'      => '#FAF5EC',
        'padding' => ['top' => '100', 'bottom' => '60', 'unit' => 'px', 'isLinked' => false],
    ], [
        nl_column(nl_id($i++), 100, [
            nl_html(nl_id($i++), '<div class="nl-eyebrow">Who We Are · Cine Suntem</div>'),
            nl_heading(nl_id($i++), 'Meet the Team', 'h1'),
            nl_text(nl_id($i++), '<p style="font-size:17px;color:#4A3318;max-width:580px;line-height:1.75;font-weight:300;">The people behind every Nonla escape — coordinators, guides, and cultural ambassadors who make each trip unforgettable.</p>'),
        ]),
    ]),

    // Team grid section (shortcode)
    nl_section(nl_id($i++), [
        'background_background' => 'classic',
        'background_color'      => '#FAF5EC',
        'padding' => ['top' => '0', 'bottom' => '100', 'unit' => 'px', 'isLinked' => false],
    ], [
        nl_column(nl_id($i++), 100, [
            nl_html(nl_id($i++), '<div style="font-size:13px;color:#7A6040;margin-bottom:24px;padding:14px 18px;background:rgba(184,85,32,.06);border-radius:4px;border-left:3px solid #B85520;">
  <strong>Admin tip:</strong> Add or edit team members via <strong>WP Admin → Team → Add New</strong>. They appear here automatically.
</div>'),
            nl_widget(nl_id($i++), 'shortcode', ['shortcode' => '[nonla_team]']),
        ]),
    ]),
];

nl_inject('team', $team_data);

// ═══════════════════════════════════════════════════════════════════
// PROJECTS PAGE
// ═══════════════════════════════════════════════════════════════════

$projects_data = [
    // Header
    nl_section(nl_id($i++), [
        'background_background' => 'classic',
        'background_color'      => '#FAF5EC',
        'padding' => ['top' => '100', 'bottom' => '60', 'unit' => 'px', 'isLinked' => false],
    ], [
        nl_column(nl_id($i++), 100, [
            nl_html(nl_id($i++), '<div class="nl-eyebrow">Our Escapes · Escapadele Noastre</div>'),
            nl_heading(nl_id($i++), 'Vietnam Escape Projects', 'h1'),
            nl_text(nl_id($i++), '<p style="font-size:17px;color:#4A3318;max-width:580px;line-height:1.75;font-weight:300;">Every edition is a new adventure. Browse our past and upcoming escapes through Vietnam.</p>'),
        ]),
    ]),

    // Two series cards
    nl_section(nl_id($i++), [
        'background_background' => 'classic',
        'background_color'      => '#FAF5EC',
        'padding' => ['top' => '0', 'bottom' => '60', 'unit' => 'px', 'isLinked' => false],
        'gap' => 'wider',
    ], [
        nl_column(nl_id($i++), 50, [
            nl_html(nl_id($i++), '<div class="nl-card">
  <div class="nl-card-label">Annual Group Escape</div>
  <div class="nl-card-title">Vietnam Escape</div>
  <p class="nl-card-desc">Our flagship annual escape — 15 days through Vietnam\'s iconic destinations. Flights, hotels, transfers, and daily guided experiences all included.</p>
</div>'),
        ]),
        nl_column(nl_id($i++), 50, [
            nl_html(nl_id($i++), '<div class="nl-card">
  <div class="nl-card-label">Off The Beaten Path</div>
  <div class="nl-card-title">Vietnam Undiscovered</div>
  <p class="nl-card-desc">For those who want to go deeper. Remote villages, hidden valleys, and lesser-known corners of Vietnam. More adventurous, more intimate.</p>
</div>'),
        ]),
    ]),

    // Projects grid (shortcode)
    nl_section(nl_id($i++), [
        'background_background' => 'classic',
        'background_color'      => '#FAF5EC',
        'padding' => ['top' => '0', 'bottom' => '100', 'unit' => 'px', 'isLinked' => false],
    ], [
        nl_column(nl_id($i++), 100, [
            nl_heading(nl_id($i++), 'All Editions', 'h2'),
            nl_html(nl_id($i++), '<div style="font-size:13px;color:#7A6040;margin-bottom:24px;padding:14px 18px;background:rgba(184,85,32,.06);border-radius:4px;border-left:3px solid #B85520;">
  <strong>Admin tip:</strong> Add or edit projects via <strong>WP Admin → Projects → Add New</strong>. They appear here automatically.
</div>'),
            nl_widget(nl_id($i++), 'shortcode', ['shortcode' => '[nonla_projects]']),
        ]),
    ]),
];

nl_inject('projects', $projects_data);

// ═══════════════════════════════════════════════════════════════════
// HOME PAGE
// ═══════════════════════════════════════════════════════════════════

$home_data = [

    // 1. HERO
    nl_section(nl_id($i++), [
        'background_background'         => 'classic',
        'background_image'              => ['url' => $photos . 'hero.jpg', 'id' => ''],
        'background_size'               => 'cover',
        'background_position'           => 'center center',
        'height'                        => 'min-height',
        'min_height'                    => ['size' => 100, 'unit' => 'vh'],
        'background_overlay_background' => 'classic',
        'background_overlay_color'      => 'rgba(20,10,4,0.6)',
        'background_overlay_opacity'    => ['unit' => 'px', 'size' => 1],
        'padding'                       => ['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false],
    ], [
        nl_column(nl_id($i++), 100, [
            nl_html(nl_id($i++), '<div style="font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:#D4A030;font-weight:500;display:flex;align-items:center;gap:10px;margin-bottom:22px;"><span style="width:28px;height:1px;background:#D4A030;display:block;"></span>Escape the ordinary. Discover the unknown.</div>'),
            nl_heading(nl_id($i++), 'Cultural <em>Escapes</em><br>Through Vietnam', 'h1', [
                'typography_typography' => 'custom',
                'typography_font_family' => 'Cormorant Garamond',
                'typography_font_size'   => ['size' => 100, 'unit' => 'px'],
                'typography_font_weight' => '600',
                'typography_line_height' => ['size' => 0.92, 'unit' => 'em'],
                'title_color'            => '#FFFDF7',
            ]),
            nl_text(nl_id($i++), '<p style="font-size:17px;color:rgba(255,253,247,.75);max-width:440px;line-height:1.75;font-weight:300;">Nonla organizes immersive small-group trips to Vietnam — authentic experiences designed around real connection with the country and its people.</p>'),
            nl_spacer(nl_id($i++), 20),
            nl_html(nl_id($i++), '<div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;">
  <a href="/projects/" style="display:inline-flex;align-items:center;gap:10px;background:#B85520;color:#fff;font-weight:600;font-size:12px;letter-spacing:.12em;text-transform:uppercase;padding:14px 32px;border-radius:3px;text-decoration:none;">See Our Projects →</a>
  <a href="#about" style="font-size:12px;letter-spacing:.12em;text-transform:uppercase;font-weight:500;color:rgba(255,253,247,.6);border-bottom:1px solid rgba(255,253,247,.25);padding:14px 4px;text-decoration:none;">Learn About Us</a>
</div>'),
            nl_spacer(nl_id($i++), 40),
            nl_html(nl_id($i++), '<div style="display:flex;gap:36px;border-left:1px solid rgba(255,253,247,.18);padding-left:36px;">
  <div><div style="font-family:Bebas Neue,sans-serif;font-size:44px;color:#D4A030;">5+</div><div style="font-size:10px;text-transform:uppercase;letter-spacing:.18em;color:rgba(255,253,247,.45);margin-top:4px;">Years</div></div>
  <div><div style="font-family:Bebas Neue,sans-serif;font-size:44px;color:#D4A030;">5</div><div style="font-size:10px;text-transform:uppercase;letter-spacing:.18em;color:rgba(255,253,247,.45);margin-top:4px;">Editions</div></div>
  <div><div style="font-family:Bebas Neue,sans-serif;font-size:44px;color:#D4A030;">100+</div><div style="font-size:10px;text-transform:uppercase;letter-spacing:.18em;color:rgba(255,253,247,.45);margin-top:4px;">Travelers</div></div>
</div>'),
        ], ['padding' => ['top' => '120', 'right' => '52', 'bottom' => '90', 'left' => '52', 'unit' => 'px', 'isLinked' => false]]),
    ]),

    // 2. TICKER
    nl_section(nl_id($i++), [
        'background_background' => 'classic',
        'background_color'      => '#B85520',
        'padding' => ['top' => '0', 'bottom' => '0', 'unit' => 'px', 'isLinked' => false],
    ], [
        nl_column(nl_id($i++), 100, [
            nl_html(nl_id($i++), '<style>
.nl-ticker-wrap{overflow:hidden;padding:13px 0;}
.nl-ticker{display:flex;animation:nltick 30s linear infinite;white-space:nowrap;}
.nl-ticker-item{display:inline-flex;align-items:center;gap:18px;padding:0 24px;font-size:11px;font-weight:600;letter-spacing:.2em;text-transform:uppercase;color:rgba(255,255,255,.88);}
.nl-ticker-dot{width:3px;height:3px;background:rgba(255,255,255,.35);border-radius:50%;display:inline-block;}
@keyframes nltick{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
</style>
<div class="nl-ticker-wrap"><div class="nl-ticker">
<span class="nl-ticker-item"><span class="nl-ticker-dot"></span>Vietnam Escape</span>
<span class="nl-ticker-item"><span class="nl-ticker-dot"></span>Vietnam Undiscovered</span>
<span class="nl-ticker-item"><span class="nl-ticker-dot"></span>ONG Românesc</span>
<span class="nl-ticker-item"><span class="nl-ticker-dot"></span>Grupuri Mici</span>
<span class="nl-ticker-item"><span class="nl-ticker-dot"></span>Authentic Experiences</span>
<span class="nl-ticker-item"><span class="nl-ticker-dot"></span>Expert Coordinators</span>
<span class="nl-ticker-item"><span class="nl-ticker-dot"></span>All-Inclusive</span>
<span class="nl-ticker-item"><span class="nl-ticker-dot"></span>Zboruri Incluse</span>
<span class="nl-ticker-item"><span class="nl-ticker-dot"></span>Vietnam Escape</span>
<span class="nl-ticker-item"><span class="nl-ticker-dot"></span>Vietnam Undiscovered</span>
<span class="nl-ticker-item"><span class="nl-ticker-dot"></span>ONG Românesc</span>
<span class="nl-ticker-item"><span class="nl-ticker-dot"></span>Grupuri Mici</span>
<span class="nl-ticker-item"><span class="nl-ticker-dot"></span>Authentic Experiences</span>
<span class="nl-ticker-item"><span class="nl-ticker-dot"></span>Expert Coordinators</span>
<span class="nl-ticker-item"><span class="nl-ticker-dot"></span>All-Inclusive</span>
<span class="nl-ticker-item"><span class="nl-ticker-dot"></span>Zboruri Incluse</span>
</div></div>'),
        ]),
    ]),

    // 3. ABOUT
    nl_section(nl_id($i++), [
        'background_background' => 'classic',
        'background_color'      => '#FAF5EC',
        'padding' => ['top' => '100', 'bottom' => '100', 'unit' => 'px', 'isLinked' => false],
        'gap' => 'wider',
    ], [
        nl_column(nl_id($i++), 50, [
            nl_widget(nl_id($i++), 'image', [
                'image' => ['url' => $photos . 'tamcoc.jpg', 'id' => ''],
                'image_size' => 'full',
                'caption_source' => 'none',
            ]),
        ]),
        nl_column(nl_id($i++), 50, [
            nl_html(nl_id($i++), '<div class="nl-eyebrow">Who We Are</div>'),
            nl_heading(nl_id($i++), 'More Than<br><em>a Trip.</em>', 'h2', [
                'typography_font_family' => 'Cormorant Garamond',
                'typography_font_size'   => ['size' => 64, 'unit' => 'px'],
                'typography_font_weight' => '600',
                'typography_line_height' => ['size' => 0.96, 'unit' => 'em'],
            ]),
            nl_text(nl_id($i++), '<p style="font-size:16px;color:#4A3318;line-height:1.85;font-weight:300;margin-bottom:18px;">Nonla Cultural Escape is a non-governmental organization founded in Romania with a global vision — offering a safe and inclusive space for cultural exchange and personal growth.</p>
<p style="font-size:16px;color:#4A3318;line-height:1.85;font-weight:300;margin-bottom:18px;">We organize small-group trips to Vietnam with everything handled — flights, accommodation, transfers, and expert coordinators who speak Romanian, English, and Vietnamese.</p>
<blockquote style="font-family:Cormorant Garamond,serif;font-style:italic;font-size:22px;color:#1A0E06;line-height:1.5;border-left:2.5px solid #B85520;padding-left:22px;margin:28px 0;">Building connections through shared adventures and creating a supportive environment for everyone.</blockquote>'),
        ]),
    ]),

    // 4. SERIES
    nl_section(nl_id($i++), [
        'background_background' => 'classic',
        'background_color'      => '#FAF5EC',
        'padding' => ['top' => '0', 'bottom' => '100', 'unit' => 'px', 'isLinked' => false],
    ], [
        nl_column(nl_id($i++), 100, [
            nl_html(nl_id($i++), '<div class="nl-eyebrow">Our Escapes</div>'),
            nl_heading(nl_id($i++), 'Two Series.<br><em>One Vietnam.</em>', 'h2', [
                'typography_font_family' => 'Cormorant Garamond',
                'typography_font_size'   => ['size' => 64, 'unit' => 'px'],
                'typography_font_weight' => '600',
                'typography_line_height' => ['size' => 0.96, 'unit' => 'em'],
            ]),
            nl_spacer(nl_id($i++), 32),
            nl_html(nl_id($i++), '<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
  <div class="nl-card">
    <div class="nl-card-label">Annual Group Escape</div>
    <div class="nl-card-title">Vietnam Escape</div>
    <p class="nl-card-desc">Our flagship annual escape — 15 days through Vietnam\'s iconic destinations. From Hanoi\'s ancient streets to Phu Quoc\'s tropical waters, everything is included: flights, hotels, transfers, and daily guided experiences.</p>
    <a href="/projects/" class="nl-link">See Editions →</a>
  </div>
  <div class="nl-card">
    <div class="nl-card-label">Off The Beaten Path</div>
    <div class="nl-card-title">Vietnam Undiscovered</div>
    <p class="nl-card-desc">For those who want to go deeper. Remote villages, hidden valleys, and lesser-known corners of Vietnam that most travelers never reach. More adventurous, more intimate.</p>
    <a href="/projects/" class="nl-link">See Editions →</a>
  </div>
</div>'),
        ]),
    ]),

    // 5. DESTINATIONS
    nl_section(nl_id($i++), [
        'background_background' => 'classic',
        'background_color'      => '#F2E8D4',
        'padding' => ['top' => '80', 'bottom' => '0', 'unit' => 'px', 'isLinked' => false],
    ], [
        nl_column(nl_id($i++), 100, [
            nl_html(nl_id($i++), '<div class="nl-eyebrow">Where We Go</div>'),
            nl_heading(nl_id($i++), 'Places That Stay<br><em>With You.</em>', 'h2', [
                'typography_font_family' => 'Cormorant Garamond',
                'typography_font_size'   => ['size' => 64, 'unit' => 'px'],
                'typography_font_weight' => '600',
                'typography_line_height' => ['size' => 0.96, 'unit' => 'em'],
            ]),
            nl_spacer(nl_id($i++), 40),
            nl_html(nl_id($i++), '<style>
.nl-dest-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:3px;}
.nl-dest-card{position:relative;overflow:hidden;aspect-ratio:3/4;}
.nl-dest-card img{width:100%;height:100%;object-fit:cover;transition:transform .6s ease;display:block;}
.nl-dest-card:hover img{transform:scale(1.05);}
.nl-dest-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(20,10,4,.88) 0%,rgba(20,10,4,.2) 55%,transparent 100%);display:flex;flex-direction:column;justify-content:flex-end;padding:28px 22px;}
.nl-dest-tag{font-size:10px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:#D4A030;margin-bottom:6px;}
.nl-dest-name{font-family:Cormorant Garamond,serif;font-size:34px;font-weight:600;color:#FFFDF7;margin-bottom:5px;line-height:1;}
.nl-dest-desc{font-size:12px;color:rgba(255,253,247,.6);line-height:1.5;font-weight:300;}
@media(max-width:768px){.nl-dest-grid{grid-template-columns:repeat(2,1fr);}}
</style>
<div class="nl-dest-grid">
  <div class="nl-dest-card"><img src="' . $photos . 'hanoi.jpg" alt="Hanoi" loading="lazy"><div class="nl-dest-overlay"><div class="nl-dest-tag">Hanoi</div><div class="nl-dest-name">Old Quarter</div><div class="nl-dest-desc">Ancient streets, Temple of Literature, street food &amp; Vietnamese coffee</div></div></div>
  <div class="nl-dest-card"><img src="' . $photos . 'halong.jpg" alt="Ha Long Bay" loading="lazy"><div class="nl-dest-overlay"><div class="nl-dest-tag">Lan Ha Bay</div><div class="nl-dest-name">Ha Long</div><div class="nl-dest-desc">UNESCO limestone karsts, emerald waters, boat trips &amp; kayaking</div></div></div>
  <div class="nl-dest-card"><img src="' . $photos . 'ninhbinh.jpg" alt="Ninh Binh" loading="lazy"><div class="nl-dest-overlay"><div class="nl-dest-tag">Ninh Binh</div><div class="nl-dest-name">Trang An</div><div class="nl-dest-desc">River boats, Mua Cave, bicycle rides through endless rice paddies</div></div></div>
  <div class="nl-dest-card"><img src="' . $photos . 'phuquoc.jpg" alt="Phu Quoc" loading="lazy"><div class="nl-dest-overlay"><div class="nl-dest-tag">Phu Quoc</div><div class="nl-dest-name">Island Life</div><div class="nl-dest-desc">Tropical island, snorkeling, boat tours, hiking &amp; pure beach days</div></div></div>
</div>'),
        ]),
    ]),

    // 6. WHY NONLA
    nl_section(nl_id($i++), [
        'background_background' => 'classic',
        'background_color'      => '#FAF5EC',
        'padding' => ['top' => '100', 'bottom' => '100', 'unit' => 'px', 'isLinked' => false],
    ], [
        nl_column(nl_id($i++), 100, [
            nl_html(nl_id($i++), '<div class="nl-eyebrow">Why Nonla</div>'),
            nl_heading(nl_id($i++), 'The Difference<br><em>You\'ll Feel.</em>', 'h2', [
                'typography_font_family' => 'Cormorant Garamond',
                'typography_font_size'   => ['size' => 64, 'unit' => 'px'],
                'typography_font_weight' => '600',
                'typography_line_height' => ['size' => 0.96, 'unit' => 'em'],
            ]),
            nl_spacer(nl_id($i++), 40),
            nl_html(nl_id($i++), '<style>
.nl-why-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;}
.nl-why-item{background:#FFF8EE;border:1px solid #DDD0B0;border-radius:6px;padding:36px 30px;transition:box-shadow .2s,transform .2s;}
.nl-why-item:hover{box-shadow:0 8px 32px rgba(26,14,6,.08);transform:translateY(-2px);}
.nl-why-icon{font-size:26px;margin-bottom:16px;}
.nl-why-title{font-family:Cormorant Garamond,serif;font-size:22px;font-weight:600;margin-bottom:10px;color:#1A0E06;}
.nl-why-desc{font-size:13px;color:#4A3318;line-height:1.75;font-weight:300;}
@media(max-width:768px){.nl-why-grid{grid-template-columns:1fr;gap:14px;}}
</style>
<div class="nl-why-grid">
  <div class="nl-why-item"><div class="nl-why-icon">🧭</div><div class="nl-why-title">Local Expertise</div><p class="nl-why-desc">2–4 coordinators speak Vietnamese, Romanian &amp; English. No language barrier, no wasted days.</p></div>
  <div class="nl-why-item"><div class="nl-why-icon">🏨</div><div class="nl-why-title">Vetted Stays</div><p class="nl-why-desc">Every property personally tested and rated 8.5+ on Booking.com. Comfort you can count on.</p></div>
  <div class="nl-why-item"><div class="nl-why-icon">✈️</div><div class="nl-why-title">All-Inclusive</div><p class="nl-why-desc">Flights, transfers, accommodation, daily breakfast and curated experiences. You just show up.</p></div>
  <div class="nl-why-item"><div class="nl-why-icon">🌿</div><div class="nl-why-title">NGO Organized</div><p class="nl-why-desc">We\'re a certified NGO. Sustainable travel, community partnerships, and cultural respect are in our DNA.</p></div>
  <div class="nl-why-item"><div class="nl-why-icon">🍜</div><div class="nl-why-title">Authentic Vietnam</div><p class="nl-why-desc">Phở for €1.50, coffee workshops, market visits, local restaurants. Real Vietnam, not the tourist version.</p></div>
  <div class="nl-why-item"><div class="nl-why-icon">🤝</div><div class="nl-why-title">Small Group</div><p class="nl-why-desc">Intimate group travel means real friendships. We travel, eat, and explore together. Community that lasts.</p></div>
</div>'),
        ]),
    ]),

    // 7. GALLERY
    nl_section(nl_id($i++), [
        'background_background' => 'classic',
        'background_color'      => '#FAF5EC',
        'padding' => ['top' => '0', 'bottom' => '0', 'unit' => 'px', 'isLinked' => false],
    ], [
        nl_column(nl_id($i++), 100, [
            nl_html(nl_id($i++), '<style>
.nl-gallery{display:grid;grid-template-columns:2fr 1fr 1fr;gap:4px;}
.nl-gallery-photo{overflow:hidden;}
.nl-gallery-photo img{width:100%;height:100%;object-fit:cover;transition:transform .6s;display:block;}
.nl-gallery-photo:hover img{transform:scale(1.04);}
.nl-gallery-photo.tall{aspect-ratio:16/9;}
.nl-gallery-photo.short{aspect-ratio:4/3;}
.nl-gallery-col{display:flex;flex-direction:column;gap:4px;}
@media(max-width:768px){.nl-gallery{grid-template-columns:1fr;}}
</style>
<div class="nl-gallery">
  <div class="nl-gallery-photo tall"><img src="' . $photos . 'hero.jpg" alt="Trang An" loading="lazy"></div>
  <div class="nl-gallery-col">
    <div class="nl-gallery-photo short"><img src="' . $photos . 'garden.jpg" alt="Ninh Binh" loading="lazy"></div>
    <div class="nl-gallery-photo short"><img src="' . $photos . 'halong.jpg" alt="Ha Long" loading="lazy"></div>
  </div>
  <div class="nl-gallery-col">
    <div class="nl-gallery-photo short"><img src="' . $photos . 'tamcoc.jpg" alt="Tam Coc" loading="lazy"></div>
    <div class="nl-gallery-photo short"><img src="' . $photos . 'phuquoc.jpg" alt="Phu Quoc" loading="lazy"></div>
  </div>
</div>'),
        ]),
    ]),

    // 8. CTA
    nl_section(nl_id($i++), [
        'background_background' => 'classic',
        'background_image'              => ['url' => $photos . 'ninhbinh.jpg', 'id' => ''],
        'background_size'               => 'cover',
        'background_position'           => 'center 35%',
        'background_overlay_background' => 'classic',
        'background_overlay_color'      => 'rgba(20,10,4,0.76)',
        'background_overlay_opacity'    => ['unit' => 'px', 'size' => 1],
        'padding' => ['top' => '120', 'bottom' => '120', 'unit' => 'px', 'isLinked' => false],
    ], [
        nl_column(nl_id($i++), 100, [
            nl_html(nl_id($i++), '<div style="text-align:center;"><div style="font-size:11px;letter-spacing:.24em;text-transform:uppercase;color:#D4A030;font-weight:600;display:inline-flex;align-items:center;gap:10px;margin-bottom:20px;">Ready to Escape?</div></div>'),
            nl_heading(nl_id($i++), 'Find Your<br><em>Escape.</em>', 'h2', [
                'typography_font_family' => 'Cormorant Garamond',
                'typography_font_size'   => ['size' => 96, 'unit' => 'px'],
                'typography_font_weight' => '600',
                'typography_line_height' => ['size' => 0.92, 'unit' => 'em'],
                'title_color'            => '#FFFDF7',
                'align'                  => 'center',
            ]),
            nl_text(nl_id($i++), '<p style="font-size:17px;color:rgba(255,253,247,.7);max-width:420px;margin:0 auto;font-weight:300;line-height:1.75;text-align:center;">Browse our Vietnam escapes — past, present, and upcoming — and find the one that calls to you.</p>'),
            nl_spacer(nl_id($i++), 32),
            nl_html(nl_id($i++), '<div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;">
  <a href="/projects/" style="display:inline-flex;align-items:center;gap:10px;background:#B85520;color:#fff;font-weight:600;font-size:12px;letter-spacing:.12em;text-transform:uppercase;padding:14px 32px;border-radius:3px;text-decoration:none;">See All Projects →</a>
  <a href="mailto:contact@nonlaescape.com" style="font-size:12px;letter-spacing:.12em;text-transform:uppercase;font-weight:500;color:rgba(255,253,247,.6);border-bottom:1px solid rgba(255,253,247,.25);padding:14px 4px;text-decoration:none;">contact@nonlaescape.com</a>
</div>'),
        ], ['_element_custom_width' => ['size' => 75, 'unit' => '%']]),
    ]),

    // 9. FOOTER
    nl_section(nl_id($i++), [
        'background_background' => 'classic',
        'background_color'      => '#1A0E06',
        'padding' => ['top' => '60', 'bottom' => '36', 'unit' => 'px', 'isLinked' => false],
        'gap' => 'wider',
    ], [
        nl_column(nl_id($i++), 40, [
            nl_html(nl_id($i++), '<div style="font-family:Bebas Neue,sans-serif;font-size:22px;letter-spacing:.1em;color:#F2E8D4;margin-bottom:14px;">NONLA<span style="color:#D4A030;">.</span></div>
<p style="font-size:13px;color:rgba(242,232,212,.5);line-height:1.7;max-width:260px;font-weight:300;">Asociația Nonla Cultural Escape — Romanian NGO dedicated to cultural exchange and sustainable travel.<br><br>CIF: 49845083 · Câmpulung Muscel, Argeș County.</p>'),
        ]),
        nl_column(nl_id($i++), 30, [
            nl_html(nl_id($i++), '<div style="font-size:10px;font-weight:700;letter-spacing:.22em;text-transform:uppercase;color:rgba(242,232,212,.38);margin-bottom:18px;">Navigate</div>
<ul style="list-style:none;padding:0;display:flex;flex-direction:column;gap:11px;">
  <li><a href="#about" style="font-size:13px;color:rgba(242,232,212,.52);text-decoration:none;">About</a></li>
  <li><a href="/projects/" style="font-size:13px;color:rgba(242,232,212,.52);text-decoration:none;">Projects</a></li>
  <li><a href="/team/" style="font-size:13px;color:rgba(242,232,212,.52);text-decoration:none;">Team</a></li>
  <li><a href="/legal/" style="font-size:13px;color:rgba(242,232,212,.52);text-decoration:none;">Legal</a></li>
</ul>'),
        ]),
        nl_column(nl_id($i++), 30, [
            nl_html(nl_id($i++), '<div style="font-size:10px;font-weight:700;letter-spacing:.22em;text-transform:uppercase;color:rgba(242,232,212,.38);margin-bottom:18px;">Contact</div>
<ul style="list-style:none;padding:0;display:flex;flex-direction:column;gap:11px;">
  <li><a href="mailto:contact@nonlaescape.com" style="font-size:13px;color:rgba(242,232,212,.52);text-decoration:none;">contact@nonlaescape.com</a></li>
</ul>'),
        ]),
    ]),
];

nl_inject('home', $home_data);

WP_CLI::success('All pages injected. Go to http://nonla-local.local to preview.');
