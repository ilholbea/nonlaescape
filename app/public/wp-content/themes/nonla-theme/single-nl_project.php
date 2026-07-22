<?php
// Project detail page template — rendered for each nl_project CPT entry.
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
$edition_label = trim(($edition ?: '') . ($year ? ' · ' . $year : ''));
?>

<div class="nl-project-detail">

  <!-- ── PAGE HEADER ──────────────────────────────────────────────── -->
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
            <?php if ($dates_en): ?>
            <div class="nl-detail-meta-item">
              <div class="nl-meta-label t" data-en="Dates" data-ro="Perioada">Dates</div>
              <div class="nl-meta-value t"
                   data-en="<?php echo esc_attr($dates_en); ?>"
                   data-ro="<?php echo esc_attr($dates_ro ?: $dates_en); ?>">
                <?php echo esc_html($dates_en); ?>
              </div>
            </div>
            <?php endif; ?>
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
            <?php if ($price): ?>
            <div class="nl-detail-meta-item">
              <div class="nl-meta-label t" data-en="Price" data-ro="Preț">Price</div>
              <div class="nl-meta-value nl-meta-price"><?php echo esc_html($price); ?></div>
              <?php if ($note_en): ?>
              <div class="nl-meta-note t"
                   data-en="<?php echo esc_attr($note_en); ?>"
                   data-ro="<?php echo esc_attr($note_ro ?: $note_en); ?>">
                <?php echo esc_html($note_en); ?>
              </div>
              <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
          <?php if ($dests): ?>
          <div class="nl-project-dests nl-reveal">
            <?php foreach ($dests as $d): ?>
            <span class="nl-dest-pill"><?php echo esc_html($d); ?></span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          <div class="nl-project-detail-actions nl-reveal">
            <a href="<?php echo esc_url(home_url('/projects')); ?>" class="nl-btn-outline">
              <span class="t" data-en="All Projects" data-ro="Toate Proiectele">All Projects</span>
            </a>
            <div class="nl-project-actions-right">
              <?php if ($status === 'upcoming'): ?>
              <a href="<?php echo esc_url($reg_url); ?>" target="_blank" rel="noopener" class="nl-btn-primary">
                <span class="t" data-en="Register Now" data-ro="Înregistrează-te">Register Now</span>
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M2.5 7.5h10M8.5 3.5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </a>
              <?php elseif ($next_form_url): ?>
              <a href="<?php echo esc_url($next_form_url); ?>" target="_blank" rel="noopener" class="nl-btn-primary">
                <span class="t" data-en="Register for Next Edition" data-ro="Înregistrează-te pentru Ediția Viitoare">Register for Next Edition</span>
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M2.5 7.5h10M8.5 3.5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </a>
              <?php else: ?>
              <a href="<?php echo esc_url($ig_url); ?>" target="_blank" rel="noopener" class="nl-btn-outline">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                <span class="t" data-en="Follow on Instagram" data-ro="Urmărește pe Instagram">Follow on Instagram</span>
              </a>
              <?php endif; ?>
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

  <!-- ── DAILY ITINERARY ──────────────────────────────────────────── -->
  <?php if ($itinerary): ?>
  <section class="nl-detail-section nl-itinerary-section">
    <div class="nl-detail-inner">
      <div class="nl-eyebrow nl-reveal">
        <span class="t" data-en="Day by Day" data-ro="Zi cu Zi">Day by Day</span>
      </div>
      <h2 class="nl-section-title nl-reveal">
        <span class="t" data-en="Daily" data-ro="Itinerar">Daily</span><br>
        <em><span class="t" data-en="Itinerary." data-ro="Zilnic.">Itinerary.</span></em>
      </h2>
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
  </section>
  <?php endif; ?>

  <!-- ── ACCOMMODATIONS ───────────────────────────────────────────── -->
  <?php if ($accomm): ?>
  <section class="nl-detail-section nl-accomm-section">
    <div class="nl-detail-inner">
      <div class="nl-eyebrow nl-reveal">
        <span class="t" data-en="Where You Stay" data-ro="Unde Stați">Where You Stay</span>
      </div>
      <h2 class="nl-section-title nl-reveal">
        <span class="t" data-en="Accommo-" data-ro="Cazare">Accommo-</span><br>
        <em><span class="t" data-en="dations." data-ro="Selecționată.">dations.</span></em>
      </h2>
      <p class="nl-accomm-note nl-reveal t"
         data-en="All accommodations are hand-picked and tested — rated 8.5 or higher on Booking.com. Rooms accommodate 2–4 people."
         data-ro="Toate cazările sunt alese și testate personal — rating 8.5+ pe Booking.com. Camerele sunt pentru 2–4 persoane.">
        All accommodations are hand-picked and tested — rated 8.5 or higher on Booking.com. Rooms accommodate 2–4 people.
      </p>
      <div class="nl-accomm-grid">
        <?php foreach ($accomm as $acc):
            $hotel       = $acc->post_title;
            $acc_city    = get_field('city', $acc->ID) ?: '';
            $acc_desc_en = get_field('desc_en', $acc->ID) ?: '';
            $acc_desc_ro = get_field('desc_ro', $acc->ID) ?: $acc_desc_en;
            $booking_url = get_field('booking_url', $acc->ID) ?: '';
            $photos      = array_filter([
                get_the_post_thumbnail_url($acc->ID, 'large'),
                get_field('photo_2', $acc->ID),
                get_field('photo_3', $acc->ID),
                get_field('photo_4', $acc->ID),
            ]);
            ?>
        <div class="nl-accomm-card nl-reveal">
          <?php if ($photos): ?>
          <div class="nl-accomm-photos">
            <?php foreach (array_values(array_slice($photos, 0, 4)) as $pi => $photo): ?>
            <div class="nl-accomm-photo <?php echo $pi === 0 ? 'nl-accomm-photo-main' : ''; ?>">
              <img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($hotel); ?>" loading="lazy">
            </div>
            <?php endforeach; ?>
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
  </section>
  <?php endif; ?>

  <!-- ── BACK CTA ─────────────────────────────────────────────────── -->
  <div class="nl-project-detail-back">
    <div class="nl-detail-inner" style="text-align:center;padding:60px 52px;">
      <a href="<?php echo esc_url(home_url('/projects')); ?>" class="nl-btn-outline">
        ← <span class="t" data-en="All Projects" data-ro="Toate Proiectele">All Projects</span>
      </a>
    </div>
  </div>

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
