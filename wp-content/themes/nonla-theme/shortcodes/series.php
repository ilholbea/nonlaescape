<?php
// [nl_series] — renders series cards from nl_series CPT

add_shortcode('nl_series', function () {
    $items = get_posts([
        'post_type'      => 'nl_series',
        'posts_per_page' => -1,
        'meta_key'       => 'display_order',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
    ]);

    if (empty($items)) return '';

    $projects_url = get_permalink(get_page_by_path('projects'));
    $arrow_sm = '<svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 7h10M7.5 2.5l4.5 4.5-4.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    ob_start(); ?>
    <div class="nl-series-grid">
      <?php foreach ($items as $i => $item):
          $label       = get_field('label', $item->ID);
          $description = get_field('description', $item->ID);
          $link_url    = get_field('link_url', $item->ID) ?: $projects_url;
          $delay_class = 'nl-d' . ($i + 2);
          ?>
      <div class="nl-series-card nl-reveal <?php echo esc_attr($delay_class); ?>">
        <?php if ($label): ?>
        <div class="nl-series-label"><?php echo esc_html($label); ?></div>
        <?php endif; ?>
        <div class="nl-series-name"><?php echo esc_html($item->post_title); ?></div>
        <?php if ($description): ?>
        <div class="nl-series-desc"><?php echo wp_kses_post($description); ?></div>
        <?php endif; ?>
        <a href="<?php echo esc_url($link_url); ?>" class="nl-series-link">
          <span class="t" data-en="See Editions" data-ro="Vezi Edițiile">See Editions</span>
          <?php echo $arrow_sm; ?>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php return ob_get_clean();
});
