<?php
// Home — Series section (renders via [nl_series] shortcode)

$series_html = <<<'HTML'
<section class="nl-series-wrap">
  <div class="nl-section-inner">
    <div class="nl-eyebrow nl-reveal">
      <span class="t" data-en="Our Escapes" data-ro="Călătoriile Noastre">Our Escapes</span>
    </div>
    <h2 class="nl-section-title nl-reveal nl-d1">
      <span class="t" data-en="Two Series." data-ro="Două Serii.">Two Series.</span><br>
      <em><span class="t" data-en="One Vietnam." data-ro="Un Vietnam.">One Vietnam.</span></em>
    </h2>
    [nl_series]
  </div>
</section>
HTML;
