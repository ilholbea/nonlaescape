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
          $icon     = get_field('icon', $item->ID);
          $title_en = $item->post_title;
          $title_ro = get_field('title_ro', $item->ID) ?: $title_en;
          $desc_en  = wp_strip_all_tags((string) get_field('description', $item->ID));
          $desc_ro  = wp_strip_all_tags((string) get_field('description_ro', $item->ID)) ?: $desc_en;
          $delay_class = 'nl-d' . (($i % 3) + 1);
          ?>
      <div class="nl-why-item nl-reveal <?php echo esc_attr($delay_class); ?>">
        <?php if ($icon): ?><div class="nl-why-icon"><?php echo esc_html($icon); ?></div><?php endif; ?>
        <div class="nl-why-title t" data-en="<?php echo esc_attr($title_en); ?>" data-ro="<?php echo esc_attr($title_ro); ?>"><?php echo esc_html($title_en); ?></div>
        <?php if ($desc_en): ?><p class="nl-why-desc t" data-en="<?php echo esc_attr($desc_en); ?>" data-ro="<?php echo esc_attr($desc_ro); ?>"><?php echo esc_html($desc_en); ?></p><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php return ob_get_clean();
});
