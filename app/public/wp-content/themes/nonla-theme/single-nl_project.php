<?php
// Project detail page template — rendered for each nl_project CPT entry.
// Narrative-first order: Hero → About → Purpose → Experience → Project Support →
// (Itinerary + Accommodations, relocated) → Participation → Who Is This For → Final CTA.
// Every narrative section is wrapped in an if() so projects with empty fields
// degrade gracefully (no blank headers). All content is owner-editable in wp-admin.
get_header();

$pid = get_the_ID();

$series_labels = [
    'vietnam-escape'       => 'Vietnam Escape',
    'vietnam-undiscovered' => 'Vietnam Undiscovered',
];

$series_slug = get_field('series', $pid) ?: 'vietnam-escape';
$series      = $series_labels[$series_slug] ?? 'Vietnam Escape';
$edition     = get_field('edition', $pid);
$year        = get_field('year', $pid);
$status      = get_field('status', $pid) ?: 'upcoming';
$dates_en    = get_field('dates_en', $pid);
$dates_ro    = get_field('dates_ro', $pid);
$dur_en      = get_field('duration_en', $pid);
$dur_ro      = get_field('duration_ro', $pid);
$price       = get_field('price', $pid);
$note_en     = get_field('price_note_en', $pid);
$note_ro     = get_field('price_note_ro', $pid);
$desc_en     = get_field('desc_en', $pid);
$desc_ro     = get_field('desc_ro', $pid);
$cover       = get_field('cover_image', $pid);
$raw_dests   = get_field('destinations', $pid);
$dests       = $raw_dests ? array_map('trim', explode(',', $raw_dests)) : [];
$reg_url     = get_field('register_form_url', $pid) ?: get_option('nonla_registration_url', 'https://docs.google.com/forms/d/e/1FAIpQLScaiRzBL7j7vd7o2TVD7zEXSXCG7ObDLJvfEtXsygLuM_dCIQ/viewform');
$contact     = get_option('nonla_contact_email', 'nonlaescape@gmail.com');
$ig_url      = 'https://www.instagram.com/nonlaescape/';
$fb_url      = 'https://www.facebook.com/nonlaescape/';

// ── Narrative fields (redesign — all optional, entered per project in wp-admin) ──
$group_size       = get_field('group_size', $pid);
$about_en         = get_field('about_en', $pid);
$about_ro         = get_field('about_ro', $pid);
$purpose_intro_en = get_field('purpose_intro_en', $pid);
$purpose_intro_ro = get_field('purpose_intro_ro', $pid);
$exp_intro_en     = get_field('experience_intro_en', $pid);
$exp_intro_ro     = get_field('experience_intro_ro', $pid);
$support_intro_en = get_field('support_intro_en', $pid);
$support_intro_ro = get_field('support_intro_ro', $pid);
$support_items_en = get_field('support_items_en', $pid);
$support_items_ro = get_field('support_items_ro', $pid);
$accomm_note_en   = get_field('accomm_note_en', $pid);
$accomm_note_ro   = get_field('accomm_note_ro', $pid);
$partic_note_en   = get_field('participation_note_en', $pid);
$partic_note_ro   = get_field('participation_note_ro', $pid);
$pdf_url          = get_field('presentation_pdf_url', $pid);
$audience_intro_en = get_field('audience_intro_en', $pid);
$audience_intro_ro = get_field('audience_intro_ro', $pid);
$audience_items_en = get_field('audience_items_en', $pid);
$audience_items_ro = get_field('audience_items_ro', $pid);
$cta_heading_en   = get_field('cta_heading_en', $pid);
$cta_heading_ro   = get_field('cta_heading_ro', $pid);
$cta_body_en      = get_field('cta_body_en', $pid);
$cta_body_ro      = get_field('cta_body_ro', $pid);

// Purpose cards — collect only the ones with a title filled in.
$purpose_cards = [];
for ($i = 1; $i <= 4; $i++) {
    $t_en = get_field("purpose_{$i}_title_en", $pid);
    if (!$t_en) continue;
    $purpose_cards[] = [
        'icon'     => get_field("purpose_{$i}_icon", $pid),
        'title_en' => $t_en,
        'title_ro' => get_field("purpose_{$i}_title_ro", $pid) ?: $t_en,
        'desc_en'  => get_field("purpose_{$i}_desc_en", $pid),
        'desc_ro'  => get_field("purpose_{$i}_desc_ro", $pid),
    ];
}

// Find next upcoming project for conditional CTA when this project is completed
$_next_upcoming = get_posts(['post_type' => 'nl_project', 'posts_per_page' => 1, 'meta_query' => [['key' => 'status', 'value' => 'upcoming']], 'no_found_rows' => true]);
$next_proj      = $_next_upcoming[0] ?? null;
$next_form_url  = $next_proj ? (get_field('register_form_url', $next_proj->ID) ?: $reg_url) : null;

$itinerary = get_posts([
    'post_type'      => 'nl_itinerary_day',
    'posts_per_page' => -1,
    'meta_query'     => [['key' => 'project', 'value' => $pid, 'compare' => '=']],
    'meta_key'       => 'day',
    'orderby'        => 'meta_value_num',
    'order'          => 'ASC',
    'no_found_rows'  => true,
]);

$accomm = get_posts([
    'post_type'      => 'nl_accommodation',
    'posts_per_page' => -1,
    'meta_query'     => [['key' => 'project', 'value' => $pid, 'compare' => '=']],
    'meta_key'       => 'display_order',
    'orderby'        => 'meta_value_num',
    'order'          => 'ASC',
    'no_found_rows'  => true,
]);

$experience = get_posts([
    'post_type'      => 'nl_experience_stop',
    'posts_per_page' => -1,
    'meta_query'     => [['key' => 'project', 'value' => $pid, 'compare' => '=']],
    'meta_key'       => 'display_order',
    'orderby'        => 'meta_value_num',
    'order'          => 'ASC',
    'no_found_rows'  => true,
]);

$edition_label = trim(($edition ?: '') . ($year ? ' · ' . $year : ''));

// Primary "apply" action — used in both the hero and the final CTA (§4.8).
if (!function_exists('nl_project_apply_button')) {
    function nl_project_apply_button($status, $reg_url, $next_form_url, $ig_url) {
        $arrow = '<svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M2.5 7.5h10M8.5 3.5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        ob_start();
        if ($status === 'upcoming'): ?>
          <a href="<?php echo esc_url($reg_url); ?>" target="_blank" rel="noopener" class="nl-btn-primary">
            <span class="t" data-en="Apply for the Programme" data-ro="Înscrie-te în Program">Apply for the Programme</span>
            <?php echo $arrow; ?>
          </a>
        <?php elseif ($next_form_url): ?>
          <a href="<?php echo esc_url($next_form_url); ?>" target="_blank" rel="noopener" class="nl-btn-primary">
            <span class="t" data-en="Register for Next Edition" data-ro="Înregistrează-te pentru Ediția Viitoare">Register for Next Edition</span>
            <?php echo $arrow; ?>
          </a>
        <?php else: ?>
          <a href="<?php echo esc_url($ig_url); ?>" target="_blank" rel="noopener" class="nl-btn-outline">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
            <span class="t" data-en="Follow on Instagram" data-ro="Urmărește pe Instagram">Follow on Instagram</span>
          </a>
        <?php endif;
        return ob_get_clean();
    }
}
?>

<div class="nl-project-detail">

  <!-- ── HERO / PAGE HEADER ───────────────────────────────────────────── -->
  <div class="nl-page-header nl-project-detail-header">
    <div class="nl-page-header-inner">
      <div class="nl-breadcrumb">
        <a href="<?php echo esc_url(home_url('/')); ?>">Nonla</a> /
        <a href="<?php echo esc_url(home_url('/projects')); ?>"><span class="t" data-en="Projects" data-ro="Proiecte">Projects</span></a> /
        <span><?php echo esc_html($series . ($edition_label ? ' · ' . $edition_label : '')); ?></span>
      </div>
      <div class="nl-project-detail-header-inner">
        <div class="nl-project-detail-header-text">
          <div class="nl-project-status-badge <?php echo esc_attr($status); ?>" style="position:static;margin-bottom:18px;display:inline-flex;">
            <span class="t"
                  data-en="<?php echo $status === 'completed' ? 'Completed' : 'Upcoming'; ?>"
                  data-ro="<?php echo $status === 'completed' ? 'Finalizat' : 'Viitor'; ?>">
              <?php echo $status === 'completed' ? 'Completed' : 'Upcoming'; ?>
            </span>
          </div>
          <h1 class="nl-page-title nl-reveal">
            <?php echo esc_html($series); ?><br>
            <?php if ($edition_label): ?><em><?php echo esc_html($edition_label); ?></em><?php endif; ?>
          </h1>
          <?php
          $fmt_en = nl_format_desc($desc_en);
          $fmt_ro = nl_format_desc($desc_ro ?: $desc_en);
          if ($fmt_en): ?>
          <div class="nl-page-subtitle nl-project-desc nl-reveal t"
               data-en="<?php echo esc_attr($fmt_en); ?>"
               data-ro="<?php echo esc_attr($fmt_ro); ?>">
            <?php echo $fmt_en; ?>
          </div>
          <?php endif; ?>
          <div class="nl-project-detail-meta nl-reveal">
            <?php if ($dur_en): ?>
            <div class="nl-detail-meta-item">
              <div class="nl-meta-label t" data-en="Duration" data-ro="Durată">Duration</div>
              <div class="nl-meta-value t"
                   data-en="<?php echo esc_attr($dur_en); ?>"
                   data-ro="<?php echo esc_attr($dur_ro ?: $dur_en); ?>">
                <?php echo esc_html($dur_en); ?>
              </div>
            </div>
            <?php endif; ?>
            <?php if ($dests): ?>
            <div class="nl-detail-meta-item">
              <div class="nl-meta-label t" data-en="Location" data-ro="Locație">Location</div>
              <div class="nl-meta-value"><?php echo esc_html(implode(' · ', $dests)); ?></div>
            </div>
            <?php endif; ?>
            <?php if ($group_size): ?>
            <div class="nl-detail-meta-item">
              <div class="nl-meta-label t" data-en="Group Size" data-ro="Grup">Group Size</div>
              <div class="nl-meta-value"><?php echo esc_html($group_size); ?></div>
            </div>
            <?php endif; ?>
          </div>
          <div class="nl-project-detail-actions nl-reveal">
            <a href="<?php echo esc_url(home_url('/projects')); ?>" class="nl-btn-outline">
              <span class="t" data-en="All Projects" data-ro="Toate Proiectele">All Projects</span>
            </a>
            <div class="nl-project-actions-right">
              <?php echo nl_project_apply_button($status, $reg_url, $next_form_url, $ig_url); ?>
              <div class="nl-project-social-links">
                <a href="<?php echo esc_url($ig_url); ?>" class="nl-project-social-link" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                </a>
                <a href="<?php echo esc_url($fb_url); ?>" class="nl-project-social-link" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                </a>
              </div>
            </div>
          </div>
        </div>
        <?php if ($cover): ?>
        <div class="nl-project-detail-cover nl-reveal">
          <img src="<?php echo esc_url($cover); ?>"
               alt="<?php echo esc_attr($series . ' ' . $edition_label); ?>"
               loading="eager">
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- ── ABOUT THE PROJECT ────────────────────────────────────────────── -->
  <?php
  $about_fmt_en = nl_format_desc($about_en);
  $about_fmt_ro = nl_format_desc($about_ro ?: $about_en);
  if ($about_fmt_en): ?>
  <section class="nl-detail-section nl-about-section">
    <div class="nl-detail-inner">
      <div class="nl-eyebrow nl-reveal">
        <span class="t" data-en="The Mission" data-ro="Misiunea">The Mission</span>
      </div>
      <h2 class="nl-section-title nl-reveal">
        <span class="t" data-en="About the" data-ro="Despre">About the</span><br>
        <em><span class="t" data-en="Project." data-ro="Proiect.">Project.</span></em>
      </h2>
      <div class="nl-about-body nl-reveal t"
           data-en="<?php echo esc_attr($about_fmt_en); ?>"
           data-ro="<?php echo esc_attr($about_fmt_ro); ?>">
        <?php echo $about_fmt_en; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ── PURPOSE ──────────────────────────────────────────────────────── -->
  <?php if ($purpose_cards): ?>
  <section class="nl-detail-section nl-purpose-section">
    <div class="nl-detail-inner">
      <div class="nl-eyebrow nl-reveal">
        <span class="t" data-en="Purpose" data-ro="Scop">Purpose</span>
      </div>
      <h2 class="nl-section-title nl-reveal">
        <span class="t" data-en="Why It" data-ro="De Ce">Why It</span><br>
        <em><span class="t" data-en="Matters." data-ro="Contează.">Matters.</span></em>
      </h2>
      <?php if ($purpose_intro_en): ?>
      <p class="nl-section-intro nl-reveal t"
         data-en="<?php echo esc_attr($purpose_intro_en); ?>"
         data-ro="<?php echo esc_attr($purpose_intro_ro ?: $purpose_intro_en); ?>">
        <?php echo esc_html($purpose_intro_en); ?>
      </p>
      <?php endif; ?>
      <div class="nl-why-grid nl-why-grid--4">
        <?php foreach ($purpose_cards as $c): ?>
        <div class="nl-why-item nl-reveal">
          <?php if ($c['icon']): ?><div class="nl-why-icon"><?php echo esc_html($c['icon']); ?></div><?php endif; ?>
          <div class="nl-why-title t"
               data-en="<?php echo esc_attr($c['title_en']); ?>"
               data-ro="<?php echo esc_attr($c['title_ro']); ?>">
            <?php echo esc_html($c['title_en']); ?>
          </div>
          <?php if ($c['desc_en']): ?>
          <p class="nl-why-desc t"
             data-en="<?php echo esc_attr($c['desc_en']); ?>"
             data-ro="<?php echo esc_attr($c['desc_ro'] ?: $c['desc_en']); ?>">
            <?php echo esc_html($c['desc_en']); ?>
          </p>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ── THE EXPERIENCE ───────────────────────────────────────────────── -->
  <?php if ($experience): ?>
  <section class="nl-detail-section nl-experience-section">
    <div class="nl-detail-inner">
      <div class="nl-eyebrow nl-reveal">
        <span class="t" data-en="The Experience" data-ro="Experiența">The Experience</span>
      </div>
      <h2 class="nl-section-title nl-reveal">
        <span class="t" data-en="What You'll" data-ro="Ce Vei">What You'll</span><br>
        <em><span class="t" data-en="Experience." data-ro="Trăi.">Experience.</span></em>
      </h2>
      <?php if ($exp_intro_en): ?>
      <p class="nl-section-intro nl-reveal t"
         data-en="<?php echo esc_attr($exp_intro_en); ?>"
         data-ro="<?php echo esc_attr($exp_intro_ro ?: $exp_intro_en); ?>">
        <?php echo esc_html($exp_intro_en); ?>
      </p>
      <?php endif; ?>
      <div class="nl-experience-grid">
        <?php foreach ($experience as $stop):
            $s_name  = $stop->post_title;
            $s_photo = get_field('photo', $stop->ID);
            $s_tag   = get_field('tag', $stop->ID);
            $blocks  = [
                ['en' => 'Why we chose it', 'ro' => 'De ce l-am ales',   'v_en' => get_field('why_en', $stop->ID),        'v_ro' => get_field('why_ro', $stop->ID)],
                ['en' => 'What you experience', 'ro' => 'Ce vei trăi',    'v_en' => get_field('experience_en', $stop->ID), 'v_ro' => get_field('experience_ro', $stop->ID)],
                ['en' => 'Cultural value', 'ro' => 'Valoare culturală',   'v_en' => get_field('value_en', $stop->ID),      'v_ro' => get_field('value_ro', $stop->ID)],
            ];
            ?>
        <div class="nl-experience-card nl-reveal">
          <?php if ($s_photo): ?>
          <div class="nl-experience-photo" style="background-image:url('<?php echo esc_url($s_photo); ?>')"></div>
          <?php endif; ?>
          <div class="nl-experience-body">
            <?php if ($s_tag): ?><div class="nl-experience-tag"><?php echo esc_html($s_tag); ?></div><?php endif; ?>
            <div class="nl-experience-name"><?php echo esc_html($s_name); ?></div>
            <?php foreach ($blocks as $b): if (!$b['v_en']) continue; ?>
            <div class="nl-experience-block">
              <div class="nl-experience-block-label t" data-en="<?php echo esc_attr($b['en']); ?>" data-ro="<?php echo esc_attr($b['ro']); ?>"><?php echo esc_html($b['en']); ?></div>
              <p class="nl-experience-block-text t"
                 data-en="<?php echo esc_attr($b['v_en']); ?>"
                 data-ro="<?php echo esc_attr($b['v_ro'] ?: $b['v_en']); ?>">
                <?php echo esc_html($b['v_en']); ?>
              </p>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ── PROJECT SUPPORT ──────────────────────────────────────────────── -->
  <?php
  $support_fmt_en = nl_format_desc($support_items_en);
  $support_fmt_ro = nl_format_desc($support_items_ro ?: $support_items_en);
  if ($support_intro_en || $support_fmt_en): ?>
  <section class="nl-detail-section nl-support-section">
    <div class="nl-detail-inner">
      <div class="nl-eyebrow nl-reveal">
        <span class="t" data-en="How We Support You" data-ro="Cum Te Sprijinim">How We Support You</span>
      </div>
      <h2 class="nl-section-title nl-reveal">
        <span class="t" data-en="Project" data-ro="Sprijin">Project</span><br>
        <em><span class="t" data-en="Support." data-ro="Complet.">Support.</span></em>
      </h2>
      <?php if ($support_intro_en): ?>
      <p class="nl-section-intro nl-reveal t"
         data-en="<?php echo esc_attr($support_intro_en); ?>"
         data-ro="<?php echo esc_attr($support_intro_ro ?: $support_intro_en); ?>">
        <?php echo esc_html($support_intro_en); ?>
      </p>
      <?php endif; ?>
      <?php if ($support_fmt_en): ?>
      <div class="nl-support-list nl-reveal t"
           data-en="<?php echo esc_attr($support_fmt_en); ?>"
           data-ro="<?php echo esc_attr($support_fmt_ro); ?>">
        <?php echo $support_fmt_en; ?>
      </div>
      <?php endif; ?>
    </div>
  </section>
  <?php endif; ?>

  <!-- ── PRACTICAL DETAIL: ITINERARY + ACCOMMODATIONS (relocated) ───────── -->
  <?php if ($itinerary || $accomm): ?>
  <section class="nl-detail-section nl-practical-section">
    <div class="nl-detail-inner">
      <p class="nl-practical-leadin nl-reveal t"
         data-en="For those who want the full practical picture:"
         data-ro="Pentru cei care vor toate detaliile practice:">
        For those who want the full practical picture:
      </p>
    </div>

    <?php if ($itinerary): ?>
    <div class="nl-detail-inner nl-itinerary-block">
      <div class="nl-eyebrow nl-reveal">
        <span class="t" data-en="Day by Day" data-ro="Zi cu Zi">Day by Day</span>
      </div>
      <h3 class="nl-section-title nl-section-title--sub nl-reveal">
        <span class="t" data-en="Daily" data-ro="Itinerar">Daily</span>
        <em><span class="t" data-en="Itinerary." data-ro="Zilnic.">Itinerary.</span></em>
      </h3>
      <div class="nl-itinerary-list">
        <?php foreach ($itinerary as $day):
            $day_num   = intval(get_field('day', $day->ID));
            $day_date  = get_field('date', $day->ID) ?: '';
            $title_en  = $day->post_title;
            $title_ro  = get_field('title_ro', $day->ID) ?: $title_en;
            $desc_en_d = get_field('desc_en', $day->ID) ?: '';
            $desc_ro_d = get_field('desc_ro', $day->ID) ?: $desc_en_d;
            ?>
        <div class="nl-itinerary-day nl-reveal">
          <button class="nl-itin-head" onclick="nlToggleDay(this)" aria-expanded="false">
            <div class="nl-itin-day-num">
              <span class="t" data-en="Day" data-ro="Ziua">Day</span> <?php echo $day_num; ?>
            </div>
            <?php if ($day_date): ?>
            <div class="nl-itin-date"><?php echo esc_html($day_date); ?></div>
            <?php endif; ?>
            <div class="nl-itin-title t"
                 data-en="<?php echo esc_attr($title_en); ?>"
                 data-ro="<?php echo esc_attr($title_ro); ?>">
              <?php echo esc_html($title_en); ?>
            </div>
            <svg class="nl-itin-chevron" width="18" height="18" viewBox="0 0 18 18" fill="none">
              <path d="M4.5 6.75L9 11.25l4.5-4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <?php if ($desc_en_d): ?>
          <div class="nl-itin-body">
            <p class="t"
               data-en="<?php echo esc_attr($desc_en_d); ?>"
               data-ro="<?php echo esc_attr($desc_ro_d); ?>">
              <?php echo nl2br(esc_html($desc_en_d)); ?>
            </p>
          </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($accomm): ?>
    <div class="nl-detail-inner nl-accomm-block">
      <div class="nl-eyebrow nl-reveal">
        <span class="t" data-en="Where You Stay" data-ro="Unde Stai">Where You Stay</span>
      </div>
      <h3 class="nl-section-title nl-section-title--sub nl-reveal">
        <span class="t" data-en="Accommo-" data-ro="Cazare">Accommo-</span>
        <em><span class="t" data-en="dations." data-ro="Selecționată.">dations.</span></em>
      </h3>
      <?php if ($accomm_note_en): ?>
      <p class="nl-accomm-note nl-reveal t"
         data-en="<?php echo esc_attr($accomm_note_en); ?>"
         data-ro="<?php echo esc_attr($accomm_note_ro ?: $accomm_note_en); ?>">
        <?php echo esc_html($accomm_note_en); ?>
      </p>
      <?php endif; ?>
      <div class="nl-accomm-grid">
        <?php foreach ($accomm as $acc):
            $hotel       = $acc->post_title;
            $acc_city    = get_field('city', $acc->ID) ?: '';
            $acc_desc_en = get_field('desc_en', $acc->ID) ?: '';
            $acc_desc_ro = get_field('desc_ro', $acc->ID) ?: $acc_desc_en;
            $booking_url = get_field('booking_url', $acc->ID) ?: '';
            $acc_photo   = get_the_post_thumbnail_url($acc->ID, 'large');
            ?>
        <div class="nl-accomm-card nl-reveal">
          <?php if ($acc_photo): ?>
          <div class="nl-accomm-photo nl-accomm-photo-single">
            <img src="<?php echo esc_url($acc_photo); ?>" alt="<?php echo esc_attr($hotel); ?>" loading="lazy">
          </div>
          <?php endif; ?>
          <div class="nl-accomm-body">
            <div class="nl-accomm-city"><?php echo esc_html($acc_city); ?></div>
            <div class="nl-accomm-hotel"><?php echo esc_html($hotel); ?></div>
            <?php if ($acc_desc_en): ?>
            <p class="nl-accomm-desc t"
               data-en="<?php echo esc_attr($acc_desc_en); ?>"
               data-ro="<?php echo esc_attr($acc_desc_ro); ?>">
              <?php echo esc_html($acc_desc_en); ?>
            </p>
            <?php endif; ?>
            <?php if ($booking_url): ?>
            <a href="<?php echo esc_url($booking_url); ?>" target="_blank" rel="noopener" class="nl-accomm-link">
              <span class="t" data-en="View on Booking.com" data-ro="Vezi pe Booking.com">View on Booking.com</span>
              <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M2 6.5h9M7 2.5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </section>
  <?php endif; ?>

  <!-- ── PARTICIPATION CONTRIBUTION ───────────────────────────────────── -->
  <?php if ($partic_note_en || $price): ?>
  <section class="nl-detail-section nl-participation-section">
    <div class="nl-detail-inner nl-participation-inner">
      <div class="nl-eyebrow nl-reveal">
        <span class="t" data-en="Join Us" data-ro="Alătură-te">Join Us</span>
      </div>
      <h2 class="nl-section-title nl-reveal">
        <span class="t" data-en="Your" data-ro="Contribuția">Your</span><br>
        <em><span class="t" data-en="Contribution." data-ro="Ta.">Contribution.</span></em>
      </h2>
      <?php if ($partic_note_en): ?>
      <p class="nl-section-intro nl-reveal t"
         data-en="<?php echo esc_attr($partic_note_en); ?>"
         data-ro="<?php echo esc_attr($partic_note_ro ?: $partic_note_en); ?>">
        <?php echo esc_html($partic_note_en); ?>
      </p>
      <?php endif; ?>
      <?php if ($price): ?>
      <div class="nl-participation-amount nl-reveal"><?php echo esc_html($price); ?></div>
      <?php if ($note_en): ?>
      <div class="nl-participation-note nl-reveal t"
           data-en="<?php echo esc_attr($note_en); ?>"
           data-ro="<?php echo esc_attr($note_ro ?: $note_en); ?>">
        <?php echo esc_html($note_en); ?>
      </div>
      <?php endif; ?>
      <?php endif; ?>
      <?php if ($pdf_url): ?>
      <a href="<?php echo esc_url($pdf_url); ?>" target="_blank" rel="noopener" class="nl-accomm-link nl-reveal">
        <span class="t" data-en="Download Project Presentation" data-ro="Descarcă Prezentarea Proiectului">Download Project Presentation</span>
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M2 6.5h9M7 2.5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </a>
      <?php endif; ?>
    </div>
  </section>
  <?php endif; ?>

  <!-- ── WHO IS THIS FOR ──────────────────────────────────────────────── -->
  <?php
  $audience_fmt_en = nl_format_desc($audience_items_en);
  $audience_fmt_ro = nl_format_desc($audience_items_ro ?: $audience_items_en);
  if ($audience_intro_en || $audience_fmt_en): ?>
  <section class="nl-detail-section nl-audience-section">
    <div class="nl-detail-inner">
      <div class="nl-eyebrow nl-reveal">
        <span class="t" data-en="The Right Fit" data-ro="Pentru Tine">The Right Fit</span>
      </div>
      <h2 class="nl-section-title nl-reveal">
        <span class="t" data-en="Who Is This" data-ro="Pentru Cine">Who Is This</span><br>
        <em><span class="t" data-en="For?" data-ro="Este?">For?</span></em>
      </h2>
      <?php if ($audience_intro_en): ?>
      <p class="nl-section-intro nl-reveal t"
         data-en="<?php echo esc_attr($audience_intro_en); ?>"
         data-ro="<?php echo esc_attr($audience_intro_ro ?: $audience_intro_en); ?>">
        <?php echo esc_html($audience_intro_en); ?>
      </p>
      <?php endif; ?>
      <?php if ($audience_fmt_en): ?>
      <div class="nl-support-list nl-reveal t"
           data-en="<?php echo esc_attr($audience_fmt_en); ?>"
           data-ro="<?php echo esc_attr($audience_fmt_ro); ?>">
        <?php echo $audience_fmt_en; ?>
      </div>
      <?php endif; ?>
    </div>
  </section>
  <?php endif; ?>

  <!-- ── FINAL CTA ────────────────────────────────────────────────────── -->
  <?php if ($cta_heading_en): ?>
  <section class="nl-detail-section nl-project-cta">
    <div class="nl-detail-inner nl-project-cta-inner">
      <h2 class="nl-reveal t"
          data-en="<?php echo esc_attr($cta_heading_en); ?>"
          data-ro="<?php echo esc_attr($cta_heading_ro ?: $cta_heading_en); ?>">
        <?php echo esc_html($cta_heading_en); ?>
      </h2>
      <?php if ($cta_body_en): ?>
      <p class="nl-reveal t"
         data-en="<?php echo esc_attr($cta_body_en); ?>"
         data-ro="<?php echo esc_attr($cta_body_ro ?: $cta_body_en); ?>">
        <?php echo esc_html($cta_body_en); ?>
      </p>
      <?php endif; ?>
      <div class="nl-project-cta-actions nl-reveal">
        <?php echo nl_project_apply_button($status, $reg_url, $next_form_url, $ig_url); ?>
        <a href="<?php echo esc_url(home_url('/projects')); ?>" class="nl-btn-outline nl-btn-outline--light">
          <span class="t" data-en="All Projects" data-ro="Toate Proiectele">All Projects</span>
        </a>
      </div>
    </div>
  </section>
  <?php else: ?>
  <!-- ── BACK CTA (fallback when no Final CTA content) ────────────────── -->
  <div class="nl-project-detail-back">
    <div class="nl-detail-inner" style="text-align:center;padding:60px 52px;">
      <a href="<?php echo esc_url(home_url('/projects')); ?>" class="nl-btn-outline">
        ← <span class="t" data-en="All Projects" data-ro="Toate Proiectele">All Projects</span>
      </a>
    </div>
  </div>
  <?php endif; ?>

</div>

<script>
function nlToggleDay(btn) {
  var body = btn.nextElementSibling;
  var expanded = btn.getAttribute('aria-expanded') === 'true';
  btn.setAttribute('aria-expanded', !expanded);
  if (body) body.style.maxHeight = expanded ? null : body.scrollHeight + 'px';
  btn.classList.toggle('open', !expanded);
}
</script>

<?php get_footer(); ?>
