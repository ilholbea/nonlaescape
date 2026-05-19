<?php
// [nonla_projects] — renders project cards from nl_project CPT

if (!function_exists('nl_format_desc')) {
    function nl_format_desc($text) {
        if (!$text) return '';
        $text   = wp_strip_all_tags(trim($text));
        $blocks = preg_split('/\n{2,}/', $text);
        $html   = '';
        foreach ($blocks as $block) {
            $block = trim($block);
            if (!$block) continue;
            $lines      = explode("\n", $block);
            $list_items = [];
            $plain      = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;
                if (preg_match('/^[-*•]\s+(.+)/', $line, $m)) {
                    // flush any accumulated plain lines first
                    if ($plain) {
                        $html  .= '<p>' . implode('<br>', array_map('esc_html', $plain)) . '</p>';
                        $plain  = [];
                    }
                    $list_items[] = $m[1];
                } else {
                    // flush any accumulated list items first
                    if ($list_items) {
                        $html       .= '<ul>' . implode('', array_map(fn($i) => '<li>' . esc_html($i) . '</li>', $list_items)) . '</ul>';
                        $list_items  = [];
                    }
                    $plain[] = $line;
                }
            }
            if ($list_items) {
                $html .= '<ul>' . implode('', array_map(fn($i) => '<li>' . esc_html($i) . '</li>', $list_items)) . '</ul>';
            }
            if ($plain) {
                $html .= '<p>' . implode('<br>', array_map('esc_html', $plain)) . '</p>';
            }
        }
        return $html;
    }
}

add_shortcode('nonla_projects', function () {
    $reg_url = get_option('nonla_registration_url', 'https://docs.google.com/forms/d/e/1FAIpQLScaiRzBL7j7vd7o2TVD7zEXSXCG7ObDLJvfEtXsygLuM_dCIQ/viewform');
    $contact = get_option('nonla_contact_email', 'nonlaescape@gmail.com');

    $series_labels = [
        'vietnam-escape'       => 'Vietnam Escape',
        'vietnam-undiscovered' => 'Vietnam Undiscovered',
    ];

    // Fetch all projects; sort upcoming first then completed, each group by year DESC
    $posts = get_posts([
        'post_type'      => 'nl_project',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

    usort($posts, function ($a, $b) {
        $sa = get_field('status', $a->ID);
        $sb = get_field('status', $b->ID);
        $ca = is_string($sa) && strtolower(trim($sa)) === 'completed';
        $cb = is_string($sb) && strtolower(trim($sb)) === 'completed';
        if ($ca !== $cb) return $ca ? 1 : -1;
        return intval(get_field('year', $b->ID)) - intval(get_field('year', $a->ID));
    });

    // Find next upcoming project for conditional CTA on completed cards
    $next_upcoming = get_posts([
        'post_type'      => 'nl_project',
        'posts_per_page' => 1,
        'meta_query'     => [['key' => 'status', 'value' => 'upcoming']],
        'no_found_rows'  => true,
    ]);
    $next_proj     = $next_upcoming[0] ?? null;
    $next_form_url = $next_proj ? (get_field('register_form_url', $next_proj->ID) ?: $reg_url) : null;

    $ig_url = 'https://www.instagram.com/nonlaescape/';
    $fb_url = 'https://www.facebook.com/nonlaescape/';

    ob_start(); ?>
    <div class="nl-filters-bar">
      <div class="nl-filter-tabs">
        <button class="nl-filter-btn active" data-filter="all" onclick="nlFilterProjects(this,'all')">
          <span class="t" data-en="All" data-ro="Toate">All</span>
        </button>
        <button class="nl-filter-btn" data-filter="vietnam-escape" onclick="nlFilterProjects(this,'vietnam-escape')">Vietnam Escape</button>
        <button class="nl-filter-btn" data-filter="vietnam-undiscovered" onclick="nlFilterProjects(this,'vietnam-undiscovered')">Vietnam Undiscovered</button>
        <button class="nl-filter-btn" data-filter="upcoming" onclick="nlFilterProjects(this,'upcoming')">
          <span class="t" data-en="Upcoming" data-ro="Viitoare">Upcoming</span>
        </button>
        <button class="nl-filter-btn" data-filter="completed" onclick="nlFilterProjects(this,'completed')">
          <span class="t" data-en="Completed" data-ro="Finalizate">Completed</span>
        </button>
      </div>
    </div>

    <div class="nl-projects-list">
      <?php if ($posts): ?>
        <?php foreach ($posts as $p):
            $series_slug = get_field('series', $p->ID) ?: 'vietnam-escape';
            $series      = $series_labels[$series_slug] ?? 'Vietnam Escape';
            $edition     = get_field('edition', $p->ID);
            $year        = get_field('year', $p->ID);
            $raw_status  = get_field('status', $p->ID);
            $status      = (is_string($raw_status) && strtolower(trim($raw_status)) === 'completed') ? 'completed' : 'upcoming';
            $dates_en    = get_field('dates_en', $p->ID);
            $dates_ro    = get_field('dates_ro', $p->ID);
            $dur_en      = get_field('duration_en', $p->ID);
            $dur_ro      = get_field('duration_ro', $p->ID);
            $price       = get_field('price', $p->ID);
            $note_en     = get_field('price_note_en', $p->ID);
            $note_ro     = get_field('price_note_ro', $p->ID);
            $raw_dests   = get_field('destinations', $p->ID);
            $dests       = $raw_dests ? array_map('trim', explode(',', $raw_dests)) : [];
            $cover       = get_field('cover_image', $p->ID);
            $desc_en     = get_field('desc_en', $p->ID);
            $desc_ro     = get_field('desc_ro', $p->ID);
            $form_url    = get_field('register_form_url', $p->ID) ?: $reg_url;
            $fmt_en      = nl_format_desc($desc_en);
            $fmt_ro      = nl_format_desc($desc_ro ?: $desc_en);
            ?>
        <div class="nl-project-card nl-reveal"
             data-series="<?php echo esc_attr($series_slug); ?>"
             data-status="<?php echo esc_attr($status); ?>">
          <div class="nl-project-cover">
            <?php if ($cover): ?>
            <img src="<?php echo esc_url($cover); ?>" alt="<?php echo esc_attr($series . ' ' . $edition); ?>" loading="lazy">
            <?php else: ?>
            <div class="nl-project-cover-placeholder">
              <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 48 L20 24 L32 36 L44 20 L60 48 Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" fill="none"/>
                <circle cx="18" cy="16" r="5" stroke="currentColor" stroke-width="2" fill="none"/>
              </svg>
            </div>
            <?php endif; ?>
            <div class="nl-project-status-badge <?php echo esc_attr($status); ?>">
              <span class="t"
                    data-en="<?php echo $status === 'completed' ? 'Completed' : 'Upcoming'; ?>"
                    data-ro="<?php echo $status === 'completed' ? 'Finalizat' : 'Viitor'; ?>">
                <?php echo $status === 'completed' ? 'Completed' : 'Upcoming'; ?>
              </span>
            </div>
          </div>
          <div class="nl-project-body">
            <span class="nl-project-series"><?php echo esc_html($series); ?></span>
            <div class="nl-project-name"><?php echo esc_html($series); ?></div>
            <div class="nl-project-edition"><?php echo esc_html($edition); ?><?php echo $year ? ' · ' . esc_html($year) : ''; ?></div>
            <?php if ($fmt_en): ?>
            <div class="nl-project-desc t"
                 data-en="<?php echo esc_attr($fmt_en); ?>"
                 data-ro="<?php echo esc_attr($fmt_ro); ?>">
              <?php echo $fmt_en; ?>
            </div>
            <?php endif; ?>
            <div class="nl-project-meta">
              <?php if ($dates_en): ?>
              <div>
                <div class="nl-meta-label t" data-en="Dates" data-ro="Perioada">Dates</div>
                <div class="nl-meta-value t"
                     data-en="<?php echo esc_attr($dates_en); ?>"
                     data-ro="<?php echo esc_attr($dates_ro ?: $dates_en); ?>">
                  <?php echo esc_html($dates_en); ?>
                </div>
              </div>
              <?php endif; ?>
              <?php if ($dur_en): ?>
              <div>
                <div class="nl-meta-label t" data-en="Duration" data-ro="Durată">Duration</div>
                <div class="nl-meta-value t"
                     data-en="<?php echo esc_attr($dur_en); ?>"
                     data-ro="<?php echo esc_attr($dur_ro ?: $dur_en); ?>">
                  <?php echo esc_html($dur_en); ?>
                </div>
              </div>
              <?php endif; ?>
              <?php if ($price): ?>
              <div>
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
            <div class="nl-project-dests">
              <?php foreach ($dests as $d): ?>
              <span class="nl-dest-pill"><?php echo esc_html($d); ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="nl-project-actions">
              <a href="<?php echo esc_url(get_permalink($p->ID)); ?>" class="nl-btn-outline">
                <span class="t" data-en="View Details" data-ro="Vezi Detalii">View Details</span>
              </a>
              <div class="nl-project-actions-right">
                <?php if ($status !== 'completed'): ?>
                <a href="<?php echo esc_url($form_url); ?>" target="_blank" rel="noopener" class="nl-btn-primary">
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
                <a href="<?php echo esc_url($fb_url); ?>" target="_blank" rel="noopener" class="nl-btn-outline">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                  <span class="t" data-en="Follow on Facebook" data-ro="Urmărește pe Facebook">Follow on Facebook</span>
                </a>
                <?php endif; ?>
                <div class="nl-project-social-links">
                  <a href="<?php echo esc_url($ig_url); ?>" class="nl-project-social-link" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                  </a>
                  <a href="<?php echo esc_url($fb_url); ?>" class="nl-project-social-link" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="nl-empty-state">
          <p class="t"
             data-en="No projects found. New escapes are announced soon — check back or get in touch."
             data-ro="Niciun proiect găsit. Călătoriile noi sunt anunțate în curând — revino sau contactează-ne.">
            No projects found. New escapes are announced soon — check back or get in touch.
          </p>
        </div>
      <?php endif; ?>
    </div>
    <?php return ob_get_clean();
});