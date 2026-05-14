<?php
// Team page — included from inject-elementor.php
/** @var string $home_url */
/** @var string $projects_url */
/** @var string $arrow */
/** @var int $i */

$team_header_html = <<<HTML
<div class="nl-page-header">
  <div class="nl-page-header-inner">
    <div class="nl-breadcrumb">
      <a href="{$home_url}">Nonla</a> / <span class="t" data-en="Team" data-ro="Echipă">Team</span>
    </div>
    <h1 class="nl-page-title nl-reveal">
      <span class="t" data-en="The People" data-ro="Oamenii">The People</span><br>
      <em><span class="t" data-en="Behind the Escapes." data-ro="Din Spatele Călătoriilor.">Behind the Escapes.</span></em>
    </h1>
    <p class="nl-page-subtitle nl-reveal t"
       data-en="Meet the coordinators who make it all happen — on the ground in Vietnam, every single day of the escape."
       data-ro="Cunoaște coordonatorii care fac totul posibil — pe teren în Vietnam, în fiecare zi a călătoriei.">
      Meet the coordinators who make it all happen — on the ground in Vietnam, every single day of the escape.
    </p>
  </div>
</div>
HTML;

$team_cta_html = <<<HTML
<div class="nl-team-cta">
  <div class="nl-team-cta-inner">
    <div class="nl-eyebrow nl-eyebrow-center nl-reveal">
      <span class="t" data-en="Our Approach" data-ro="Abordarea Noastră">Our Approach</span>
    </div>
    <h2 class="nl-reveal nl-d1">
      <span class="t" data-en="We Travel" data-ro="Călătorim">We Travel</span><br>
      <em><span class="t" data-en="With You." data-ro="Cu Voi.">With You.</span></em>
    </h2>
    <p class="nl-reveal nl-d2 t"
       data-en="Our coordinators aren't just guides — they're travel companions who speak your language, know Vietnam deeply, and are with you from the first flight to the last goodbye."
       data-ro="Coordonatorii noștri nu sunt doar ghizi — sunt însoțitori de călătorie care vorbesc limba ta, cunosc Vietnam în profunzime și sunt alături de tine de la primul zbor până la ultimul rămas bun.">
      Our coordinators aren't just guides — they're travel companions who speak your language, know Vietnam deeply, and are with you from the first flight to the last goodbye.
    </p>
    <a href="{$projects_url}" class="nl-btn-primary nl-reveal nl-d3">
      <span class="t" data-en="See Our Escapes" data-ro="Vezi Călătoriile Noastre">See Our Escapes</span>
      {$arrow}
    </a>
  </div>
</div>
HTML;

$team_data = [
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $team_header_html)], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_sc(nl_id($i++), '[nonla_team]')], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $team_cta_html)], nl_col_settings())]),
];
