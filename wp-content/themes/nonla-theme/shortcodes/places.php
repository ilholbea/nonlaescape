<?php
// [nl_places] — renders destination grid from nl_place CPT

add_shortcode('nl_places', function () {
    if (!function_exists('get_field')) return '';

    $places = get_posts([
        'post_type'      => 'nl_place',
        'posts_per_page' => -1,
        'meta_key'       => 'display_order',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
    ]);

    if (empty($places)) return '';

    ob_start(); ?>
    <div class="nl-dest-grid">
      <?php foreach ($places as $place):
          $photo = get_field('photo', $place->ID);
          $tag   = get_field('tag', $place->ID);
          $name  = $place->post_title;
          $desc  = get_field('description', $place->ID);
          ?>
      <div class="nl-dest-card"<?php if ($photo): ?> style="background-image:url('<?php echo esc_url($photo); ?>')"<?php endif; ?>>
        <div class="nl-dest-overlay">
          <?php if ($tag):  ?><div class="nl-dest-tag"><?php echo esc_html($tag); ?></div><?php endif; ?>
          <?php if ($name): ?><div class="nl-dest-name"><?php echo esc_html($name); ?></div><?php endif; ?>
          <?php if ($desc): ?><div class="nl-dest-desc"><?php echo wp_strip_all_tags($desc); ?></div><?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php return ob_get_clean();
});
