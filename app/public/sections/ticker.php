<?php
// Home — Ticker / announcement bar section
// Uses: (no external vars needed — self-contained)

$ticker_items = [
    ['Vietnam Escape',        'Vietnam Escape'],
    ['Vietnam Undiscovered',  'Vietnam Nedescoperit'],
    ['Small Groups',          'Grupuri Mici'],
    ['Authentic Experiences', 'Experiențe Autentice'],
    ['Expert Coordinators',   'Coordonatori Experți'],
    ['All-Inclusive',         'Totul Inclus'],
    ['Cultural Travel',       'Călătorii Culturale'],
    ['Since 2020',            'Din 2020'],
];
$ticker_inner = '';
foreach (array_merge($ticker_items, $ticker_items) as [$en, $ro]) {
    $ticker_inner .= '<span class="nl-ticker-item"><span class="nl-ticker-sep"></span>'
        . '<span class="t" data-en="' . esc_attr($en) . '" data-ro="' . esc_attr($ro) . '">' . esc_html($en) . '</span>'
        . '</span>';
}
$ticker_html = '<div class="nl-ticker-wrap"><div class="nl-ticker">' . $ticker_inner . '</div></div>';
