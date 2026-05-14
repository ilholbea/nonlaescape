<?php
// Projects page — included from inject-elementor.php
/** @var string $home_url */
/** @var int $i */

$proj_header_html = <<<HTML
<div class="nl-page-header">
  <div class="nl-page-header-inner">
    <div class="nl-breadcrumb">
      <a href="{$home_url}">Nonla</a> / <span class="t" data-en="Projects" data-ro="Proiecte">Projects</span>
    </div>
    <h1 class="nl-page-title nl-reveal">
      <span class="t" data-en="Our" data-ro="Călătoriile">Our</span><br>
      <em><span class="t" data-en="Escapes." data-ro="Noastre.">Escapes.</span></em>
    </h1>
    <p class="nl-page-subtitle nl-reveal t"
       data-en="Every edition of Vietnam Escape and Vietnam Undiscovered — past, present, and upcoming. Find the one that calls to you."
       data-ro="Fiecare ediție a Vietnam Escape și Vietnam Undiscovered — trecute, prezente și viitoare. Găsește-o pe cea care te cheamă.">
      Every edition of Vietnam Escape and Vietnam Undiscovered — past, present, and upcoming. Find the one that calls to you.
    </p>
  </div>
</div>
HTML;

$proj_data = [
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $proj_header_html)], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_sc(nl_id($i++), '[nonla_projects]')], nl_col_settings())]),
];
