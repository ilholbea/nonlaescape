<?php

require_once get_stylesheet_directory() . '/inc/post-types.php';
require_once get_stylesheet_directory() . '/inc/acf-fields.php';

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('nonla-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=Inter:wght@300;400;500;600&family=Bebas+Neue&display=swap', [], null);
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('nonla-style', get_stylesheet_uri(), ['parent-style'], '1.0');
});

// ── SHORTCODES ──────────────────────────────────────────────────────────────

add_shortcode('nonla_team', function () {
    $members = get_posts([
            'post_type' => 'nl_team_member',
            'posts_per_page' => -1,
            'meta_key' => 'display_order',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
    ]);
    if (!$members) return '<p style="color:var(--muted);font-style:italic;">No team members added yet. Add them via WP Admin → Team.</p>';
    ob_start(); ?>
    <div class="nl-team-grid">
        <?php foreach ($members as $m):
            $photo = get_field('photo', $m->ID);
            $role = get_field('role_en', $m->ID);
            $city = get_field('city', $m->ID);
            $bio = get_field('bio_en', $m->ID);
            ?>
            <div class="nl-team-card">
                <?php if ($photo): ?>
                    <img class="nl-team-photo" src="<?php echo esc_url($photo); ?>"
                         alt="<?php echo esc_attr($m->post_title); ?>">
                <?php else: ?>
                    <div class="nl-team-photo-placeholder">👤</div>
                <?php endif; ?>
                <div class="nl-team-body">
                    <div class="nl-team-name"><?php echo esc_html($m->post_title); ?></div>
                    <?php if ($role): ?>
                        <div class="nl-team-role"><?php echo esc_html($role); ?></div><?php endif; ?>
                    <?php if ($city): ?>
                        <div class="nl-team-city">📍 <?php echo esc_html($city); ?></div><?php endif; ?>
                    <?php if ($bio): ?><p class="nl-team-bio"><?php echo esc_html($bio); ?></p><?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php return ob_get_clean();
});

add_shortcode('nonla_projects', function () {
    $projects = get_posts([
            'post_type' => 'nl_project',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
    ]);
    if (!$projects) return '<p style="color:var(--muted);font-style:italic;">No projects added yet. Add them via WP Admin → Projects.</p>';
    ob_start(); ?>
    <div class="nl-projects-grid">
        <?php foreach ($projects as $p):
            $series = get_field('series', $p->ID);
            $edition = get_field('edition', $p->ID);
            $status = get_field('status', $p->ID);
            $dates = get_field('dates_en', $p->ID);
            $duration = get_field('duration_en', $p->ID);
            $price = get_field('price', $p->ID);
            $note = get_field('price_note_en', $p->ID);
            $dests = get_field('destinations', $p->ID);
            $cover = get_field('cover_image', $p->ID);
            $desc = get_field('desc_en', $p->ID);
            $series_label = $series === 'vietnam-escape' ? 'Vietnam Escape' : 'Vietnam Undiscovered';
            ?>
            <div class="nl-project-card">
                <?php if ($cover): ?>
                    <img class="nl-project-cover" src="<?php echo esc_url($cover); ?>"
                         alt="<?php echo esc_attr($p->post_title); ?>">
                <?php else: ?>
                    <div class="nl-project-cover-placeholder"></div>
                <?php endif; ?>
                <div class="nl-project-body">
                    <div class="nl-project-meta">
                        <span class="nl-project-series"><?php echo esc_html($series_label); ?></span>
                        <span class="nl-project-status-<?php echo esc_attr($status ?: 'upcoming'); ?>"><?php echo ($status === 'completed') ? 'Completed' : 'Upcoming'; ?></span>
                    </div>
                    <div class="nl-project-title"><?php echo esc_html($p->post_title); ?></div>
                    <?php if ($edition): ?>
                        <div class="nl-project-edition"><?php echo esc_html($edition); ?></div><?php endif; ?>
                    <div class="nl-project-details">
                        <?php if ($dates): ?>
                            <div class="nl-project-detail"><strong>Dates:</strong> <?php echo esc_html($dates); ?>
                            </div><?php endif; ?>
                        <?php if ($duration): ?>
                            <div class="nl-project-detail"><strong>Duration:</strong> <?php echo esc_html($duration); ?>
                            </div><?php endif; ?>
                        <?php if ($dests): ?>
                            <div class="nl-project-detail">
                            <strong>Destinations:</strong> <?php echo esc_html($dests); ?></div><?php endif; ?>
                    </div>
                    <?php if ($price): ?>
                        <div class="nl-project-price"><?php echo esc_html($price); ?></div>
                        <?php if ($note): ?>
                            <div class="nl-project-price-note"><?php echo esc_html($note); ?></div><?php endif; ?>
                    <?php endif; ?>
                    <?php if ($desc): ?><p class="nl-project-desc"><?php echo esc_html($desc); ?></p><?php endif; ?>
                    <?php if ($status === 'completed'): ?>
                        <span class="nl-project-btn-disabled">Completed</span>
                    <?php else:
                        $form_url = get_field('register_form_url', $p->ID); ?>
                        <a href="<?php echo esc_url($form_url ?: 'mailto:contact@nonlaescape.com'); ?>"
                           class="nl-project-btn" <?php echo $form_url ?
                                'target="_blank"' : ''; ?>>Register Interest →</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php return ob_get_clean();
});
