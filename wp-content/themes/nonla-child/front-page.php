<?php
$photos = get_stylesheet_directory_uri() . '/assets/photos/';
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Vietnam Escape Vol. 5 — Nonla Cultural Escape</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital@1&display=swap" rel="stylesheet">
<?php wp_head(); ?>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --black:  #080808;
  --dark:   #111;
  --card:   #161616;
  --border: #252525;
  --gold:   #e8a040;
  --gold2:  #c97b28;
  --rust:   #c0402f;
  --white:  #f4efe9;
  --muted:  #7a7570;
  --head:   'Bebas Neue', sans-serif;
  --body:   'Inter', sans-serif;
  --serif:  'Playfair Display', serif;
}

html { scroll-behavior: smooth; }
body { background: var(--black); color: var(--white); font-family: var(--body); overflow-x: hidden; }
a { color: inherit; text-decoration: none; }
img { display: block; max-width: 100%; }

/* ── NAV ── */
.nav {
  position: fixed; top: 0; left: 0; right: 0; z-index: 200;
  display: flex; align-items: center; justify-content: space-between;
  padding: 28px 52px;
  transition: background .35s, padding .35s, backdrop-filter .35s;
}
.nav.stuck {
  background: rgba(8,8,8,.9); backdrop-filter: blur(16px);
  padding: 16px 52px; border-bottom: 1px solid var(--border);
}
.logo { font-family: var(--head); font-size: 24px; letter-spacing: .1em; }
.logo span { color: var(--gold); }
.nav-links { display: flex; gap: 32px; list-style: none; }
.nav-links a { font-size: 12px; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); transition: color .2s; }
.nav-links a:hover { color: var(--white); }
.nav-cta {
  background: var(--gold); color: var(--black); font-size: 12px; font-weight: 700;
  letter-spacing: .12em; text-transform: uppercase; padding: 11px 26px; border-radius: 2px;
  transition: background .2s, transform .2s;
}
.nav-cta:hover { background: var(--gold2); transform: translateY(-1px); }

/* ── HERO ── */
.hero {
  position: relative; min-height: 100vh; display: flex; align-items: flex-end;
  overflow: hidden;
}
.hero-photo {
  position: absolute; inset: 0;
  background: url('<?php echo $photos; ?>hero.jpg') center center / cover no-repeat;
}
.hero-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(
    to bottom,
    rgba(8,8,8,.55) 0%,
    rgba(8,8,8,.2) 35%,
    rgba(8,8,8,.5) 65%,
    rgba(8,8,8,.95) 100%
  );
}
.hero-content {
  position: relative; z-index: 2;
  width: 100%; max-width: 1200px; margin: 0 auto;
  padding: 0 52px 80px;
}
.hero-eyebrow {
  display: inline-flex; align-items: center; gap: 10px;
  font-size: 11px; letter-spacing: .28em; text-transform: uppercase;
  color: var(--gold); font-weight: 600; margin-bottom: 20px;
}
.hero-eyebrow::before { content: ''; width: 28px; height: 1px; background: var(--gold); }
.hero h1 {
  font-family: var(--head);
  font-size: clamp(80px, 11vw, 160px);
  line-height: .88; letter-spacing: .015em; text-transform: uppercase;
  margin-bottom: 28px;
}
.hero h1 em { font-style: normal; -webkit-text-stroke: 1.5px var(--white); color: transparent; }
.hero-row {
  display: flex; align-items: flex-end; justify-content: space-between; gap: 40px; flex-wrap: wrap;
}
.hero-sub {
  font-size: 17px; color: rgba(244,239,233,.7); max-width: 420px;
  line-height: 1.75; font-weight: 300;
}
.hero-actions { display: flex; gap: 14px; align-items: center; flex-wrap: wrap; }
.btn-primary {
  display: inline-flex; align-items: center; gap: 10px;
  background: var(--gold); color: var(--black); font-weight: 700;
  font-size: 12px; letter-spacing: .12em; text-transform: uppercase;
  padding: 15px 34px; border-radius: 2px;
  transition: background .2s, transform .2s, box-shadow .2s;
}
.btn-primary:hover { background: var(--gold2); transform: translateY(-2px); box-shadow: 0 14px 36px rgba(232,160,64,.3); }
.btn-ghost {
  display: inline-flex; align-items: center; gap: 8px;
  font-size: 12px; letter-spacing: .12em; text-transform: uppercase; font-weight: 500;
  color: rgba(244,239,233,.65); border-bottom: 1px solid var(--border);
  padding: 15px 4px; transition: color .2s, border-color .2s;
}
.btn-ghost:hover { color: var(--white); border-color: var(--white); }
.hero-stats {
  display: flex; gap: 36px; flex-shrink: 0;
  border-left: 1px solid var(--border); padding-left: 36px;
}
.stat-n { font-family: var(--head); font-size: 44px; line-height: 1; color: var(--gold); }
.stat-l { font-size: 10px; text-transform: uppercase; letter-spacing: .18em; color: var(--muted); margin-top: 4px; }

/* ── TICKER ── */
.ticker-wrap { background: var(--gold); overflow: hidden; padding: 14px 0; }
.ticker { display: flex; animation: tick 25s linear infinite; white-space: nowrap; }
.ticker-item {
  display: inline-flex; align-items: center; gap: 20px;
  padding: 0 28px; font-size: 12px; font-weight: 700;
  letter-spacing: .22em; text-transform: uppercase; color: var(--black);
}
.ticker-item .sep { width: 4px; height: 4px; background: rgba(0,0,0,.35); border-radius: 50%; }
@keyframes tick { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

/* ── DESTINATIONS ── */
.destinations-wrap { padding: 100px 0 0; }
.section-inner { max-width: 1200px; margin: 0 auto; padding: 0 52px; }
.eyebrow {
  display: inline-flex; align-items: center; gap: 10px;
  font-size: 11px; letter-spacing: .25em; text-transform: uppercase;
  color: var(--gold); font-weight: 600; margin-bottom: 18px;
}
.eyebrow::before { content: ''; width: 22px; height: 1px; background: var(--gold); }
.section-title {
  font-family: var(--head); font-size: clamp(52px, 6vw, 88px);
  line-height: .93; letter-spacing: .02em; text-transform: uppercase; margin-bottom: 52px;
}
.dest-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 3px; }
.dest-card {
  position: relative; overflow: hidden; aspect-ratio: 3/4; cursor: default;
}
.dest-card img {
  width: 100%; height: 100%; object-fit: cover;
  transition: transform .6s ease; display: block;
}
.dest-card:hover img { transform: scale(1.06); }
.dest-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(8,8,8,.92) 0%, rgba(8,8,8,.3) 50%, transparent 100%);
  display: flex; flex-direction: column; justify-content: flex-end;
  padding: 28px 24px;
}
.dest-tag {
  font-size: 10px; font-weight: 700; letter-spacing: .2em;
  text-transform: uppercase; color: var(--gold); margin-bottom: 6px;
}
.dest-name {
  font-family: var(--head); font-size: 32px; letter-spacing: .04em;
  text-transform: uppercase; line-height: 1; margin-bottom: 6px;
}
.dest-desc { font-size: 12px; color: rgba(244,239,233,.6); line-height: 1.5; font-weight: 300; }

/* ── ABOUT (2-col) ── */
.about-wrap { padding: 100px 0; }
.about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
.about-photo-stack { position: relative; }
.about-photo-main {
  width: 100%; aspect-ratio: 4/5; object-fit: cover; border-radius: 3px;
}
.about-photo-float {
  position: absolute; bottom: -28px; right: -28px;
  width: 55%; aspect-ratio: 1/1; object-fit: cover; border-radius: 3px;
  border: 4px solid var(--black);
}
.about-text { padding-left: 20px; }
.about-text p {
  font-size: 16px; color: var(--muted); line-height: 1.85; font-weight: 300; margin-bottom: 18px;
}
.about-text p strong { color: var(--white); font-weight: 500; }
.about-quote {
  font-family: var(--serif); font-style: italic; font-size: 22px;
  color: var(--white); line-height: 1.5;
  border-left: 2px solid var(--gold); padding-left: 22px; margin: 32px 0;
}
.includes-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 28px; }
.include-item {
  display: flex; align-items: flex-start; gap: 10px;
  font-size: 13px; color: var(--muted); line-height: 1.5;
}
.include-item::before { content: '✓'; color: var(--gold); font-weight: 700; flex-shrink: 0; margin-top: 1px; }

/* ── ITINERARY ── */
.itin-wrap { background: var(--dark); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 100px 0; }
.itin-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 52px; flex-wrap: wrap; gap: 24px; }
.price-tag { text-align: right; }
.price-tag .amount { font-family: var(--head); font-size: 64px; color: var(--gold); line-height: 1; }
.price-tag .from { font-size: 11px; letter-spacing: .15em; text-transform: uppercase; color: var(--muted); margin-bottom: 4px; }
.itin-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 3px; }
.itin-stop {
  background: var(--card); border: 1px solid var(--border);
  padding: 28px 22px; position: relative; overflow: hidden;
  transition: border-color .2s, background .2s;
}
.itin-stop:hover { border-color: var(--gold); background: #1a1a1a; }
.itin-stop::after {
  content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 2px;
  background: linear-gradient(90deg, var(--gold), var(--rust));
  transform: scaleX(0); transform-origin: left; transition: transform .3s;
}
.itin-stop:hover::after { transform: scaleX(1); }
.stop-day { font-size: 10px; letter-spacing: .2em; text-transform: uppercase; color: var(--muted); margin-bottom: 10px; }
.stop-city { font-family: var(--head); font-size: 26px; letter-spacing: .04em; text-transform: uppercase; margin-bottom: 8px; color: var(--white); }
.stop-desc { font-size: 12px; color: var(--muted); line-height: 1.6; font-weight: 300; }
.stop-icon { font-size: 22px; margin-bottom: 14px; }

/* ── GALLERY ROW ── */
.gallery-wrap { padding: 4px 0; }
.gallery-row { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 4px; }
.gallery-photo { overflow: hidden; }
.gallery-photo img { width: 100%; height: 100%; object-fit: cover; transition: transform .6s; display: block; }
.gallery-photo:hover img { transform: scale(1.04); }
.gallery-photo.tall { aspect-ratio: 16/9; }
.gallery-photo.short { aspect-ratio: 4/3; }

/* ── WHY ── */
.why-wrap { padding: 100px 0; }
.why-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; background: var(--border); border: 1px solid var(--border); border-radius: 3px; overflow: hidden; margin-top: 52px; }
.why-item { background: var(--card); padding: 36px 32px; transition: background .2s; }
.why-item:hover { background: #1c1c1c; }
.why-icon { font-size: 28px; margin-bottom: 18px; }
.why-title { font-family: var(--head); font-size: 20px; letter-spacing: .05em; text-transform: uppercase; margin-bottom: 10px; }
.why-desc { font-size: 13px; color: var(--muted); line-height: 1.7; font-weight: 300; }

/* ── CTA ── */
.cta-wrap {
  position: relative; overflow: hidden;
  background: url('<?php echo $photos; ?>halong.jpg') center 40% / cover no-repeat;
}
.cta-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(rgba(8,8,8,.82), rgba(8,8,8,.88));
}
.cta-inner {
  position: relative; z-index: 2; text-align: center;
  max-width: 1200px; margin: 0 auto; padding: 130px 52px;
}
.cta-inner h2 {
  font-family: var(--head); font-size: clamp(60px, 9vw, 130px);
  line-height: .9; letter-spacing: .02em; text-transform: uppercase; margin-bottom: 24px;
}
.cta-inner h2 span { color: var(--gold); }
.cta-inner p { font-size: 17px; color: var(--muted); max-width: 460px; margin: 0 auto 44px; font-weight: 300; line-height: 1.75; }
.cta-actions { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
.dates-badge {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(232,160,64,.12); border: 1px solid rgba(232,160,64,.3);
  color: var(--gold); font-size: 12px; font-weight: 600; letter-spacing: .1em;
  text-transform: uppercase; padding: 8px 18px; border-radius: 100px; margin-bottom: 28px;
}

/* ── FOOTER ── */
.footer { background: var(--dark); border-top: 1px solid var(--border); }
.footer-inner { max-width: 1200px; margin: 0 auto; padding: 60px 52px 36px; }
.footer-top { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 60px; margin-bottom: 44px; }
.footer-logo { font-family: var(--head); font-size: 22px; letter-spacing: .1em; margin-bottom: 14px; }
.footer-logo span { color: var(--gold); }
.footer-tagline { font-size: 13px; color: var(--muted); line-height: 1.7; max-width: 260px; font-weight: 300; }
.footer-head { font-size: 10px; font-weight: 700; letter-spacing: .22em; text-transform: uppercase; color: var(--muted); margin-bottom: 18px; }
.footer-links { list-style: none; display: flex; flex-direction: column; gap: 11px; }
.footer-links a { font-size: 13px; color: var(--muted); transition: color .2s; }
.footer-links a:hover { color: var(--white); }
.footer-bottom { border-top: 1px solid var(--border); padding-top: 24px; display: flex; justify-content: space-between; font-size: 12px; color: var(--muted); }

/* ── REVEAL ── */
.reveal { opacity: 0; transform: translateY(28px); transition: opacity .65s ease, transform .65s ease; }
.reveal.on { opacity: 1; transform: none; }
.d1 { transition-delay: .1s; } .d2 { transition-delay: .2s; }
.d3 { transition-delay: .3s; } .d4 { transition-delay: .4s; }
.d5 { transition-delay: .5s; }

/* ── RESPONSIVE ── */
@media (max-width: 960px) {
  .nav { padding: 20px 24px; }
  .nav.stuck { padding: 14px 24px; }
  .nav-links { display: none; }
  .hero-content { padding: 0 24px 64px; }
  .hero-stats { display: none; }
  .section-inner { padding: 0 24px; }
  .dest-grid { grid-template-columns: repeat(2, 1fr); }
  .about-grid { grid-template-columns: 1fr; }
  .about-photo-float { display: none; }
  .about-text { padding-left: 0; }
  .itin-grid { grid-template-columns: repeat(2, 1fr); }
  .gallery-row { grid-template-columns: 1fr; }
  .why-grid { grid-template-columns: 1fr; }
  .footer-top { grid-template-columns: 1fr; gap: 36px; }
  .footer-inner { padding: 48px 24px 28px; }
  .cta-inner { padding: 100px 24px; }
  .about-wrap { padding: 80px 0; }
  .why-wrap { padding: 80px 0; }
  .destinations-wrap { padding: 80px 0 0; }
}
@media (max-width: 540px) {
  .dest-grid { grid-template-columns: 1fr; }
  .itin-grid { grid-template-columns: 1fr; }
  .includes-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<!-- NAV -->
<nav class="nav" id="nav">
  <div class="logo">NONLA<span>.</span></div>
  <ul class="nav-links">
    <li><a href="#destinations">Destinations</a></li>
    <li><a href="#about">About</a></li>
    <li><a href="#itinerary">Itinerary</a></li>
    <li><a href="#why">Why Us</a></li>
  </ul>
  <a href="mailto:contact@nonlaescape.com?subject=Vietnam Escape Vol. 5" class="nav-cta">Reserve a Spot</a>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-photo"></div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <div class="hero-eyebrow">Nonla Cultural Escape · Vol. 5</div>
    <h1>Vietnam<br><em>Escape</em></h1>
    <div class="hero-row">
      <div>
        <p class="hero-sub">14 April – 29 April 2026 · 15 days across Vietnam's most breathtaking landscapes, cities, and waterways.</p>
        <div class="hero-actions" style="margin-top: 28px;">
          <a href="mailto:contact@nonlaescape.com?subject=Vietnam Escape Vol. 5" class="btn-primary">
            Reserve Your Spot
            <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M2.5 7.5h10M8.5 3.5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </a>
          <a href="#itinerary" class="btn-ghost">See the itinerary</a>
        </div>
      </div>
      <div class="hero-stats">
        <div><div class="stat-n">15</div><div class="stat-l">Days</div></div>
        <div><div class="stat-n">5</div><div class="stat-l">Cities</div></div>
        <div><div class="stat-n">€1,895</div><div class="stat-l">All-inclusive</div></div>
      </div>
    </div>
  </div>
</section>

<!-- TICKER -->
<div class="ticker-wrap">
  <div class="ticker">
    <?php
    $items = ['Hanoi', 'Ha Long Bay', 'Ninh Binh', 'Phu Quoc', 'Ho Chi Minh City', '14–29 April 2026', '€1,895 All-Inclusive', 'Flights Included', 'Expert Coordinators', 'Hanoi', 'Ha Long Bay', 'Ninh Binh', 'Phu Quoc', 'Ho Chi Minh City', '14–29 April 2026', '€1,895 All-Inclusive', 'Flights Included', 'Expert Coordinators'];
    foreach ($items as $item): ?>
    <span class="ticker-item"><span class="sep"></span><?php echo $item; ?></span>
    <?php endforeach; ?>
  </div>
</div>

<!-- DESTINATIONS -->
<section class="destinations-wrap" id="destinations">
  <div class="section-inner">
    <div class="eyebrow reveal">5 Destinations</div>
    <div class="section-title reveal d1">Where You're<br>Going.</div>
  </div>
  <div class="dest-grid">
    <div class="dest-card">
      <img src="<?php echo $photos; ?>hanoi.jpg" alt="Hanoi" loading="lazy">
      <div class="dest-overlay">
        <div class="dest-tag">Day 2–3</div>
        <div class="dest-name">Hanoi</div>
        <div class="dest-desc">The Old Quarter, Temple of Literature, street food & Vietnamese coffee culture</div>
      </div>
    </div>
    <div class="dest-card">
      <img src="<?php echo $photos; ?>halong.jpg" alt="Ha Long Bay" loading="lazy">
      <div class="dest-overlay">
        <div class="dest-tag">Day 3 · Day Trip</div>
        <div class="dest-name">Lan Ha Bay</div>
        <div class="dest-desc">UNESCO limestone karsts, emerald waters, boat trip & kayaking</div>
      </div>
    </div>
    <div class="dest-card">
      <img src="<?php echo $photos; ?>ninhbinh.jpg" alt="Ninh Binh" loading="lazy">
      <div class="dest-overlay">
        <div class="dest-tag">Day 4–7</div>
        <div class="dest-name">Ninh Binh</div>
        <div class="dest-desc">Trang An river boats, Mua Cave viewpoint, bicycle rides through rice paddies</div>
      </div>
    </div>
    <div class="dest-card">
      <img src="<?php echo $photos; ?>phuquoc.jpg" alt="Phu Quoc" loading="lazy">
      <div class="dest-overlay">
        <div class="dest-tag">Day 8–11</div>
        <div class="dest-name">Phu Quoc</div>
        <div class="dest-desc">Tropical island, snorkeling, boat tour, hiking & beach life</div>
      </div>
    </div>
  </div>
  <div style="background: var(--card); border-top: 3px solid var(--gold); padding: 28px 52px; display: flex; align-items: center; justify-content: space-between; flex-wrap: gap;">
    <div style="display: flex; align-items: center; gap: 16px; padding: 0 0;">
      <div style="font-size: 28px;">🏙️</div>
      <div>
        <div style="font-family: var(--head); font-size: 22px; letter-spacing: .04em; text-transform: uppercase;">+ Ho Chi Minh City</div>
        <div style="font-size: 12px; color: var(--muted); margin-top: 2px;">Day 12–15 · Reunification Palace, Ben Thanh market, rooftop bars & departure</div>
      </div>
    </div>
    <a href="mailto:contact@nonlaescape.com?subject=Vietnam Escape Vol. 5" class="btn-primary" style="flex-shrink: 0;">Join This Escape →</a>
  </div>
</section>

<!-- ABOUT -->
<section class="about-wrap" id="about">
  <div class="section-inner">
    <div class="about-grid">
      <div class="about-photo-stack reveal">
        <img class="about-photo-main" src="<?php echo $photos; ?>tamcoc.jpg" alt="Tam Coc, Ninh Binh">
        <img class="about-photo-float" src="<?php echo $photos; ?>boats.jpg" alt="Local boats">
      </div>
      <div class="about-text">
        <div class="eyebrow reveal">Who organizes this</div>
        <div class="section-title reveal d1">More Than<br>a Trip.</div>
        <p class="reveal d2">
          <strong>Nonla Cultural Escape</strong> is a Romanian NGO dedicated to authentic cultural travel. We don't do package tourism — every escape is a curated experience designed around real connection with the places and people we visit.
        </p>
        <p class="reveal d3">
          Vietnam Vol. 5 comes with <strong>2–4 expert coordinators</strong> who speak Romanian, English and Vietnamese — on the ground with you from arrival to departure, every single day.
        </p>
        <div class="about-quote reveal d3">"Not just a trip — a shift in how you see the world."</div>
        <div class="includes-grid reveal d4">
          <div class="include-item">International flights (Turkish Airlines)</div>
          <div class="include-item">13 nights, 8.5+ rated hotels</div>
          <div class="include-item">All in-country transfers</div>
          <div class="include-item">Daily breakfast included</div>
          <div class="include-item">Boat trip + snorkeling</div>
          <div class="include-item">Hiking + bicycle excursions</div>
          <div class="include-item">Vietnamese coffee workshop</div>
          <div class="include-item">Expert coordinators daily</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ITINERARY -->
<div class="itin-wrap" id="itinerary">
  <div class="section-inner">
    <div class="itin-header">
      <div>
        <div class="eyebrow reveal">The full journey</div>
        <div class="section-title reveal d1">15 Days.<br>5 Cities.</div>
      </div>
      <div class="price-tag reveal d2">
        <div class="from">All-inclusive from</div>
        <div class="amount">€1,895</div>
        <div style="font-size: 11px; color: var(--muted); margin-top: 6px;">Flights · Hotels · Transfers · Breakfast</div>
      </div>
    </div>
    <div class="itin-grid">
      <div class="itin-stop reveal d1">
        <div class="stop-icon">✈️</div>
        <div class="stop-day">Day 1 · Apr 14</div>
        <div class="stop-city">Bucharest</div>
        <div class="stop-desc">Departure 21:45 · Turkish Airlines · via Istanbul</div>
      </div>
      <div class="itin-stop reveal d2">
        <div class="stop-icon">🏛️</div>
        <div class="stop-day">Day 2–3 · Apr 15–16</div>
        <div class="stop-city">Hanoi</div>
        <div class="stop-desc">Old Quarter, Temple of Literature, Lan Ha Bay day trip by boat</div>
      </div>
      <div class="itin-stop reveal d3">
        <div class="stop-icon">⛰️</div>
        <div class="stop-day">Day 4–7 · Apr 17–20</div>
        <div class="stop-city">Ninh Binh</div>
        <div class="stop-desc">Trang An river, Mua Cave, Tam Coc, bicycle through rice fields</div>
      </div>
      <div class="itin-stop reveal d4">
        <div class="stop-icon">🏝️</div>
        <div class="stop-day">Day 8–11 · Apr 21–24</div>
        <div class="stop-city">Phu Quoc</div>
        <div class="stop-desc">Coral Bay Resort, boat tour, snorkeling, hiking, beach free day</div>
      </div>
      <div class="itin-stop reveal d5">
        <div class="stop-icon">🌆</div>
        <div class="stop-day">Day 12–15 · Apr 25–29</div>
        <div class="stop-city">HCMC</div>
        <div class="stop-desc">Ho Chi Minh City, Reunification Palace, markets · Departure Apr 28</div>
      </div>
    </div>
  </div>
</div>

<!-- GALLERY -->
<div class="gallery-wrap">
  <div class="gallery-row">
    <div class="gallery-photo tall">
      <img src="<?php echo $photos; ?>hero.jpg" alt="Trang An, Ninh Binh" loading="lazy">
    </div>
    <div style="display: flex; flex-direction: column; gap: 4px;">
      <div class="gallery-photo short">
        <img src="<?php echo $photos; ?>garden.jpg" alt="Ninh Binh Garden" loading="lazy">
      </div>
      <div class="gallery-photo short">
        <img src="<?php echo $photos; ?>halong.jpg" alt="Ha Long Bay" loading="lazy">
      </div>
    </div>
    <div style="display: flex; flex-direction: column; gap: 4px;">
      <div class="gallery-photo short">
        <img src="<?php echo $photos; ?>tamcoc.jpg" alt="Tam Coc" loading="lazy">
      </div>
      <div class="gallery-photo short">
        <img src="<?php echo $photos; ?>phuquoc.jpg" alt="Phu Quoc snorkeling" loading="lazy">
      </div>
    </div>
  </div>
</div>

<!-- WHY -->
<section class="why-wrap" id="why">
  <div class="section-inner">
    <div class="eyebrow reveal">Why Nonla</div>
    <div class="section-title reveal d1">The Difference<br>You'll Feel.</div>
    <div class="why-grid">
      <div class="why-item reveal d1">
        <div class="why-icon">🧭</div>
        <div class="why-title">Local Expertise</div>
        <p class="why-desc">2–4 coordinators speak Vietnamese, Romanian & English. No language barrier, no getting lost, no wasted days figuring things out.</p>
      </div>
      <div class="why-item reveal d2">
        <div class="why-icon">🏨</div>
        <div class="why-title">Vetted Stays</div>
        <p class="why-desc">Wecozy Hanoi, Tam Coc Friendly Homestay, Coral Bay Resort — every property personally tested, rated 8.5+ on Booking.com.</p>
      </div>
      <div class="why-item reveal d3">
        <div class="why-icon">✈️</div>
        <div class="why-title">All-Inclusive</div>
        <p class="why-desc">Flights, transfers, 13 nights accommodation, daily breakfast, and curated experiences — all in €1,895. You just show up.</p>
      </div>
      <div class="why-item reveal d1">
        <div class="why-icon">🌿</div>
        <div class="why-title">NGO Organized</div>
        <p class="why-desc">We're a certified non-governmental organization. Sustainable travel, community partnerships, and cultural respect are in our DNA.</p>
      </div>
      <div class="why-item reveal d2">
        <div class="why-icon">🍜</div>
        <div class="why-title">Authentic Vietnam</div>
        <p class="why-desc">Phở for €1.50, Vietnamese coffee workshops, market visits, and local restaurants. Real Vietnam, not the tourist version.</p>
      </div>
      <div class="why-item reveal d3">
        <div class="why-icon">🤝</div>
        <div class="why-title">Small Group</div>
        <p class="why-desc">Intimate group travel means real friendships. We travel together, eat together, explore together. Community that lasts beyond the trip.</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<div class="cta-wrap" id="contact">
  <div class="cta-overlay"></div>
  <div class="cta-inner">
    <div class="dates-badge reveal">📅 14 April – 29 April 2026</div>
    <h2 class="reveal d1">Vietnam<br>Is <span>Waiting.</span></h2>
    <p class="reveal d2">Spots are limited. Get in touch to secure your place on Vietnam Escape Vol. 5 or ask us anything — we respond fast.</p>
    <div class="cta-actions reveal d3">
      <a href="mailto:contact@nonlaescape.com?subject=Vietnam Escape Vol. 5" class="btn-primary">
        Reserve Your Spot
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M2.5 7.5h10M8.5 3.5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </a>
      <a href="mailto:contact@nonlaescape.com" class="btn-ghost" style="color: rgba(244,239,233,.65);">contact@nonlaescape.com</a>
    </div>
  </div>
</div>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-inner">
    <div class="footer-top">
      <div>
        <div class="footer-logo">NONLA<span>.</span></div>
        <p class="footer-tagline">Asociația Nonla Cultural Escape — Romanian NGO dedicated to cultural exchange and sustainable travel.<br><br>CIF: 49845083 · Câmpulung Muscel, Argeș County.</p>
      </div>
      <div>
        <div class="footer-head">Navigate</div>
        <ul class="footer-links">
          <li><a href="#destinations">Destinations</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#itinerary">Itinerary</a></li>
          <li><a href="#why">Why Us</a></li>
        </ul>
      </div>
      <div>
        <div class="footer-head">Contact</div>
        <ul class="footer-links">
          <li><a href="mailto:contact@nonlaescape.com">contact@nonlaescape.com</a></li>
          <li><a href="<?php echo esc_url(home_url('/legal')); ?>">Legal Notice</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <?php echo date('Y'); ?> Asociația Nonla Cultural Escape. All rights reserved.</span>
      <span>Vietnam Escape Vol. 5</span>
    </div>
  </div>
</footer>

<script>
// Nav scroll
const nav = document.getElementById('nav');
window.addEventListener('scroll', () => nav.classList.toggle('stuck', scrollY > 50), { passive: true });

// Scroll reveal
const obs = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('on'); obs.unobserve(e.target); } });
}, { threshold: 0.1, rootMargin: '0px 0px -32px 0px' });
document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
</script>

<?php wp_footer(); ?>
</body>
</html>
