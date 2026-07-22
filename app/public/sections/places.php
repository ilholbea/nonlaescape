<?php
// Home — Destinations / Places section (renders via [nl_places] shortcode)

$dest_html = <<<'HTML'
<section class="nl-dest-wrap" id="destinations">
  <div class="nl-dest-header">
    <div class="nl-eyebrow nl-reveal">
      <span class="t" data-en="Where We Go" data-ro="Unde Mergem">Where We Go</span>
    </div>
    <h2 class="nl-section-title nl-reveal nl-d1" style="margin-bottom:0;">
      <span class="t" data-en="Places That Stay" data-ro="Locuri Care Rămân">Places That Stay</span><br>
      <em><span class="t" data-en="With You." data-ro="Cu Tine.">With You.</span></em>
    </h2>
  </div>
  [nl_places]
</section>
HTML;
