<?php
// [nl_why_items] — renders Why Nonla grid from nl_why_item CPT

add_shortcode('nl_why_items', function () {
    $items = get_posts([
        'post_type'      => 'nl_why_item',
        'posts_per_page' => -1,
        'meta_key'       => 'display_order',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
    ]);

    if (empty($items)) return '';

    ob_start(); ?>
    <div class="nl-why-grid">
      <?php foreach ($items as $i => $item):
          $icon  = get_field('icon', $item->ID);
          $title = $item->post_title;
          $desc  = get_field('description', $item->ID);
          $delay_class = 'nl-d' . (($i % 3) + 1);
          ?>
      <div class="nl-why-item nl-reveal <?php echo esc_attr($delay_class); ?>">
        <?php if ($icon): ?><div class="nl-why-icon"><?php echo esc_html($icon); ?></div><?php endif; ?>
        <div class="nl-why-title"><?php echo esc_html($title); ?></div>
        <?php if ($desc): ?><p class="nl-why-desc"><?php echo esc_html(wp_strip_all_tags($desc)); ?></p><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php return ob_get_clean();
});
