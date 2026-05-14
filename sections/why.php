<?php
// Home — Why Nonla section (renders via [nl_why_items] shortcode)

$why_html = <<<'HTML'
<section class="nl-why-wrap" id="why">
  <div class="nl-section-inner">
    <div class="nl-eyebrow nl-reveal">
      <span class="t" data-en="Why Nonla" data-ro="De Ce Nonla">Why Nonla</span>
    </div>
    <h2 class="nl-section-title nl-reveal nl-d1">
      <span class="t" data-en="The Difference" data-ro="Diferența">The Difference</span><br>
      <em><span class="t" data-en="You'll Feel." data-ro="Pe Care O Vei Simți.">You'll Feel.</span></em>
    </h2>
    [nl_why_items]
  </div>
</section>
HTML;
