<?php

require_once get_stylesheet_directory() . '/inc/post-types.php';
require_once get_stylesheet_directory() . '/inc/acf-fields.php';

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('nonla-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=Inter:wght@300;400;500;600&family=Bebas+Neue&display=swap', [], null);
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('nonla-style', get_stylesheet_uri(), ['parent-style'], '1.0');
});

// Reveal observer + filter function injected in footer on every page
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

// ── NONLA SETTINGS ──────────────────────────────────────────────────────────

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

// ── SHORTCODES ──────────────────────────────────────────────────────────────

add_shortcode('nonla_team', function () {
    $members = get_posts([
        'post_type'      => 'nl_team_member',
        'posts_per_page' => -1,
        'meta_key'       => 'display_order',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
    ]);

    ob_start(); ?>
    <div class="nl-team-wrap">
      <?php if ($members): ?>
      <div class="nl-team-grid">
        <?php foreach ($members as $i => $m):
            $photo   = get_field('photo', $m->ID);
            $role_en = get_field('role_en', $m->ID);
            $role_ro = get_field('role_ro', $m->ID);
            $city    = get_field('city', $m->ID);
            $bio_en  = get_field('bio_en', $m->ID);
            $bio_ro  = get_field('bio_ro', $m->ID);
            $email   = get_field('email', $m->ID);
            $delay   = ($i % 3) * 0.1;
            ?>
        <div class="nl-team-card nl-reveal" style="transition-delay: <?php echo $delay; ?>s;">
          <div class="nl-team-photo">
            <?php if ($photo): ?>
            <img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($m->post_title); ?>" loading="lazy">
            <?php else: ?>
            <div class="nl-team-photo-placeholder">👤</div>
            <?php endif; ?>
          </div>
          <div class="nl-team-body">
            <div class="nl-team-name"><?php echo esc_html($m->post_title); ?></div>
            <?php if ($role_en): ?>
            <div class="nl-team-role t"
                 data-en="<?php echo esc_attr($role_en); ?>"
                 data-ro="<?php echo esc_attr($role_ro ?: $role_en); ?>">
              <?php echo esc_html($role_en); ?>
            </div>
            <?php endif; ?>
            <?php if ($city): ?>
            <div class="nl-team-city">📍 <?php echo esc_html($city); ?></div>
            <?php endif; ?>
            <?php if ($bio_en): ?>
            <p class="nl-team-bio t"
               data-en="<?php echo esc_attr($bio_en); ?>"
               data-ro="<?php echo esc_attr($bio_ro ?: $bio_en); ?>">
              <?php echo esc_html($bio_en); ?>
            </p>
            <?php endif; ?>
            <?php if ($email): ?>
            <div class="nl-team-email"><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></div>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="nl-team-empty">
        <p class="t"
           data-en="Team profiles are coming soon. In the meantime, get in touch directly."
           data-ro="Profilurile echipei vin în curând. Între timp, contactați-ne direct.">
          Team profiles are coming soon. In the meantime, get in touch directly.
        </p>
        <a href="mailto:<?php echo esc_attr(get_option('nonla_contact_email', 'contact@nonlaescape.com')); ?>" class="nl-btn-primary" style="margin-top:24px;display:inline-flex;">
          <?php echo esc_html(get_option('nonla_contact_email', 'contact@nonlaescape.com')); ?>
        </a>
      </div>
      <?php endif; ?>
    </div>
    <?php return ob_get_clean();
});

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
             data-ro="Niciun proiect găsit. Escapadele noi sunt anunțate în curând — revino sau contactează-ne.">
            No projects found. New escapes are announced soon — check back or get in touch.
          </p>
        </div>
      <?php endif; ?>
    </div>
    <?php return ob_get_clean();
});

add_shortcode('nl_places', function () {
    if (!function_exists('get_field')) return '';

    $front_id = (int) get_option('page_on_front');
    $max      = (int) get_field('places_max_items', $front_id) ?: 4;
    $items    = get_field('places_items', $front_id) ?: [];
    $items = array_slice($items, 0, $max);

    if (empty($items)) return '';

    ob_start(); ?>
    <div class="nl-dest-grid">
      <?php foreach ($items as $item):
          $image = $item['image'] ?? '';
          $tag   = $item['tag'] ?? '';
          $name  = $item['name'] ?? '';
          $desc  = $item['description'] ?? '';
          ?>
      <div class="nl-dest-card">
        <?php if ($image): ?>
        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy">
        <?php endif; ?>
        <div class="nl-dest-overlay">
          <?php if ($tag):  ?><div class="nl-dest-tag"><?php echo esc_html($tag); ?></div><?php endif; ?>
          <?php if ($name): ?><div class="nl-dest-name"><?php echo esc_html($name); ?></div><?php endif; ?>
          <?php if ($desc): ?><div class="nl-dest-desc"><?php echo esc_html($desc); ?></div><?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php return ob_get_clean();
});
