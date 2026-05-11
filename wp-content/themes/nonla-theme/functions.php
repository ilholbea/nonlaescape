<?php

require_once get_stylesheet_directory() . '/inc/post-types.php';
require_once get_stylesheet_directory() . '/inc/acf-fields.php';

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('nonla-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=Inter:wght@300;400;500;600&family=Bebas+Neue&display=swap', [], null);
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('nonla-style', get_stylesheet_uri(), ['parent-style'], '1.0');
    wp_add_inline_style('nonla-style', nonla_global_css());
});

function nonla_global_css() {
    return '
:root {
  --cream:   #FAF5EC;
  --parch:   #F2E8D4;
  --white:   #FFFDF7;
  --terra:   #B85520;
  --terra-l: #D4703A;
  --gold:    #B8841A;
  --gold-l:  #D4A030;
  --forest:  #2C4A1A;
  --text:    #1A0E06;
  --text2:   #4A3318;
  --muted:   #7A6040;
  --border:  #DDD0B0;
  --card:    #FFF8EE;
  --shadow:  rgba(26,14,6,.08);
}
body { background: var(--cream); color: var(--text); font-family: "Inter", sans-serif; }
h1,h2,h3,h4 { font-family: "Cormorant Garamond", serif; }

.nl-eyebrow {
  display: inline-flex; align-items: center; gap: 10px;
  font-size: 11px; letter-spacing: .24em; text-transform: uppercase;
  color: var(--terra); font-weight: 600; margin-bottom: 16px;
}
.nl-eyebrow::before { content: ""; width: 20px; height: 1.5px; background: var(--terra); display: block; }

/* Cards */
.nl-card { background: var(--card); border: 1px solid var(--border); border-radius: 6px; padding: 40px 36px; transition: box-shadow .25s, transform .25s; }
.nl-card:hover { box-shadow: 0 16px 48px var(--shadow); transform: translateY(-3px); }
.nl-card-label { display: inline-flex; align-items: center; font-size: 10px; font-weight: 700; letter-spacing: .22em; text-transform: uppercase; color: var(--terra); background: rgba(184,85,32,.08); padding: 5px 12px; border-radius: 100px; margin-bottom: 20px; }
.nl-card-title { font-family: "Cormorant Garamond", serif; font-size: 34px; font-weight: 600; line-height: 1.1; margin-bottom: 14px; }
.nl-card-desc { font-size: 14px; color: var(--text2); line-height: 1.75; font-weight: 300; }
.nl-link { display: inline-flex; align-items: center; gap: 8px; margin-top: 20px; font-size: 12px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--terra); border-bottom: 1px solid rgba(184,85,32,.3); padding-bottom: 2px; transition: border-color .2s, gap .2s; }
.nl-link:hover { border-color: var(--terra); gap: 12px; }

/* Team */
.nl-team-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 28px; }
.nl-team-card { background: var(--card); border: 1px solid var(--border); border-radius: 6px; overflow: hidden; }
.nl-team-photo { width: 100%; aspect-ratio: 3/4; object-fit: cover; display: block; }
.nl-team-photo-placeholder { width: 100%; aspect-ratio: 3/4; background: var(--parch); display: flex; align-items: center; justify-content: center; font-size: 48px; }
.nl-team-body { padding: 24px 22px; }
.nl-team-name { font-family: "Cormorant Garamond", serif; font-size: 24px; font-weight: 600; margin-bottom: 4px; color: var(--text); }
.nl-team-role { font-size: 11px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--terra); margin-bottom: 6px; }
.nl-team-city { font-size: 12px; color: var(--muted); margin-bottom: 14px; }
.nl-team-bio { font-size: 13px; color: var(--text2); line-height: 1.75; font-weight: 300; }
@media (max-width: 768px) { .nl-team-grid { grid-template-columns: 1fr; } }

/* Projects */
.nl-projects-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 28px; }
.nl-project-card { background: var(--card); border: 1px solid var(--border); border-radius: 6px; overflow: hidden; }
.nl-project-cover { width: 100%; aspect-ratio: 16/9; object-fit: cover; display: block; }
.nl-project-cover-placeholder { width: 100%; aspect-ratio: 16/9; background: var(--parch); }
.nl-project-body { padding: 28px 26px; }
.nl-project-meta { display: flex; gap: 10px; align-items: center; margin-bottom: 14px; flex-wrap: wrap; }
.nl-project-series { font-size: 10px; font-weight: 700; letter-spacing: .2em; text-transform: uppercase; color: var(--terra); background: rgba(184,85,32,.08); padding: 4px 10px; border-radius: 100px; }
.nl-project-status-upcoming { font-size: 10px; font-weight: 700; letter-spacing: .15em; text-transform: uppercase; color: #fff; background: var(--forest); padding: 4px 10px; border-radius: 100px; }
.nl-project-status-completed { font-size: 10px; font-weight: 700; letter-spacing: .15em; text-transform: uppercase; color: var(--muted); background: rgba(122,96,64,.12); padding: 4px 10px; border-radius: 100px; }
.nl-project-title { font-family: "Cormorant Garamond", serif; font-size: 28px; font-weight: 600; margin-bottom: 6px; color: var(--text); }
.nl-project-edition { font-size: 12px; color: var(--muted); margin-bottom: 16px; }
.nl-project-details { display: flex; flex-direction: column; gap: 6px; margin-bottom: 18px; }
.nl-project-detail { font-size: 13px; color: var(--text2); }
.nl-project-detail strong { color: var(--text); font-weight: 600; }
.nl-project-price { font-family: "Cormorant Garamond", serif; font-size: 32px; font-weight: 600; color: var(--terra); }
.nl-project-price-note { font-size: 12px; color: var(--muted); margin-top: 2px; }
.nl-project-desc { font-size: 13px; color: var(--text2); line-height: 1.75; font-weight: 300; margin-top: 14px; }
.nl-project-btn { display: inline-flex; align-items: center; gap: 8px; margin-top: 20px; background: var(--terra); color: #fff !important; font-size: 11px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; padding: 12px 24px; border-radius: 3px; transition: background .2s; text-decoration: none; }
.nl-project-btn:hover { background: var(--terra-l); }
.nl-project-btn-disabled { display: inline-flex; margin-top: 20px; background: rgba(122,96,64,.12); color: var(--muted); font-size: 11px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; padding: 12px 24px; border-radius: 3px; }
@media (max-width: 768px) { .nl-projects-grid { grid-template-columns: 1fr; } }

/* Legal */
.nl-legal-wrap { max-width: 800px; margin: 0 auto; }
.nl-legal-wrap h2 { font-family: "Cormorant Garamond", serif; font-size: 32px; font-weight: 600; margin: 40px 0 16px; color: var(--text); }
.nl-legal-wrap h3 { font-family: "Cormorant Garamond", serif; font-size: 22px; font-weight: 600; margin: 28px 0 10px; color: var(--text); }
.nl-legal-wrap p { font-size: 15px; color: var(--text2); line-height: 1.85; font-weight: 300; margin-bottom: 16px; }
.nl-legal-wrap ul { padding-left: 20px; margin-bottom: 16px; }
.nl-legal-wrap li { font-size: 15px; color: var(--text2); line-height: 1.85; font-weight: 300; margin-bottom: 6px; }
';
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
    if (!$members) return '<p style="color:var(--muted);font-style:italic;">No team members added yet. Add them via WP Admin → Team.</p>';
    ob_start(); ?>
    <div class="nl-team-grid">
    <?php foreach ($members as $m):
        $photo = get_field('photo', $m->ID);
        $role  = get_field('role_en', $m->ID);
        $city  = get_field('city', $m->ID);
        $bio   = get_field('bio_en', $m->ID);
    ?>
      <div class="nl-team-card">
        <?php if ($photo): ?>
          <img class="nl-team-photo" src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($m->post_title); ?>">
        <?php else: ?>
          <div class="nl-team-photo-placeholder">👤</div>
        <?php endif; ?>
        <div class="nl-team-body">
          <div class="nl-team-name"><?php echo esc_html($m->post_title); ?></div>
          <?php if ($role): ?><div class="nl-team-role"><?php echo esc_html($role); ?></div><?php endif; ?>
          <?php if ($city): ?><div class="nl-team-city">📍 <?php echo esc_html($city); ?></div><?php endif; ?>
          <?php if ($bio): ?><p class="nl-team-bio"><?php echo esc_html($bio); ?></p><?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
    <?php return ob_get_clean();
});

add_shortcode('nonla_projects', function () {
    $projects = get_posts([
        'post_type'      => 'nl_project',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
    if (!$projects) return '<p style="color:var(--muted);font-style:italic;">No projects added yet. Add them via WP Admin → Projects.</p>';
    ob_start(); ?>
    <div class="nl-projects-grid">
    <?php foreach ($projects as $p):
        $series   = get_field('series', $p->ID);
        $edition  = get_field('edition', $p->ID);
        $status   = get_field('status', $p->ID);
        $dates    = get_field('dates_en', $p->ID);
        $duration = get_field('duration_en', $p->ID);
        $price    = get_field('price', $p->ID);
        $note     = get_field('price_note_en', $p->ID);
        $dests    = get_field('destinations', $p->ID);
        $cover    = get_field('cover_image', $p->ID);
        $desc     = get_field('desc_en', $p->ID);
        $series_label = $series === 'vietnam-escape' ? 'Vietnam Escape' : 'Vietnam Undiscovered';
    ?>
      <div class="nl-project-card">
        <?php if ($cover): ?>
          <img class="nl-project-cover" src="<?php echo esc_url($cover); ?>" alt="<?php echo esc_attr($p->post_title); ?>">
        <?php else: ?>
          <div class="nl-project-cover-placeholder"></div>
        <?php endif; ?>
        <div class="nl-project-body">
          <div class="nl-project-meta">
            <span class="nl-project-series"><?php echo esc_html($series_label); ?></span>
            <span class="nl-project-status-<?php echo esc_attr($status ?: 'upcoming'); ?>"><?php echo ($status === 'completed') ? 'Completed' : 'Upcoming'; ?></span>
          </div>
          <div class="nl-project-title"><?php echo esc_html($p->post_title); ?></div>
          <?php if ($edition): ?><div class="nl-project-edition"><?php echo esc_html($edition); ?></div><?php endif; ?>
          <div class="nl-project-details">
            <?php if ($dates):    ?><div class="nl-project-detail"><strong>Dates:</strong> <?php echo esc_html($dates); ?></div><?php endif; ?>
            <?php if ($duration): ?><div class="nl-project-detail"><strong>Duration:</strong> <?php echo esc_html($duration); ?></div><?php endif; ?>
            <?php if ($dests):    ?><div class="nl-project-detail"><strong>Destinations:</strong> <?php echo esc_html($dests); ?></div><?php endif; ?>
          </div>
          <?php if ($price): ?>
            <div class="nl-project-price"><?php echo esc_html($price); ?></div>
            <?php if ($note): ?><div class="nl-project-price-note"><?php echo esc_html($note); ?></div><?php endif; ?>
          <?php endif; ?>
          <?php if ($desc): ?><p class="nl-project-desc"><?php echo esc_html($desc); ?></p><?php endif; ?>
          <?php if ($status === 'completed'): ?>
            <span class="nl-project-btn-disabled">Completed</span>
          <?php else: ?>
            <a href="mailto:contact@nonlaescape.com" class="nl-project-btn">Register Interest →</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
    <?php return ob_get_clean();
});
