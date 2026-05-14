<?php
// Gallery page — included from inject-elementor.php
/** @var string $home_url */
/** @var int $i */

$gallery_header_html = <<<HTML
<div class="nl-page-header">
  <div class="nl-page-header-inner">
    <div class="nl-breadcrumb">
      <a href="{$home_url}">Nonla</a> / <span class="t" data-en="Gallery" data-ro="Galerie">Gallery</span>
    </div>
    <h1 class="nl-page-title nl-reveal">
      <span class="t" data-en="Moments" data-ro="Momente">Moments</span><br>
      <em><span class="t" data-en="From Vietnam." data-ro="Din Vietnam.">From Vietnam.</span></em>
    </h1>
    <p class="nl-page-subtitle nl-reveal t"
       data-en="A glimpse into the places, people, and experiences that make every Nonla escape unforgettable."
       data-ro="O privire asupra locurilor, oamenilor și experiențelor care fac fiecare călătorie Nonla de neuitat.">
      A glimpse into the places, people, and experiences that make every Nonla escape unforgettable.
    </p>
  </div>
</div>
HTML;

$gallery_data = [
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $gallery_header_html)], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), '<div class="nl-gallery-wrap">[nl_gallery_items]</div>')], nl_col_settings())]),
];
