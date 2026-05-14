<?php
// [nonla_team] — renders team member grid from nl_team_member CPT

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
