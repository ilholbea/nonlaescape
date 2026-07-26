<?php
// Home — Ticker / announcement bar section
// Uses: (no external vars needed — self-contained)

$ticker_items = [
    ['Cultural Exchange',     'Schimb Cultural'],
    ['Human Connection',      'Conexiuni Reale'],
    ['More Than a Trip',      'Mai Mult Decât o Călătorie'],
    ['Small Groups',          'Grupuri Mici'],
    ['Authentic Experiences', 'Experiențe Autentice'],
    ['Personal Growth',       'Dezvoltare Personală'],
    ['Cultural Immersion',    'Imersiune Culturală'],
    ['Since 2020',            'Din 2020'],
];
$ticker_inner = '';
foreach (array_merge($ticker_items, $ticker_items) as [$en, $ro]) {
    $ticker_inner .= '<span class="nl-ticker-item"><span class="nl-ticker-sep"></span>'
        . '<span class="t" data-en="' . esc_attr($en) . '" data-ro="' . esc_attr($ro) . '">' . esc_html($en) . '</span>'
        . '</span>';
}
$ticker_html = '<div class="nl-ticker-wrap"><div class="nl-ticker">' . $ticker_inner . '</div></div>';
