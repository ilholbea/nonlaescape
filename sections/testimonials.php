<?php
// Home — Testimonials section (renders via [nl_testimonials] shortcode)

$testimonials_html = <<<'HTML'
<section class="nl-testimonials-wrap">
  <div class="nl-section-inner">
    <div class="nl-eyebrow nl-reveal">
      <span class="t" data-en="What Travelers Say" data-ro="Ce Spun Călătorii">What Travelers Say</span>
    </div>
    <h2 class="nl-section-title nl-reveal nl-d1">
      <span class="t" data-en="Their" data-ro="Povestea">Their</span><br>
      <em><span class="t" data-en="Stories." data-ro="Lor.">Stories.</span></em>
    </h2>
    [nl_testimonials]
  </div>
</section>
HTML;
