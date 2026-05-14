<?php
/**
 * Injects Elementor page data for all 4 Nonla pages.
 * Run via: wp eval-file inject-elementor.php
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

// ── VARIABLES ─────────────────────────────────────────────────────────────────

$photos       = get_stylesheet_directory_uri() . '/assets/photos/';
$videos       = get_stylesheet_directory_uri() . '/assets/videos/';
$home_url     = home_url('/');
$projects_url = home_url('/projects');
$team_url     = home_url('/team');
$legal_url    = home_url('/legal');
$contact_email = get_option('nonla_contact_email', 'contact@nonlaescape.com');
$reg_url      = get_option('nonla_registration_url', 'https://docs.google.com/forms/d/e/1FAIpQLScaiRzBL7j7vd7o2TVD7zEXSXCG7ObDLJvfEtXsygLuM_dCIQ/viewform');

$arrow = '<svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M2.5 7.5h10M8.5 3.5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$arrow_sm = '<svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 7h10M7.5 2.5l4.5 4.5-4.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

$i = 1;

// ═══════════════════════════════════════════════════════════════════════════════
// HOME PAGE
// ═══════════════════════════════════════════════════════════════════════════════

$hero_html = <<<HTML
<section class="nl-hero">
  <video class="nl-hero-video" autoplay muted loop playsinline preload="auto">
    <source src="{$videos}hero-video-web.mp4" type="video/mp4">
  </video>
  <div class="nl-hero-overlay"></div>
  <div class="nl-hero-content">
    <div class="nl-hero-eyebrow">
      <span class="t" data-en="Escape the ordinary. Discover the unknown." data-ro="Evadează din obișnuit. Descoperă necunoscutul.">Escape the ordinary. Discover the unknown.</span>
    </div>
    <h1>
      <span class="t" data-en="Cultural" data-ro="Călătorii">Cultural</span><br>
      <em><span class="t" data-en="Escapes" data-ro="Culturale">Escapes</span></em><br>
      <span class="t" data-en="Through Vietnam" data-ro="Prin Vietnam">Through Vietnam</span>
    </h1>
    <div class="nl-hero-row">
      <div>
        <p class="nl-hero-sub t"
           data-en="Nonla organizes immersive small-group trips to Vietnam — authentic experiences designed around real connection with the country and its people."
           data-ro="Nonla organizează experiențe de grup în Vietnam — autentice, menite să creeze conexiuni reale cu oamenii și locurile pe care le vizitezi.">
          Nonla organizes immersive small-group trips to Vietnam — authentic experiences designed around real connection with the country and its people.
        </p>
        <div class="nl-hero-actions">
          <a href="{$projects_url}" class="nl-btn-primary">
            <span class="t" data-en="See Our Projects" data-ro="Vezi Proiectele">See Our Projects</span>
            {$arrow}
          </a>
          <a href="{$home_url}#about" class="nl-btn-ghost-light">
            <span class="t" data-en="Learn About Us" data-ro="Despre Noi">Learn About Us</span>
          </a>
        </div>
      </div>
      <div class="nl-hero-stats">
        <div>
          <div class="nl-stat-n">5+</div>
          <div class="nl-stat-l t" data-en="Years" data-ro="Ani">Years</div>
        </div>
        <div>
          <div class="nl-stat-n">5</div>
          <div class="nl-stat-l t" data-en="Editions" data-ro="Ediții">Editions</div>
        </div>
        <div>
          <div class="nl-stat-n">100+</div>
          <div class="nl-stat-l t" data-en="Travelers" data-ro="Participanți">Travelers</div>
        </div>
      </div>
    </div>
  </div>
</section>
HTML;

$ticker_items = [
    ['Vietnam Escape',        'Vietnam Escape'],
    ['Vietnam Undiscovered',  'Vietnam Nedescoperit'],
    ['Small Groups',          'Grupuri Mici'],
    ['Authentic Experiences', 'Experiențe Autentice'],
    ['Expert Coordinators',   'Coordonatori Experți'],
    ['All-Inclusive',         'Totul Inclus'],
    ['Cultural Travel',       'Călătorii Culturale'],
    ['Since 2020',            'Din 2020'],
];
$ticker_inner = '';
foreach (array_merge($ticker_items, $ticker_items) as [$en, $ro]) {
    $ticker_inner .= '<span class="nl-ticker-item"><span class="nl-ticker-sep"></span>'
        . '<span class="t" data-en="' . esc_attr($en) . '" data-ro="' . esc_attr($ro) . '">' . esc_html($en) . '</span>'
        . '</span>';
}
$ticker_html = '<div class="nl-ticker-wrap"><div class="nl-ticker">' . $ticker_inner . '</div>[nl_countdown]</div>';

$about_html = <<<HTML
<section class="nl-about-wrap" id="about">
  <div class="nl-section-inner">
    <div class="nl-about-grid">
      <div class="nl-about-photo-stack nl-reveal">
        <img class="nl-about-photo-main" src="{$photos}tamcoc.jpg" alt="Tam Coc, Ninh Binh">
        <img class="nl-about-photo-float" src="{$photos}boats.jpg" alt="Local boats">
      </div>
      <div class="nl-about-text">
        <div class="nl-eyebrow nl-reveal">
          <span class="t" data-en="Who We Are" data-ro="Cine Suntem">Who We Are</span>
        </div>
        <h2 class="nl-section-title nl-reveal nl-d1">
          <span class="t" data-en="More Than" data-ro="Mai Mult">More Than</span><br>
          <em><span class="t" data-en="a Trip." data-ro="Decât O Călătorie.">a Trip.</span></em>
        </h2>
        <p class="nl-reveal nl-d2 t"
           data-en="Nonla Cultural Escape is a non-governmental organization founded in Romania with a global vision — offering a safe and inclusive space for cultural exchange and personal growth."
           data-ro="Nonla Cultural Escape este o organizație neguvernamentală fondată în România cu o viziune globală — oferind un spațiu sigur și incluziv pentru schimb cultural și dezvoltare personală.">
          Nonla Cultural Escape is a non-governmental organization founded in Romania with a global vision — offering a safe and inclusive space for cultural exchange and personal growth.
        </p>
        <p class="nl-reveal nl-d3 t"
           data-en="We organize small-group trips to Vietnam with everything handled — flights, accommodation, transfers, and expert coordinators who speak Romanian, English, and Vietnamese. Our mission: create meaningful connections across borders."
           data-ro="Organizăm călătorii în grup mic în Vietnam cu totul inclus — zboruri, cazare, transferuri și coordonatori experți care vorbesc română, engleză și vietnameză. Misiunea noastră: conexiuni autentice peste granițe.">
          We organize small-group trips to Vietnam with everything handled — flights, accommodation, transfers, and expert coordinators who speak Romanian, English, and Vietnamese. Our mission: create meaningful connections across borders.
        </p>
        <blockquote class="nl-about-quote nl-reveal nl-d4">
          <span class="t"
                data-en="Building connections through shared adventures and creating a supportive environment for everyone."
                data-ro="Construim conexiuni prin aventuri comune și creăm un mediu de susținere pentru toată lumea.">
            Building connections through shared adventures and creating a supportive environment for everyone.
          </span>
        </blockquote>
      </div>
    </div>
  </div>
</section>
HTML;

$series_html = <<<HTML
<section class="nl-series-wrap">
  <div class="nl-section-inner">
    <div class="nl-eyebrow nl-reveal">
      <span class="t" data-en="Our Escapes" data-ro="Călătoriile Noastre">Our Escapes</span>
    </div>
    <h2 class="nl-section-title nl-reveal nl-d1">
      <span class="t" data-en="Two Series." data-ro="Două Serii.">Two Series.</span><br>
      <em><span class="t" data-en="One Vietnam." data-ro="Un Vietnam.">One Vietnam.</span></em>
    </h2>
    [nl_series]
  </div>
</section>
HTML;

$dest_html = <<<HTML
<section class="nl-dest-wrap" id="destinations">
  <div class="nl-dest-header">
    <div class="nl-eyebrow nl-reveal">
      <span class="t" data-en="Where We Go" data-ro="Unde Mergem">Where We Go</span>
    </div>
    <h2 class="nl-section-title nl-reveal nl-d1" style="margin-bottom:0;">
      <span class="t" data-en="Places That Stay" data-ro="Locuri Care Rămân">Places That Stay</span><br>
      <em><span class="t" data-en="With You." data-ro="Cu Tine.">With You.</span></em>
    </h2>
  </div>
  [nl_places]
</section>
HTML;

$why_html = <<<HTML
<section class="nl-why-wrap" id="why">
  <div class="nl-section-inner">
    <div class="nl-eyebrow nl-reveal">
      <span class="t" data-en="Why Nonla" data-ro="De Ce Nonla">Why Nonla</span>
    </div>
    <h2 class="nl-section-title nl-reveal nl-d1">
      <span class="t" data-en="The Difference" data-ro="Diferența">The Difference</span><br>
      <em><span class="t" data-en="You'll Feel." data-ro="Pe Care O Vei Simți.">You'll Feel.</span></em>
    </h2>
    [nl_why_items]
  </div>
</section>
HTML;

$testimonials_html = <<<HTML
<section class="nl-testimonials-wrap">
  <div class="nl-section-inner">
    <div class="nl-eyebrow nl-reveal">
      <span class="t" data-en="What Travelers Say" data-ro="Ce Spun Călătorii">What Travelers Say</span>
    </div>
    <h2 class="nl-section-title nl-reveal nl-d1">
      <span class="t" data-en="Their" data-ro="Povestea">Their</span><br>
      <em><span class="t" data-en="Stories." data-ro="Lor.">Stories.</span></em>
    </h2>
    [nl_testimonials]
  </div>
</section>
HTML;

$gallery_html = <<<HTML
<div class="nl-gallery-wrap">
  [nl_gallery_items]
</div>
HTML;

$cta_html = <<<HTML
<div class="nl-cta-wrap" id="contact" style="background-image:url('{$photos}ninhbinh.jpg');">
  <div class="nl-cta-overlay"></div>
  <div class="nl-cta-inner">
    <div class="nl-eyebrow nl-eyebrow-center nl-eyebrow-gold nl-reveal">
      <span class="t" data-en="Ready to Escape?" data-ro="Gata să Evadezi?">Ready to Escape?</span>
    </div>
    <h2 class="nl-reveal nl-d1">
      <span class="t" data-en="Find Your" data-ro="Găsește-ți">Find Your</span><br>
      <em><span class="t" data-en="Escape." data-ro="Călătorie.">Escape.</span></em>
    </h2>
    <p class="nl-reveal nl-d2 t"
       data-en="Browse our Vietnam escapes — past, present, and upcoming — and find the one that calls to you."
       data-ro="Explorează călătoriile noastre din Vietnam — trecute, prezente și viitoare — și găsește-o pe cea care te cheamă.">
      Browse our Vietnam escapes — past, present, and upcoming — and find the one that calls to you.
    </p>
    <div class="nl-cta-actions nl-reveal nl-d3">
      <a href="{$projects_url}" class="nl-btn-primary">
        <span class="t" data-en="See All Projects" data-ro="Vezi Toate Proiectele">See All Projects</span>
        {$arrow}
      </a>
      <a href="mailto:{$contact_email}" class="nl-btn-ghost-light">{$contact_email}</a>
    </div>
  </div>
</div>
HTML;

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
// PROJECTS PAGE
// ═══════════════════════════════════════════════════════════════════════════════

$proj_header_html = <<<HTML
<div class="nl-page-header">
  <div class="nl-page-header-inner">
    <div class="nl-breadcrumb">
      <a href="{$home_url}">Nonla</a> / <span class="t" data-en="Projects" data-ro="Proiecte">Projects</span>
    </div>
    <h1 class="nl-page-title nl-reveal">
      <span class="t" data-en="Our" data-ro="Călătoriile">Our</span><br>
      <em><span class="t" data-en="Escapes." data-ro="Noastre.">Escapes.</span></em>
    </h1>
    <p class="nl-page-subtitle nl-reveal t"
       data-en="Every edition of Vietnam Escape and Vietnam Undiscovered — past, present, and upcoming. Find the one that calls to you."
       data-ro="Fiecare ediție a Vietnam Escape și Vietnam Undiscovered — trecute, prezente și viitoare. Găsește-o pe cea care te cheamă.">
      Every edition of Vietnam Escape and Vietnam Undiscovered — past, present, and upcoming. Find the one that calls to you.
    </p>
  </div>
</div>
HTML;

$proj_data = [
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $proj_header_html)], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_sc(nl_id($i++), '[nonla_projects]')], nl_col_settings())]),
];

// ═══════════════════════════════════════════════════════════════════════════════
// TEAM PAGE
// ═══════════════════════════════════════════════════════════════════════════════

$team_header_html = <<<HTML
<div class="nl-page-header">
  <div class="nl-page-header-inner">
    <div class="nl-breadcrumb">
      <a href="{$home_url}">Nonla</a> / <span class="t" data-en="Team" data-ro="Echipă">Team</span>
    </div>
    <h1 class="nl-page-title nl-reveal">
      <span class="t" data-en="The People" data-ro="Oamenii">The People</span><br>
      <em><span class="t" data-en="Behind the Escapes." data-ro="Din Spatele Călătoriilor.">Behind the Escapes.</span></em>
    </h1>
    <p class="nl-page-subtitle nl-reveal t"
       data-en="Meet the coordinators who make it all happen — on the ground in Vietnam, every single day of the escape."
       data-ro="Cunoaște coordonatorii care fac totul posibil — pe teren în Vietnam, în fiecare zi a călătoriei.">
      Meet the coordinators who make it all happen — on the ground in Vietnam, every single day of the escape.
    </p>
  </div>
</div>
HTML;

$team_cta_html = <<<HTML
<div class="nl-team-cta">
  <div class="nl-team-cta-inner">
    <div class="nl-eyebrow nl-eyebrow-center nl-reveal">
      <span class="t" data-en="Our Approach" data-ro="Abordarea Noastră">Our Approach</span>
    </div>
    <h2 class="nl-reveal nl-d1">
      <span class="t" data-en="We Travel" data-ro="Călătorim">We Travel</span><br>
      <em><span class="t" data-en="With You." data-ro="Cu Voi.">With You.</span></em>
    </h2>
    <p class="nl-reveal nl-d2 t"
       data-en="Our coordinators aren't just guides — they're travel companions who speak your language, know Vietnam deeply, and are with you from the first flight to the last goodbye."
       data-ro="Coordonatorii noștri nu sunt doar ghizi — sunt însoțitori de călătorie care vorbesc limba ta, cunosc Vietnam în profunzime și sunt alături de tine de la primul zbor până la ultimul rămas bun.">
      Our coordinators aren't just guides — they're travel companions who speak your language, know Vietnam deeply, and are with you from the first flight to the last goodbye.
    </p>
    <a href="{$projects_url}" class="nl-btn-primary nl-reveal nl-d3">
      <span class="t" data-en="See Our Escapes" data-ro="Vezi Călătoriile Noastre">See Our Escapes</span>
      {$arrow}
    </a>
  </div>
</div>
HTML;

$team_data = [
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $team_header_html)], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_sc(nl_id($i++), '[nonla_team]')], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $team_cta_html)], nl_col_settings())]),
];

// ═══════════════════════════════════════════════════════════════════════════════
// LEGAL PAGE
// ═══════════════════════════════════════════════════════════════════════════════

$legal_header_html = <<<HTML
<div class="nl-page-header">
  <div class="nl-page-header-inner">
    <div class="nl-breadcrumb">
      <a href="{$home_url}">Nonla</a> / <span class="t" data-en="Legal Notice" data-ro="Mențiuni Legale">Legal Notice</span>
    </div>
    <h1 class="nl-page-title nl-reveal">
      <span class="t" data-en="Legal" data-ro="Mențiuni">Legal</span><br>
      <em><span class="t" data-en="Notice." data-ro="Legale.">Notice.</span></em>
    </h1>
    <p class="nl-page-subtitle nl-reveal t"
       data-en="Organization details, terms of service, and legal information for Asociația Nonla Cultural Escape."
       data-ro="Detalii despre organizație, termenii serviciului și informații legale pentru Asociația Nonla Cultural Escape.">
      Organization details, terms of service, and legal information for Asociația Nonla Cultural Escape.
    </p>
  </div>
</div>
HTML;

$legal_content_html = <<<HTML
<div class="nl-legal-wrap">
  <h2>About the Organization</h2>
  <p><strong>Asociația Nonla Cultural Escape</strong> is a Romanian non-governmental organization (NGO) registered under Romanian law, dedicated to cultural exchange and sustainable travel.</p>
  <ul>
    <li><strong>Legal name:</strong> Asociația Nonla Cultural Escape</li>
    <li><strong>CIF (Tax ID):</strong> 49845083</li>
    <li><strong>Registered address:</strong> Câmpulung Muscel, Argeș County, Romania</li>
    <li><strong>Contact:</strong> {$contact_email}</li>
  </ul>

  <h2>Services</h2>
  <p>Nonla Cultural Escape organizes small-group cultural travel experiences to Vietnam. All trips are all-inclusive packages covering flights, accommodation, transfers, and guided experiences.</p>

  <h2>Booking &amp; Payments</h2>
  <p>Reservations are confirmed upon receipt of a deposit. Full payment terms are communicated at the time of booking. Nonla reserves the right to cancel or modify itineraries due to force majeure events.</p>

  <h2>Privacy &amp; Data</h2>
  <p>We collect only the personal data necessary to organize your trip (name, contact details, travel documents). Data is stored securely and never sold to third parties. You may request access to or deletion of your data at any time by contacting us at {$contact_email}.</p>
  <p>This site uses cookies for basic analytics only. No advertising cookies are used.</p>

  <h2>Liability</h2>
  <p>Nonla Cultural Escape acts as an organizer and coordinator. We work with vetted local partners for accommodation and transport. While every care is taken, Nonla is not liable for events beyond our control including weather, political events, or acts of force majeure.</p>

  <h2>Intellectual Property</h2>
  <p>All content on this website — text, photos, and design — is the property of Asociația Nonla Cultural Escape unless otherwise stated. Reproduction without written permission is prohibited.</p>

  <h2>Governing Law</h2>
  <p>These terms are governed by Romanian law. Any disputes shall be resolved in the courts of Argeș County, Romania.</p>
</div>
HTML;

$legal_data = [
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $legal_header_html)], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(['background_background' => 'classic', 'background_color' => '#FAF5EC']), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $legal_content_html)], nl_col_settings())]),
];

// ═══════════════════════════════════════════════════════════════════════════════
// INJECT ALL PAGES
// ═══════════════════════════════════════════════════════════════════════════════

// Home page — create if needed, set as front page
$home_page = nl_get_or_create('home', 'Home');
update_option('page_on_front', $home_page->ID);
update_option('show_on_front', 'page');
nl_inject($home_page, $home_data);

nl_inject(nl_get_or_create('projects', 'Projects'), $proj_data);
nl_inject(nl_get_or_create('team', 'Team'), $team_data);
nl_inject(nl_get_or_create('legal', 'Legal Notice'), $legal_data);

WP_CLI::success("All pages injected. Run: wp elementor flush-css");
