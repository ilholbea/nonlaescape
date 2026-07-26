<?php
// Home — About section — included from inject-elementor.php
/** @var string $photos */

$about_html = <<<HTML
<section class="nl-about-wrap" id="about">
  <div class="nl-section-inner">
    <div class="nl-about-grid">
      <div class="nl-about-photo-stack nl-reveal">
        <img class="nl-about-photo-main" src="{$photos}tamcoc.jpg" alt="Tam Coc, Ninh Binh">
        <img class="nl-about-photo-float" src="{$photos}boats.jpg" alt="Local boats">
      </div>
      <div class="nl-about-text">
        <div class="nl-eyebrow nl-reveal">
          <span class="t" data-en="Who We Are" data-ro="Cine Suntem">Who We Are</span>
        </div>
        <h2 class="nl-section-title nl-reveal nl-d1">
          <span class="t" data-en="More Than" data-ro="Mai Mult">More Than</span><br>
          <em><span class="t" data-en="a Trip." data-ro="Decât O Călătorie.">a Trip.</span></em>
        </h2>
        <p class="nl-reveal nl-d2 t"
           data-en="Nonla Cultural Escape is a non-governmental organization founded in Romania with a global vision — offering a safe and inclusive space for cultural exchange and personal growth."
           data-ro="Nonla Cultural Escape este o organizație neguvernamentală fondată în România cu o viziune globală — oferind un spațiu sigur și incluziv pentru schimb cultural și dezvoltare personală.">
          Nonla Cultural Escape is a non-governmental organization founded in Romania with a global vision — offering a safe and inclusive space for cultural exchange and personal growth.
        </p>
        <p class="nl-reveal nl-d3 t"
           data-en="We organize small-group journeys through Vietnam with everything handled — flights, accommodation, transfers, and expert coordinators who speak Romanian, English, and Vietnamese. Our mission: create meaningful connections across borders."
           data-ro="Organizăm călătorii în grupuri mici prin Vietnam, cu totul inclus — zboruri, cazare, transferuri și coordonatori experți care vorbesc română, engleză și vietnameză. Misiunea noastră: să creăm conexiuni autentice peste granițe.">
          We organize small-group journeys through Vietnam with everything handled — flights, accommodation, transfers, and expert coordinators who speak Romanian, English, and Vietnamese. Our mission: create meaningful connections across borders.
        </p>
        <blockquote class="nl-about-quote nl-reveal nl-d4">
          <span class="t"
                data-en="Building connections through shared adventures and creating a supportive environment for everyone."
                data-ro="Construim conexiuni prin aventuri comune și creăm un mediu de susținere pentru toată lumea.">
            Building connections through shared adventures and creating a supportive environment for everyone.
          </span>
        </blockquote>
      </div>
    </div>
  </div>
</section>
HTML;
