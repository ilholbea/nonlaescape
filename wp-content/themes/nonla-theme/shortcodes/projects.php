<?php
// [nonla_projects] — renders project cards from nl_project CPT

add_shortcode('nonla_projects', function () {
    $reg_url = get_option('nonla_registration_url', 'https://docs.google.com/forms/d/e/1FAIpQLScaiRzBL7j7vd7o2TVD7zEXSXCG7ObDLJvfEtXsygLuM_dCIQ/viewform');
    $contact = get_option('nonla_contact_email', 'contact@nonlaescape.com');

    $series_labels = [
        'vietnam-escape'       => 'Vietnam Escape',
        'vietnam-undiscovered' => 'Vietnam Undiscovered',
    ];

    $posts = get_posts([
        'post_type'      => 'nl_project',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

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
            $status      = get_field('status', $p->ID) ?: 'upcoming';
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
            ?>
        <div class="nl-project-card nl-reveal"
             data-series="<?php echo esc_attr($series_slug); ?>"
             data-status="<?php echo esc_attr($status); ?>">
          <div class="nl-project-cover">
            <?php if ($cover): ?>
            <img src="<?php echo esc_url($cover); ?>" alt="<?php echo esc_attr($series . ' ' . $edition); ?>" loading="lazy">
            <?php else: ?>
            <div style="width:100%;height:100%;background:var(--parch);"></div>
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
            <?php if ($desc_en): ?>
            <p class="nl-project-desc t"
               data-en="<?php echo esc_attr($desc_en); ?>"
               data-ro="<?php echo esc_attr($desc_ro ?: $desc_en); ?>">
              <?php echo esc_html($desc_en); ?>
            </p>
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
              <?php if ($status === 'upcoming'): ?>
              <a href="<?php echo esc_url($form_url); ?>" target="_blank" rel="noopener" class="nl-btn-primary">
                <span class="t" data-en="Register Now" data-ro="Înregistrează-te">Register Now</span>
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M2.5 7.5h10M8.5 3.5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </a>
              <?php endif; ?>
              <a href="mailto:<?php echo esc_attr($contact); ?>?subject=<?php echo urlencode($series . ' ' . $edition); ?>" class="nl-btn-outline">
                <?php if ($status === 'completed'): ?>
                <span class="t" data-en="Ask About Next Edition" data-ro="Întreabă despre Ediția Viitoare">Ask About Next Edition</span>
                <?php else: ?>
                <span class="t" data-en="Ask a Question" data-ro="Pune o Întrebare">Ask a Question</span>
                <?php endif; ?>
              </a>
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
