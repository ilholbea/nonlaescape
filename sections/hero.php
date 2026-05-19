<?php
// Home — Hero section — included from inject-elementor.php
/** @var string $photos */
/** @var string $videos */
/** @var string $projects_url */
/** @var string $home_url */
/** @var string $arrow */

$hero_html = <<<HTML
<section class="nl-hero">
  <video class="nl-hero-video" autoplay muted loop playsinline preload="auto">
    <source src="{$videos}hero-video-1080p.mp4" type="video/mp4">
  </video>
  <div class="nl-hero-overlay"></div>
  <div class="nl-hero-content">
    <div class="nl-hero-eyebrow">
      <span class="t" data-en="Escape the ordinary. Discover the unknown." data-ro="Evadează din obișnuit. Descoperă necunoscutul.">Escape the ordinary. Discover the unknown.</span>
    </div>
    <h1>
      <span class="t" data-en="Cultural" data-ro="Călătorii">Cultural</span><br>
      <em><span class="t" data-en="Escapes" data-ro="Culturale">Escapes</span></em><br>
      <span class="t" data-en="Through Vietnam" data-ro="Prin Vietnam">Through Vietnam</span>
    </h1>
    <div class="nl-hero-row">
      <div>
        <p class="nl-hero-sub t"
           data-en="Nonla organizes immersive small-group trips to Vietnam — authentic experiences designed around real connection with the country and its people."
           data-ro="Nonla organizează experiențe de grup în Vietnam — autentice, menite să creeze conexiuni reale cu oamenii și locurile pe care le vizitezi.">
          Nonla organizes immersive small-group trips to Vietnam — authentic experiences designed around real connection with the country and its people.
        </p>
        <div class="nl-hero-actions">
          <a href="{$projects_url}" class="nl-btn-primary">
            <span class="t" data-en="See Our Projects" data-ro="Vezi Proiectele">See Our Projects</span>
            {$arrow}
          </a>
          <a href="{$home_url}#about" class="nl-btn-ghost-light">
            <span class="t" data-en="Learn About Us" data-ro="Despre Noi">Learn About Us</span>
          </a>
        </div>
      </div>
      <div class="nl-hero-stats">
        <div>
          <div class="nl-stat-n">5+</div>
          <div class="nl-stat-l t" data-en="Years" data-ro="Ani">Years</div>
        </div>
        <div>
          <div class="nl-stat-n">5</div>
          <div class="nl-stat-l t" data-en="Editions" data-ro="Ediții">Editions</div>
        </div>
        <div>
          <div class="nl-stat-n">100+</div>
          <div class="nl-stat-l t" data-en="Travelers" data-ro="Participanți">Travelers</div>
        </div>
      </div>
    </div>
    [nl_countdown]
  </div>
</section>
HTML;
