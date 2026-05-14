<?php
// [nl_gallery_items] — renders gallery row from nl_gallery_item CPT

add_shortcode('nl_gallery_items', function () {
    $items = get_posts([
        'post_type'      => 'nl_gallery_item',
        'posts_per_page' => -1,
        'meta_key'       => 'display_order',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
    ]);

    if (empty($items)) return '';

    ob_start(); ?>
    <div class="nl-gallery-row">
      <?php foreach ($items as $i => $item):
          $photo   = get_field('photo', $item->ID);
          $caption = get_field('caption', $item->ID);
          $class   = $i === 0 ? 'nl-gallery-photo-tall' : 'nl-gallery-photo-short';
          if ($i === 0): ?>
      <div class="nl-gallery-photo <?php echo $class; ?>">
        <?php if ($photo): ?><img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($caption ?: $item->post_title); ?>" loading="lazy"><?php endif; ?>
      </div>
      <?php else:
          if ($i === 1 || $i === 3): ?><div class="nl-gallery-col"><?php endif; ?>
      <div class="nl-gallery-photo <?php echo $class; ?>">
        <?php if ($photo): ?><img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($caption ?: $item->post_title); ?>" loading="lazy"><?php endif; ?>
      </div>
      <?php if ($i === 2 || $i === 4 || $i === count($items) - 1): ?></div><?php endif; ?>
      <?php endif; ?>
      <?php endforeach; ?>
    </div>
    <?php return ob_get_clean();
});
