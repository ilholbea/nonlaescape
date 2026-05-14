<?php
// [nl_testimonials] — renders testimonial cards from nl_testimonial CPT

add_shortcode('nl_testimonials', function () {
    $items = get_posts([
        'post_type'      => 'nl_testimonial',
        'posts_per_page' => -1,
        'meta_key'       => 'display_order',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
    ]);

    if (empty($items)) return '';

    ob_start(); ?>
    <div class="nl-testimonials-grid">
      <?php foreach ($items as $item):
          $quote        = get_field('quote', $item->ID);
          $author_name  = get_field('author_name', $item->ID);
          $author_role  = get_field('author_role', $item->ID);
          $author_photo = get_field('author_photo', $item->ID);
          ?>
      <div class="nl-testimonial-card nl-reveal">
        <div class="nl-testimonial-quote-mark">"</div>
        <div class="nl-testimonial-text"><?php echo wp_kses_post($quote); ?></div>
        <div class="nl-testimonial-author">
          <?php if ($author_photo): ?>
          <img class="nl-testimonial-author-photo" src="<?php echo esc_url($author_photo); ?>" alt="<?php echo esc_attr($author_name); ?>">
          <?php else: ?>
          <div class="nl-testimonial-author-photo-placeholder">👤</div>
          <?php endif; ?>
          <div>
            <div class="nl-testimonial-author-name"><?php echo esc_html($author_name); ?></div>
            <?php if ($author_role): ?>
            <div class="nl-testimonial-author-role"><?php echo esc_html($author_role); ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php return ob_get_clean();
});
