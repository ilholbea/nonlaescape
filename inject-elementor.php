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
<section class="nl-hero" id="about">
  <div class="nl-hero-photo" style="background-image:url('{$photos}hero.jpg');"></div>
  <div class="nl-hero-overlay"></div>
  <div class="nl-hero-content">
    <div class="nl-hero-eyebrow">
      <span class="t" data-en="Escape the ordinary. Discover the unknown." data-ro="Evadează din obișnuit. Descoperă necunoscutul.">Escape the ordinary. Discover the unknown.</span>
    </div>
    <h1>
      <span class="t" data-en="Cultural" data-ro="Escapade">Cultural</span><br>
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

$ticker_items = ['Vietnam Escape', 'Vietnam Undiscovered', 'ONG Românesc', 'Grupuri Mici', 'Authentic Experiences', 'Expert Coordinators', 'All-Inclusive', 'Zboruri Incluse'];
$ticker_inner = '';
foreach (array_merge($ticker_items, $ticker_items) as $item) {
    $ticker_inner .= '<span class="nl-ticker-item"><span class="nl-ticker-sep"></span>' . esc_html($item) . '</span>';
}
$ticker_html = '<div class="nl-ticker-wrap"><div class="nl-ticker">' . $ticker_inner . '</div></div>';

$about_html = <<<HTML
<section class="nl-about-wrap">
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
      <span class="t" data-en="Our Escapes" data-ro="Escapadele Noastre">Our Escapes</span>
    </div>
    <h2 class="nl-section-title nl-reveal nl-d1">
      <span class="t" data-en="Two Series." data-ro="Două Serii.">Two Series.</span><br>
      <em><span class="t" data-en="One Vietnam." data-ro="Un Vietnam.">One Vietnam.</span></em>
    </h2>
    <div class="nl-series-grid">
      <div class="nl-series-card nl-reveal nl-d2">
        <div class="nl-series-label">
          <span class="t" data-en="Annual Group Escape" data-ro="Escapadă Anuală de Grup">Annual Group Escape</span>
        </div>
        <div class="nl-series-name">Vietnam Escape</div>
        <p class="nl-series-desc t"
           data-en="Our flagship annual escape — 15 days through Vietnam's iconic destinations. From Hanoi's ancient streets to Phu Quoc's tropical waters, everything is included: flights, hotels, transfers, and daily guided experiences."
           data-ro="Escapada noastră anuală emblematică — 15 zile prin destinațiile iconice din Vietnam. De la străzile antice ale Hanoiului la apele tropicale din Phu Quoc, totul este inclus: zboruri, hoteluri, transferuri și experiențe ghidate zilnic.">
          Our flagship annual escape — 15 days through Vietnam's iconic destinations. From Hanoi's ancient streets to Phu Quoc's tropical waters, everything is included: flights, hotels, transfers, and daily guided experiences.
        </p>
        <a href="{$projects_url}" class="nl-series-link">
          <span class="t" data-en="See Editions" data-ro="Vezi Edițiile">See Editions</span>
          {$arrow_sm}
        </a>
      </div>
      <div class="nl-series-card nl-reveal nl-d3">
        <div class="nl-series-label">
          <span class="t" data-en="Off The Beaten Path" data-ro="Dincolo de Traseele Bătătorite">Off The Beaten Path</span>
        </div>
        <div class="nl-series-name">Vietnam Undiscovered</div>
        <p class="nl-series-desc t"
           data-en="For those who want to go deeper. Remote villages, hidden valleys, and lesser-known corners of Vietnam that most travelers never reach. More adventurous, more intimate."
           data-ro="Pentru cei care vor să meargă mai adânc. Sate îndepărtate, văi ascunse și colțuri mai puțin cunoscute din Vietnam pe care cei mai mulți nu le văd niciodată. Mai aventuros, mai intim.">
          For those who want to go deeper. Remote villages, hidden valleys, and lesser-known corners of Vietnam that most travelers never reach. More adventurous, more intimate.
        </p>
        <a href="{$projects_url}" class="nl-series-link">
          <span class="t" data-en="See Editions" data-ro="Vezi Edițiile">See Editions</span>
          {$arrow_sm}
        </a>
      </div>
    </div>
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
  <div class="nl-dest-grid">
    <div class="nl-dest-card">
      <img src="{$photos}hanoi.jpg" alt="Hanoi" loading="lazy">
      <div class="nl-dest-overlay">
        <div class="nl-dest-tag">Hanoi</div>
        <div class="nl-dest-name"><span class="t" data-en="Old Quarter" data-ro="Cartierul Vechi">Old Quarter</span></div>
        <div class="nl-dest-desc t" data-en="Ancient streets, Temple of Literature, street food &amp; Vietnamese coffee" data-ro="Străzi antice, Temple of Literature, mâncare stradală și cafea vietnameză">Ancient streets, Temple of Literature, street food &amp; Vietnamese coffee</div>
      </div>
    </div>
    <div class="nl-dest-card">
      <img src="{$photos}halong.jpg" alt="Ha Long Bay" loading="lazy">
      <div class="nl-dest-overlay">
        <div class="nl-dest-tag">Lan Ha Bay</div>
        <div class="nl-dest-name">Ha Long</div>
        <div class="nl-dest-desc t" data-en="UNESCO limestone karsts, emerald waters, boat trips &amp; kayaking" data-ro="Carști calcaroși UNESCO, ape smarald, excursii cu barca și kayaking">UNESCO limestone karsts, emerald waters, boat trips &amp; kayaking</div>
      </div>
    </div>
    <div class="nl-dest-card">
      <img src="{$photos}ninhbinh.jpg" alt="Ninh Binh" loading="lazy">
      <div class="nl-dest-overlay">
        <div class="nl-dest-tag">Ninh Binh</div>
        <div class="nl-dest-name">Trang An</div>
        <div class="nl-dest-desc t" data-en="River boats, Mua Cave, bicycle rides through endless rice paddies" data-ro="Bărci pe râu, Mua Cave, pedalat printre câmpuri nesfârșite de orez">River boats, Mua Cave, bicycle rides through endless rice paddies</div>
      </div>
    </div>
    <div class="nl-dest-card">
      <img src="{$photos}phuquoc.jpg" alt="Phu Quoc" loading="lazy">
      <div class="nl-dest-overlay">
        <div class="nl-dest-tag">Phu Quoc</div>
        <div class="nl-dest-name"><span class="t" data-en="Island Life" data-ro="Viață de Insulă">Island Life</span></div>
        <div class="nl-dest-desc t" data-en="Tropical island, snorkeling, boat tours, hiking &amp; pure beach days" data-ro="Insulă tropicală, snorkeling, tururi cu barca, drumeții și zile de plajă">Tropical island, snorkeling, boat tours, hiking &amp; pure beach days</div>
      </div>
    </div>
  </div>
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
    <div class="nl-why-grid">
      <div class="nl-why-item nl-reveal nl-d1">
        <div class="nl-why-icon">🧭</div>
        <div class="nl-why-title t" data-en="Local Expertise" data-ro="Expertiză Locală">Local Expertise</div>
        <p class="nl-why-desc t" data-en="2–4 coordinators speak Vietnamese, Romanian &amp; English. No language barrier, no wasted days." data-ro="2–4 coordonatori vorbesc vietnameză, română și engleză. Fără bariera lingvistică, fără zile pierdute.">2–4 coordinators speak Vietnamese, Romanian &amp; English. No language barrier, no wasted days.</p>
      </div>
      <div class="nl-why-item nl-reveal nl-d2">
        <div class="nl-why-icon">🏨</div>
        <div class="nl-why-title t" data-en="Vetted Stays" data-ro="Cazări Verificate">Vetted Stays</div>
        <p class="nl-why-desc t" data-en="Every property personally tested and rated 8.5+ on Booking.com. Comfort you can count on." data-ro="Fiecare proprietate testată personal și evaluată 8.5+ pe Booking.com. Confort garantat.">Every property personally tested and rated 8.5+ on Booking.com. Comfort you can count on.</p>
      </div>
      <div class="nl-why-item nl-reveal nl-d3">
        <div class="nl-why-icon">✈️</div>
        <div class="nl-why-title t" data-en="All-Inclusive" data-ro="All-Inclusive">All-Inclusive</div>
        <p class="nl-why-desc t" data-en="Flights, transfers, accommodation, daily breakfast and curated experiences. You just show up." data-ro="Zboruri, transferuri, cazare, mic dejun zilnic și experiențe curate. Tu trebuie doar să apari.">Flights, transfers, accommodation, daily breakfast and curated experiences. You just show up.</p>
      </div>
      <div class="nl-why-item nl-reveal nl-d1">
        <div class="nl-why-icon">🌿</div>
        <div class="nl-why-title t" data-en="NGO Organized" data-ro="Organizat de ONG">NGO Organized</div>
        <p class="nl-why-desc t" data-en="We're a certified NGO. Sustainable travel, community partnerships, and cultural respect are in our DNA." data-ro="Suntem un ONG certificat. Turismul sustenabil, parteneriatele comunitare și respectul cultural sunt în ADN-ul nostru.">We're a certified NGO. Sustainable travel, community partnerships, and cultural respect are in our DNA.</p>
      </div>
      <div class="nl-why-item nl-reveal nl-d2">
        <div class="nl-why-icon">🍜</div>
        <div class="nl-why-title t" data-en="Authentic Vietnam" data-ro="Vietnam Autentic">Authentic Vietnam</div>
        <p class="nl-why-desc t" data-en="Phở for €1.50, coffee workshops, market visits, local restaurants. Real Vietnam, not the tourist version." data-ro="Phở de 1,50€, ateliere de cafea, vizite la piețe, restaurante locale. Vietnam autentic, nu varianta turistică.">Phở for €1.50, coffee workshops, market visits, local restaurants. Real Vietnam, not the tourist version.</p>
      </div>
      <div class="nl-why-item nl-reveal nl-d3">
        <div class="nl-why-icon">🤝</div>
        <div class="nl-why-title t" data-en="Small Group" data-ro="Grup Mic">Small Group</div>
        <p class="nl-why-desc t" data-en="Intimate group travel means real friendships. We travel, eat, and explore together. Community that lasts." data-ro="Călătoria în grup mic înseamnă prietenii reale. Călătorim, mâncăm și explorăm împreună. O comunitate care durează.">Intimate group travel means real friendships. We travel, eat, and explore together. Community that lasts.</p>
      </div>
    </div>
  </div>
</section>
HTML;

$gallery_html = <<<HTML
<div class="nl-gallery-wrap">
  <div class="nl-gallery-row">
    <div class="nl-gallery-photo nl-gallery-photo-tall">
      <img src="{$photos}hero.jpg" alt="Trang An, Ninh Binh" loading="lazy">
    </div>
    <div class="nl-gallery-col">
      <div class="nl-gallery-photo nl-gallery-photo-short">
        <img src="{$photos}garden.jpg" alt="Ninh Binh Garden" loading="lazy">
      </div>
      <div class="nl-gallery-photo nl-gallery-photo-short">
        <img src="{$photos}halong.jpg" alt="Ha Long Bay" loading="lazy">
      </div>
    </div>
    <div class="nl-gallery-col">
      <div class="nl-gallery-photo nl-gallery-photo-short">
        <img src="{$photos}tamcoc.jpg" alt="Tam Coc" loading="lazy">
      </div>
      <div class="nl-gallery-photo nl-gallery-photo-short">
        <img src="{$photos}phuquoc.jpg" alt="Phu Quoc" loading="lazy">
      </div>
    </div>
  </div>
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
      <em><span class="t" data-en="Escape." data-ro="Escapada.">Escape.</span></em>
    </h2>
    <p class="nl-reveal nl-d2 t"
       data-en="Browse our Vietnam escapes — past, present, and upcoming — and find the one that calls to you."
       data-ro="Explorează escapadele noastre din Vietnam — trecute, prezente și viitoare — și găsește-o pe cea care te cheamă.">
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
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $hero_html)], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $ticker_html)], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $about_html)], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $series_html)], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $dest_html)], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $why_html)], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $gallery_html)], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $cta_html)], nl_col_settings())]),
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
      <span class="t" data-en="Our" data-ro="Escapadele">Our</span><br>
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
      <em><span class="t" data-en="Behind the Escapes." data-ro="Din Spatele Escapadelor.">Behind the Escapes.</span></em>
    </h1>
    <p class="nl-page-subtitle nl-reveal t"
       data-en="Meet the coordinators who make it all happen — on the ground in Vietnam, every single day of the escape."
       data-ro="Cunoaște coordonatorii care fac totul posibil — pe teren în Vietnam, în fiecare zi a escapadei.">
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
      <span class="t" data-en="See Our Escapes" data-ro="Vezi Escapadele Noastre">See Our Escapes</span>
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
