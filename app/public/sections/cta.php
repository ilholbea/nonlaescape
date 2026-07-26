<?php
// Home — CTA / Contact section — included from inject-elementor.php
/** @var string $photos */
/** @var string $projects_url */
/** @var string $contact_email */
/** @var string $arrow */

$cta_html = <<<HTML
<div class="nl-cta-wrap" id="contact" style="background-image:url('{$photos}ninhbinh.jpg');">
  <div class="nl-cta-overlay"></div>
  <div class="nl-cta-inner">
    <div class="nl-eyebrow nl-eyebrow-center nl-eyebrow-gold nl-reveal">
      <span class="t" data-en="Ready to Escape?" data-ro="Gata să Evadezi?">Ready to Escape?</span>
    </div>
    <h2 class="nl-reveal nl-d1">
      <span class="t" data-en="Find Your" data-ro="Găsește-ți">Find Your</span><br>
      <em><span class="t" data-en="Escape." data-ro="Evadarea.">Escape.</span></em>
    </h2>
    <p class="nl-reveal nl-d2 t"
       data-en="Browse our Vietnam escapes — past, present, and upcoming — and find the one that calls to you."
       data-ro="Explorează călătoriile noastre din Vietnam — trecute, prezente și viitoare — și găsește-o pe cea care te cheamă.">
      Browse our Vietnam escapes — past, present, and upcoming — and find the one that calls to you.
    </p>
    <div class="nl-cta-actions nl-reveal nl-d3">
      <a href="{$projects_url}" class="nl-btn-primary">
        <span class="t" data-en="See All Projects" data-ro="Vezi Toate Proiectele">See All Projects</span>
        {$arrow}
      </a>
      <a href="mailto:{$contact_email}" class="nl-btn-ghost-light">{$contact_email}</a>
    </div>
  </div>
</div>
HTML;
