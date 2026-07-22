<?php
// Legal page — included from inject-elementor.php
/** @var string $home_url */
/** @var string $contact_email */
/** @var int $i */

$legal_header_html = <<<HTML
<div class="nl-page-header">
  <div class="nl-page-header-inner">
    <div class="nl-breadcrumb">
      <a href="{$home_url}">Nonla</a> / <span class="t" data-en="Legal Notice" data-ro="Mențiuni Legale">Legal Notice</span>
    </div>
    <h1 class="nl-page-title nl-reveal">
      <span class="t" data-en="Legal" data-ro="Mențiuni">Legal</span><br>
      <em><span class="t" data-en="Notice." data-ro="Legale.">Notice.</span></em>
    </h1>
    <p class="nl-page-subtitle nl-reveal t"
       data-en="Organization details, terms of service, and legal information for Asociația Nonla Cultural Escape."
       data-ro="Detalii despre organizație, termenii serviciului și informații legale pentru Asociația Nonla Cultural Escape.">
      Organization details, terms of service, and legal information for Asociația Nonla Cultural Escape.
    </p>
  </div>
</div>
HTML;

$legal_content_html = <<<HTML
<div class="nl-legal-wrap">
  <h2>About the Organization</h2>
  <p><strong>Asociația Nonla Cultural Escape</strong> is a Romanian non-governmental organization (NGO) registered under Romanian law, dedicated to cultural exchange and sustainable travel.</p>
  <ul>
    <li><strong>Legal name:</strong> Asociația Nonla Cultural Escape</li>
    <li><strong>CIF (Tax ID):</strong> 49845083</li>
    <li><strong>Registered address:</strong> Câmpulung Muscel, Argeș County, Romania</li>
    <li><strong>Contact:</strong> {$contact_email}</li>
  </ul>

  <h2>Services</h2>
  <p>Nonla Cultural Escape organizes small-group cultural travel experiences to Vietnam. All trips are all-inclusive packages covering flights, accommodation, transfers, and guided experiences.</p>

  <h2>Booking &amp; Payments</h2>
  <p>Reservations are confirmed upon receipt of a deposit. Full payment terms are communicated at the time of booking. Nonla reserves the right to cancel or modify itineraries due to force majeure events.</p>

  <h2>Privacy &amp; Data</h2>
  <p>We collect only the personal data necessary to organize your trip (name, contact details, travel documents). Data is stored securely and never sold to third parties. You may request access to or deletion of your data at any time by contacting us at {$contact_email}.</p>
  <p>This site uses cookies for basic analytics only. No advertising cookies are used.</p>

  <h2>Liability</h2>
  <p>Nonla Cultural Escape acts as an organizer and coordinator. We work with vetted local partners for accommodation and transport. While every care is taken, Nonla is not liable for events beyond our control including weather, political events, or acts of force majeure.</p>

  <h2>Intellectual Property</h2>
  <p>All content on this website — text, photos, and design — is the property of Asociația Nonla Cultural Escape unless otherwise stated. Reproduction without written permission is prohibited.</p>

  <h2>Governing Law</h2>
  <p>These terms are governed by Romanian law. Any disputes shall be resolved in the courts of Argeș County, Romania.</p>
</div>
HTML;

$legal_data = [
    nl_section(nl_id($i++), nl_sec(), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $legal_header_html)], nl_col_settings())]),
    nl_section(nl_id($i++), nl_sec(['background_background' => 'classic', 'background_color' => '#FAF5EC']), [nl_column(nl_id($i++), 100, [nl_html(nl_id($i++), $legal_content_html)], nl_col_settings())]),
];
