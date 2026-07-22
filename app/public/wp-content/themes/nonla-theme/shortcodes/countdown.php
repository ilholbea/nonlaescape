<?php
// [nl_countdown] — live countdown to next upcoming nl_project, overlaid on the ticker

add_shortcode('nl_countdown', function () {
    if (!function_exists('get_field')) return '';

    $projects = get_posts([
        'post_type'      => 'nl_project',
        'posts_per_page' => 1,
        'meta_query'     => [['key' => 'status', 'value' => 'upcoming']],
        'orderby'        => 'meta_value',
        'meta_key'       => 'year',
        'order'          => 'ASC',
    ]);

    if (empty($projects)) return '';

    $pid      = $projects[0]->ID;
    $dates_en = get_field('dates_en', $pid);
    $year_acf = get_field('year', $pid);

    // Extract start date — with or without year embedded in dates_en
    if (preg_match('/([A-Za-z]+\s+\d{1,2})(?:\s*[–\-]\s*(?:[A-Za-z]+\s+)?\d{1,2})?,?\s*(\d{4})/', $dates_en, $m)) {
        $ts = strtotime($m[1] . ', ' . $m[2]);
    } elseif (preg_match('/([A-Za-z]+\s+\d{1,2})/', $dates_en, $m) && $year_acf) {
        $ts = strtotime($m[1] . ', ' . $year_acf);
    } else {
        return '';
    }

    if (!$ts || $ts <= time()) return '';

    ob_start(); ?>
    <div class="nl-countdown" data-ts="<?php echo esc_attr($ts * 1000); ?>">
        <div class="nl-cd-label t" data-en="Next Trip" data-ro="Următoarea Călătorie">Next Trip</div>
        <div class="nl-cd-units">
            <div class="nl-cd-unit"><span class="nl-cd-n" data-unit="months">--</span><span class="nl-cd-l t" data-en="Mo" data-ro="Lu">Mo</span></div>
            <div class="nl-cd-sep"></div>
            <div class="nl-cd-unit"><span class="nl-cd-n" data-unit="days">--</span><span class="nl-cd-l t" data-en="Days" data-ro="Zile">Days</span></div>
            <div class="nl-cd-sep"></div>
            <div class="nl-cd-unit"><span class="nl-cd-n" data-unit="hours">--</span><span class="nl-cd-l t" data-en="Hrs" data-ro="Ore">Hrs</span></div>
            <div class="nl-cd-sep"></div>
            <div class="nl-cd-unit"><span class="nl-cd-n" data-unit="minutes">--</span><span class="nl-cd-l t" data-en="Min" data-ro="Min">Min</span></div>
            <div class="nl-cd-sep"></div>
            <div class="nl-cd-unit"><span class="nl-cd-n" data-unit="seconds">--</span><span class="nl-cd-l t" data-en="Sec" data-ro="Sec">Sec</span></div>
        </div>
    </div>
    <script>
    (function () {
        var el = document.querySelector('.nl-countdown');
        if (!el) return;
        var target = parseInt(el.dataset.ts, 10);
        function tick() {
            var diff = target - Date.now();
            if (diff <= 0) { el.remove(); return; }
            var totalS = Math.floor(diff / 1000);
            var months  = Math.floor(totalS / (30 * 24 * 3600));
            var days    = Math.floor((totalS % (30 * 24 * 3600)) / (24 * 3600));
            var hours   = Math.floor((totalS % (24 * 3600)) / 3600);
            var minutes = Math.floor((totalS % 3600) / 60);
            var seconds = totalS % 60;
            el.querySelector('[data-unit="months"]').textContent  = String(months).padStart(2, '0');
            el.querySelector('[data-unit="days"]').textContent    = String(days).padStart(2, '0');
            el.querySelector('[data-unit="hours"]').textContent   = String(hours).padStart(2, '0');
            el.querySelector('[data-unit="minutes"]').textContent = String(minutes).padStart(2, '0');
            el.querySelector('[data-unit="seconds"]').textContent = String(seconds).padStart(2, '0');
        }
        tick();
        setInterval(tick, 1000);
    })();
    </script>
    <?php return ob_get_clean();
});
