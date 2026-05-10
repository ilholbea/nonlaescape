<?php
$photos = get_stylesheet_directory_uri() . '/assets/photos/';
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nonla Cultural Escape — Vietnam Escape &amp; Vietnam Undiscovered</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=Inter:wght@300;400;500;600&family=Bebas+Neue&display=swap" rel="stylesheet">
<?php wp_head(); ?>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --cream:   #FAF5EC;
  --parch:   #F2E8D4;
  --white:   #FFFDF7;
  --terra:   #B85520;
  --terra-l: #D4703A;
  --gold:    #B8841A;
  --gold-l:  #D4A030;
  --forest:  #2C4A1A;
  --text:    #1A0E06;
  --text2:   #4A3318;
  --muted:   #7A6040;
  --border:  #DDD0B0;
  --card:    #FFF8EE;
  --shadow:  rgba(26,14,6,.08);
  --head:    'Cormorant Garamond', serif;
  --body:    'Inter', sans-serif;
  --display: 'Bebas Neue', sans-serif;
}

html { scroll-behavior: smooth; }
body { background: var(--cream); color: var(--text); font-family: var(--body); overflow-x: hidden; }
a { color: inherit; text-decoration: none; }
img { display: block; max-width: 100%; }

/* NAV */
.nav {
  position: fixed; top: 0; left: 0; right: 0; z-index: 200;
  display: flex; align-items: center; justify-content: space-between;
  padding: 28px 52px;
  transition: background .35s, padding .35s;
}
.nav.stuck {
  background: rgba(250,245,236,.96); backdrop-filter: blur(12px);
  padding: 16px 52px; border-bottom: 1px solid var(--border);
}
.logo { font-family: var(--display); font-size: 24px; letter-spacing: .1em; color: var(--white); }
.logo span { color: var(--gold-l); }
.nav.stuck .logo { color: var(--text); }
.nav-links { display: flex; gap: 32px; list-style: none; }
.nav-links a { font-size: 12px; letter-spacing: .12em; text-transform: uppercase; color: rgba(255,253,247,.7); transition: color .2s; }
.nav.stuck .nav-links a { color: var(--muted); }
.nav-links a:hover { color: var(--white); }
.nav.stuck .nav-links a:hover { color: var(--text); }
.nav-right { display: flex; align-items: center; gap: 14px; }
.lang-toggle { display: flex; gap: 3px; }
.lang-btn {
  font-size: 11px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase;
  padding: 5px 10px; border-radius: 2px; cursor: pointer; border: none;
  background: transparent; color: rgba(255,253,247,.45); transition: all .2s;
}
.lang-btn.active { background: var(--gold-l); color: var(--text); }
.lang-btn:hover:not(.active) { color: rgba(255,253,247,.9); }
.nav.stuck .lang-btn { color: var(--muted); }
.nav.stuck .lang-btn.active { background: var(--terra); color: #fff; }
.nav.stuck .lang-btn:hover:not(.active) { color: var(--text); }
.nav-cta {
  background: var(--terra); color: #fff; font-size: 11px; font-weight: 600;
  letter-spacing: .12em; text-transform: uppercase; padding: 10px 22px; border-radius: 3px;
  transition: background .2s, transform .2s;
}
.nav-cta:hover { background: var(--terra-l); transform: translateY(-1px); }

/* HERO */
.hero { position: relative; min-height: 100vh; display: flex; align-items: flex-end; overflow: hidden; }
.hero-photo {
  position: absolute; inset: 0;
  background: url('<?php echo $photos; ?>hero.jpg') center 40% / cover no-repeat;
}
.hero-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to bottom, rgba(20,10,4,.48) 0%, rgba(20,10,4,.15) 30%, rgba(20,10,4,.42) 65%, rgba(20,10,4,.90) 100%);
}
.hero-content {
  position: relative; z-index: 2;
  width: 100%; max-width: 1200px; margin: 0 auto;
  padding: 0 52px 90px;
}
.hero-eyebrow {
  display: inline-flex; align-items: center; gap: 10px;
  font-size: 11px; letter-spacing: .28em; text-transform: uppercase;
  color: var(--gold-l); font-weight: 500; margin-bottom: 22px;
}
.hero-eyebrow::before { content: ''; width: 28px; height: 1px; background: var(--gold-l); }
.hero h1 {
  font-family: var(--head); font-size: clamp(64px, 10vw, 140px);
  font-weight: 600; line-height: .92; color: var(--white); margin-bottom: 30px;
}
.hero h1 em { font-style: italic; color: var(--gold-l); }
.hero-row { display: flex; align-items: flex-end; justify-content: space-between; gap: 40px; flex-wrap: wrap; }
.hero-sub { font-size: 17px; color: rgba(255,253,247,.75); max-width: 440px; line-height: 1.75; font-weight: 300; }
.hero-actions { display: flex; gap: 14px; align-items: center; flex-wrap: wrap; margin-top: 28px; }
.btn-primary {
  display: inline-flex; align-items: center; gap: 10px;
  background: var(--terra); color: #fff; font-weight: 600;
  font-size: 12px; letter-spacing: .12em; text-transform: uppercase;
  padding: 14px 32px; border-radius: 3px;
  transition: background .2s, transform .2s, box-shadow .2s;
}
.btn-primary:hover { background: var(--terra-l); transform: translateY(-2px); box-shadow: 0 12px 30px rgba(184,85,32,.3); }
.btn-ghost-light {
  font-size: 12px; letter-spacing: .12em; text-transform: uppercase; font-weight: 500;
  color: rgba(255,253,247,.6); border-bottom: 1px solid rgba(255,253,247,.25);
  padding: 14px 4px; transition: color .2s, border-color .2s;
}
.btn-ghost-light:hover { color: var(--white); border-color: rgba(255,253,247,.65); }
.hero-stats { display: flex; gap: 36px; flex-shrink: 0; border-left: 1px solid rgba(255,253,247,.18); padding-left: 36px; }
.stat-n { font-family: var(--display); font-size: 44px; line-height: 1; color: var(--gold-l); }
.stat-l { font-size: 10px; text-transform: uppercase; letter-spacing: .18em; color: rgba(255,253,247,.45); margin-top: 4px; }

/* TICKER */
.ticker-wrap { background: var(--terra); overflow: hidden; padding: 13px 0; }
.ticker { display: flex; animation: tick 30s linear infinite; white-space: nowrap; }
.ticker-item {
  display: inline-flex; align-items: center; gap: 18px;
  padding: 0 24px; font-size: 11px; font-weight: 600;
  letter-spacing: .2em; text-transform: uppercase; color: rgba(255,255,255,.88);
}
.ticker-item .sep { width: 3px; height: 3px; background: rgba(255,255,255,.35); border-radius: 50%; }
@keyframes tick { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

/* SECTION COMMONS */
.section-inner { max-width: 1200px; margin: 0 auto; padding: 0 52px; }
.eyebrow {
  display: inline-flex; align-items: center; gap: 10px;
  font-size: 11px; letter-spacing: .24em; text-transform: uppercase;
  color: var(--terra); font-weight: 600; margin-bottom: 16px;
}
.eyebrow::before { content: ''; width: 20px; height: 1.5px; background: var(--terra); }
.eyebrow-center { display: flex; justify-content: center; }
.eyebrow-center::before { display: none; }
.section-title {
  font-family: var(--head); font-size: clamp(44px, 5.5vw, 76px);
  font-weight: 600; line-height: .96; margin-bottom: 48px; color: var(--text);
}
.section-title em { font-style: italic; color: var(--terra); }

/* ABOUT */
.about-wrap { padding: 100px 0; }
.about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
.about-photo-stack { position: relative; }
.about-photo-main { width: 100%; aspect-ratio: 4/5; object-fit: cover; border-radius: 4px; }
.about-photo-float {
  position: absolute; bottom: -28px; right: -28px;
  width: 52%; aspect-ratio: 1/1; object-fit: cover; border-radius: 4px;
  border: 4px solid var(--cream); box-shadow: 0 8px 32px var(--shadow);
}
.about-text { padding-left: 16px; }
.about-text p { font-size: 16px; color: var(--text2); line-height: 1.85; font-weight: 300; margin-bottom: 18px; }
.about-text p strong { color: var(--text); font-weight: 600; }
.about-quote {
  font-family: var(--head); font-style: italic; font-size: 22px;
  color: var(--text); line-height: 1.5;
  border-left: 2.5px solid var(--terra); padding-left: 22px; margin: 30px 0;
}

/* SERIES */
.series-wrap { padding: 0 0 100px; }
.series-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
.series-card {
  background: var(--card); border: 1px solid var(--border);
  border-radius: 6px; padding: 44px 40px;
  transition: box-shadow .25s, transform .25s;
}
.series-card:hover { box-shadow: 0 16px 48px var(--shadow); transform: translateY(-3px); }
.series-label {
  display: inline-flex; align-items: center;
  font-size: 10px; font-weight: 700; letter-spacing: .22em; text-transform: uppercase;
  color: var(--terra); background: rgba(184,85,32,.08);
  padding: 5px 12px; border-radius: 100px; margin-bottom: 20px;
}
.series-name { font-family: var(--head); font-size: 36px; font-weight: 600; line-height: 1.1; color: var(--text); margin-bottom: 14px; }
.series-desc { font-size: 14px; color: var(--text2); line-height: 1.75; font-weight: 300; }
.series-link {
  display: inline-flex; align-items: center; gap: 8px;
  margin-top: 24px; font-size: 12px; font-weight: 600; letter-spacing: .1em;
  text-transform: uppercase; color: var(--terra);
  border-bottom: 1px solid rgba(184,85,32,.3); padding-bottom: 2px;
  transition: border-color .2s, gap .2s;
}
.series-link:hover { border-color: var(--terra); gap: 12px; }

/* DESTINATIONS */
.destinations-wrap { background: var(--parch); }
.dest-header { padding: 80px 52px 52px; max-width: 1200px; margin: 0 auto; }
.dest-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 3px; }
.dest-card { position: relative; overflow: hidden; aspect-ratio: 3/4; }
.dest-card img { width: 100%; height: 100%; object-fit: cover; transition: transform .6s ease; display: block; }
.dest-card:hover img { transform: scale(1.05); }
.dest-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(20,10,4,.88) 0%, rgba(20,10,4,.2) 55%, transparent 100%);
  display: flex; flex-direction: column; justify-content: flex-end; padding: 28px 22px;
}
.dest-tag { font-size: 10px; font-weight: 600; letter-spacing: .18em; text-transform: uppercase; color: var(--gold-l); margin-bottom: 6px; }
.dest-name { font-family: var(--head); font-size: 34px; font-weight: 600; color: var(--white); margin-bottom: 5px; line-height: 1; }
.dest-desc { font-size: 12px; color: rgba(255,253,247,.6); line-height: 1.5; font-weight: 300; }

/* WHY */
.why-wrap { padding: 100px 0; }
.why-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 48px; }
.why-item {
  background: var(--card); border: 1px solid var(--border); border-radius: 6px;
  padding: 36px 30px; transition: box-shadow .2s, transform .2s;
}
.why-item:hover { box-shadow: 0 8px 32px var(--shadow); transform: translateY(-2px); }
.why-icon { font-size: 26px; margin-bottom: 16px; }
.why-title { font-family: var(--head); font-size: 22px; font-weight: 600; margin-bottom: 10px; color: var(--text); }
.why-desc { font-size: 13px; color: var(--text2); line-height: 1.75; font-weight: 300; }

/* GALLERY */
.gallery-wrap { padding: 4px 0; }
.gallery-row { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 4px; }
.gallery-photo { overflow: hidden; }
.gallery-photo img { width: 100%; height: 100%; object-fit: cover; transition: transform .6s; display: block; }
.gallery-photo:hover img { transform: scale(1.04); }
.gallery-photo.tall { aspect-ratio: 16/9; }
.gallery-photo.short { aspect-ratio: 4/3; }

/* CTA */
.cta-wrap {
  position: relative; overflow: hidden;
  background: url('<?php echo $photos; ?>ninhbinh.jpg') center 35% / cover no-repeat;
}
.cta-overlay { position: absolute; inset: 0; background: linear-gradient(rgba(20,10,4,.70), rgba(20,10,4,.82)); }
.cta-inner { position: relative; z-index: 2; text-align: center; max-width: 1200px; margin: 0 auto; padding: 120px 52px; }
.cta-inner .eyebrow { color: var(--gold-l); }
.cta-inner .eyebrow::before { background: var(--gold-l); }
.cta-inner h2 {
  font-family: var(--head); font-size: clamp(52px, 8vw, 110px);
  font-weight: 600; line-height: .92; color: var(--white); margin-bottom: 22px;
}
.cta-inner h2 em { font-style: italic; color: var(--gold-l); }
.cta-inner p { font-size: 17px; color: rgba(255,253,247,.7); max-width: 420px; margin: 0 auto 44px; font-weight: 300; line-height: 1.75; }
.cta-actions { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }

/* FOOTER */
.footer { background: var(--text); color: var(--parch); }
.footer-inner { max-width: 1200px; margin: 0 auto; padding: 60px 52px 36px; }
.footer-top { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 60px; margin-bottom: 44px; }
.footer-logo { font-family: var(--display); font-size: 22px; letter-spacing: .1em; margin-bottom: 14px; }
.footer-logo span { color: var(--gold-l); }
.footer-tagline { font-size: 13px; color: rgba(242,232,212,.5); line-height: 1.7; max-width: 260px; font-weight: 300; }
.footer-head { font-size: 10px; font-weight: 700; letter-spacing: .22em; text-transform: uppercase; color: rgba(242,232,212,.38); margin-bottom: 18px; }
.footer-links { list-style: none; display: flex; flex-direction: column; gap: 11px; }
.footer-links a { font-size: 13px; color: rgba(242,232,212,.52); transition: color .2s; }
.footer-links a:hover { color: var(--parch); }
.footer-bottom { border-top: 1px solid rgba(242,232,212,.1); padding-top: 24px; display: flex; justify-content: space-between; font-size: 12px; color: rgba(242,232,212,.32); }

/* REVEAL */
.reveal { opacity: 0; transform: translateY(24px); transition: opacity .6s ease, transform .6s ease; }
.reveal.on { opacity: 1; transform: none; }
.d1 { transition-delay: .1s; } .d2 { transition-delay: .2s; }
.d3 { transition-delay: .3s; } .d4 { transition-delay: .4s; }

/* RESPONSIVE */
@media (max-width: 960px) {
  .nav { padding: 20px 24px; }
  .nav.stuck { padding: 14px 24px; }
  .nav-links { display: none; }
  .hero-content { padding: 0 24px 64px; }
  .hero-stats { display: none; }
  .section-inner { padding: 0 24px; }
  .dest-header { padding: 60px 24px 40px; }
  .dest-grid { grid-template-columns: repeat(2, 1fr); }
  .about-grid { grid-template-columns: 1fr; }
  .about-photo-float { display: none; }
  .about-text { padding-left: 0; }
  .series-grid { grid-template-columns: 1fr; }
  .why-grid { grid-template-columns: 1fr; gap: 14px; }
  .gallery-row { grid-template-columns: 1fr; }
  .footer-top { grid-template-columns: 1fr; gap: 36px; }
  .footer-inner { padding: 48px 24px 28px; }
  .cta-inner { padding: 90px 24px; }
  .about-wrap { padding: 80px 0; }
  .why-wrap { padding: 80px 0; }
}
@media (max-width: 540px) {
  .dest-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<!-- NAV -->
<nav class="nav" id="nav">
  <div class="logo">NONLA<span>.</span></div>
  <ul class="nav-links">
    <li><a href="#about"><span class="t" data-en="About" data-ro="Despre">About</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/projects')); ?>"><span class="t" data-en="Projects" data-ro="Proiecte">Projects</span></a></li>
    <li><a href="#contact"><span class="t" data-en="Contact" data-ro="Contact">Contact</span></a></li>
  </ul>
  <div class="nav-right">
    <div class="lang-toggle">
      <button class="lang-btn active" data-lang="en" onclick="applyLang('en')">EN</button>
      <button class="lang-btn" data-lang="ro" onclick="applyLang('ro')">RO</button>
    </div>
    <a href="<?php echo esc_url(home_url('/projects')); ?>" class="nav-cta">
      <span class="t" data-en="Our Escapes" data-ro="Escapadele Noastre">Our Escapes</span>
    </a>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-photo"></div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <div class="hero-eyebrow">
      <span class="t" data-en="Romanian NGO · Cultural Travel" data-ro="ONG Românesc · Turism Cultural">Romanian NGO · Cultural Travel</span>
    </div>
    <h1>
      <span class="t" data-en="Cultural" data-ro="Escapade">Cultural</span><br>
      <em><span class="t" data-en="Escapes" data-ro="Culturale">Escapes</span></em><br>
      <span class="t" data-en="Through Vietnam" data-ro="Prin Vietnam">Through Vietnam</span>
    </h1>
    <div class="hero-row">
      <div>
        <p class="hero-sub t"
           data-en="Nonla organizes immersive small-group trips to Vietnam — authentic experiences designed around real connection with the country and its people."
           data-ro="Nonla organizează experiențe de grup în Vietnam — autentice, menite să creeze conexiuni reale cu oamenii și locurile pe care le vizitezi.">
          Nonla organizes immersive small-group trips to Vietnam — authentic experiences designed around real connection with the country and its people.
        </p>
        <div class="hero-actions">
          <a href="<?php echo esc_url(home_url('/projects')); ?>" class="btn-primary">
            <span class="t" data-en="See Our Projects" data-ro="Vezi Proiectele">See Our Projects</span>
            <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M2.5 7.5h10M8.5 3.5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </a>
          <a href="#about" class="btn-ghost-light">
            <span class="t" data-en="Learn About Us" data-ro="Despre Noi">Learn About Us</span>
          </a>
        </div>
      </div>
      <div class="hero-stats">
        <div><div class="stat-n">5+</div><div class="stat-l t" data-en="Years" data-ro="Ani">Years</div></div>
        <div><div class="stat-n">5</div><div class="stat-l t" data-en="Editions" data-ro="Ediții">Editions</div></div>
        <div><div class="stat-n">100+</div><div class="stat-l t" data-en="Travelers" data-ro="Participanți">Travelers</div></div>
      </div>
    </div>
  </div>
</section>

<!-- TICKER -->
<div class="ticker-wrap">
  <div class="ticker">
    <?php
    $items = ['Vietnam Escape', 'Vietnam Undiscovered', 'ONG Românesc', 'Grupuri Mici', 'Authentic Experiences', 'Expert Coordinators', 'All-Inclusive', 'Zboruri Incluse', 'Vietnam Escape', 'Vietnam Undiscovered', 'ONG Românesc', 'Grupuri Mici', 'Authentic Experiences', 'Expert Coordinators', 'All-Inclusive', 'Zboruri Incluse'];
    foreach ($items as $item): ?>
    <span class="ticker-item"><span class="sep"></span><?php echo esc_html($item); ?></span>
    <?php endforeach; ?>
  </div>
</div>

<!-- ABOUT -->
<section class="about-wrap" id="about">
  <div class="section-inner">
    <div class="about-grid">
      <div class="about-photo-stack reveal">
        <img class="about-photo-main" src="<?php echo $photos; ?>tamcoc.jpg" alt="Tam Coc, Ninh Binh">
        <img class="about-photo-float" src="<?php echo $photos; ?>boats.jpg" alt="Local boats">
      </div>
      <div class="about-text">
        <div class="eyebrow reveal">
          <span class="t" data-en="Who We Are" data-ro="Cine Suntem">Who We Are</span>
        </div>
        <h2 class="section-title reveal d1">
          <span class="t" data-en="More Than" data-ro="Mai Mult">More Than</span><br>
          <em><span class="t" data-en="a Trip." data-ro="Decât O Călătorie.">a Trip.</span></em>
        </h2>
        <p class="reveal d2 t"
           data-en="Nonla Cultural Escape is a Romanian NGO dedicated to authentic travel. We believe travel should create genuine connections — with places, with cultures, and with people."
           data-ro="Nonla Cultural Escape este un ONG românesc dedicat călătoriilor autentice. Credem că o călătorie trebuie să creeze conexiuni reale — cu locurile, cu culturile și cu oamenii.">
          Nonla Cultural Escape is a Romanian NGO dedicated to authentic travel. We believe travel should create genuine connections — with places, with cultures, and with people.
        </p>
        <p class="reveal d3 t"
           data-en="We organize small-group trips to Vietnam where everything is handled — flights, accommodation, transfers, and expert coordinators who speak Romanian, English, and Vietnamese."
           data-ro="Organizăm călătorii în grup mic în Vietnam unde totul este inclus — zboruri, cazare, transferuri și coordonatori experți care vorbesc română, engleză și vietnameză.">
          We organize small-group trips to Vietnam where everything is handled — flights, accommodation, transfers, and expert coordinators who speak Romanian, English, and Vietnamese.
        </p>
        <blockquote class="about-quote reveal d4">
          <span class="t"
                data-en="Not just a trip — a shift in how you see the world."
                data-ro="Nu doar o călătorie — o schimbare în modul în care privești lumea.">
            Not just a trip — a shift in how you see the world.
          </span>
        </blockquote>
      </div>
    </div>
  </div>
</section>

<!-- SERIES -->
<section class="series-wrap">
  <div class="section-inner">
    <div class="eyebrow reveal">
      <span class="t" data-en="Our Escapes" data-ro="Escapadele Noastre">Our Escapes</span>
    </div>
    <h2 class="section-title reveal d1">
      <span class="t" data-en="Two Series." data-ro="Două Serii.">Two Series.</span><br>
      <em><span class="t" data-en="One Vietnam." data-ro="Un Vietnam.">One Vietnam.</span></em>
    </h2>
    <div class="series-grid">
      <div class="series-card reveal d2">
        <div class="series-label">
          <span class="t" data-en="Annual Group Escape" data-ro="Escapadă Anuală de Grup">Annual Group Escape</span>
        </div>
        <div class="series-name">Vietnam Escape</div>
        <p class="series-desc t"
           data-en="Our flagship annual escape — 15 days through Vietnam's iconic destinations. From Hanoi's ancient streets to Phu Quoc's tropical waters, everything is included: flights, hotels, transfers, and daily guided experiences."
           data-ro="Escapada noastră anuală emblematică — 15 zile prin destinațiile iconice din Vietnam. De la străzile antice ale Hanoiului la apele tropicale din Phu Quoc, totul este inclus: zboruri, hoteluri, transferuri și experiențe ghidate zilnic.">
          Our flagship annual escape — 15 days through Vietnam's iconic destinations. From Hanoi's ancient streets to Phu Quoc's tropical waters, everything is included: flights, hotels, transfers, and daily guided experiences.
        </p>
        <a href="<?php echo esc_url(home_url('/projects')); ?>" class="series-link">
          <span class="t" data-en="See Editions" data-ro="Vezi Edițiile">See Editions</span>
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 7h10M7.5 2.5l4.5 4.5-4.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
      </div>
      <div class="series-card reveal d3">
        <div class="series-label">
          <span class="t" data-en="Off The Beaten Path" data-ro="Dincolo de Traseele Bătătorite">Off The Beaten Path</span>
        </div>
        <div class="series-name">Vietnam Undiscovered</div>
        <p class="series-desc t"
           data-en="For those who want to go deeper. Remote villages, hidden valleys, and lesser-known corners of Vietnam that most travelers never reach. More adventurous, more intimate."
           data-ro="Pentru cei care vor să meargă mai adânc. Sate îndepărtate, văi ascunse și colțuri mai puțin cunoscute din Vietnam pe care cei mai mulți nu le văd niciodată. Mai aventuros, mai intim.">
          For those who want to go deeper. Remote villages, hidden valleys, and lesser-known corners of Vietnam that most travelers never reach. More adventurous, more intimate.
        </p>
        <a href="<?php echo esc_url(home_url('/projects')); ?>" class="series-link">
          <span class="t" data-en="See Editions" data-ro="Vezi Edițiile">See Editions</span>
          <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 7h10M7.5 2.5l4.5 4.5-4.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- DESTINATIONS -->
<section class="destinations-wrap" id="destinations">
  <div class="dest-header">
    <div class="eyebrow reveal">
      <span class="t" data-en="Where We Go" data-ro="Unde Mergem">Where We Go</span>
    </div>
    <h2 class="section-title reveal d1" style="margin-bottom: 0;">
      <span class="t" data-en="Places That Stay" data-ro="Locuri Care Rămân">Places That Stay</span><br>
      <em><span class="t" data-en="With You." data-ro="Cu Tine.">With You.</span></em>
    </h2>
  </div>
  <div class="dest-grid">
    <div class="dest-card">
      <img src="<?php echo $photos; ?>hanoi.jpg" alt="Hanoi" loading="lazy">
      <div class="dest-overlay">
        <div class="dest-tag">Hanoi</div>
        <div class="dest-name"><span class="t" data-en="Old Quarter" data-ro="Cartierul Vechi">Old Quarter</span></div>
        <div class="dest-desc t" data-en="Ancient streets, Temple of Literature, street food & Vietnamese coffee" data-ro="Străzi antice, Temple of Literature, mâncare stradală și cafea vietnameză">Ancient streets, Temple of Literature, street food & Vietnamese coffee</div>
      </div>
    </div>
    <div class="dest-card">
      <img src="<?php echo $photos; ?>halong.jpg" alt="Ha Long Bay" loading="lazy">
      <div class="dest-overlay">
        <div class="dest-tag">Lan Ha Bay</div>
        <div class="dest-name">Ha Long</div>
        <div class="dest-desc t" data-en="UNESCO limestone karsts, emerald waters, boat trips & kayaking" data-ro="Carsti calcaroși UNESCO, ape smarald, excursii cu barca și kayaking">UNESCO limestone karsts, emerald waters, boat trips & kayaking</div>
      </div>
    </div>
    <div class="dest-card">
      <img src="<?php echo $photos; ?>ninhbinh.jpg" alt="Ninh Binh" loading="lazy">
      <div class="dest-overlay">
        <div class="dest-tag">Ninh Binh</div>
        <div class="dest-name">Trang An</div>
        <div class="dest-desc t" data-en="River boats, Mua Cave, bicycle rides through endless rice paddies" data-ro="Bărci pe râu, Mua Cave, pedalat printre câmpuri nesfârșite de orez">River boats, Mua Cave, bicycle rides through endless rice paddies</div>
      </div>
    </div>
    <div class="dest-card">
      <img src="<?php echo $photos; ?>phuquoc.jpg" alt="Phu Quoc" loading="lazy">
      <div class="dest-overlay">
        <div class="dest-tag">Phu Quoc</div>
        <div class="dest-name"><span class="t" data-en="Island Life" data-ro="Viață de Insulă">Island Life</span></div>
        <div class="dest-desc t" data-en="Tropical island, snorkeling, boat tours, hiking & pure beach days" data-ro="Insulă tropicală, snorkeling, tururi cu barca, drumeții și zile de plajă">Tropical island, snorkeling, boat tours, hiking & pure beach days</div>
      </div>
    </div>
  </div>
</section>

<!-- WHY NONLA -->
<section class="why-wrap" id="why">
  <div class="section-inner">
    <div class="eyebrow reveal">
      <span class="t" data-en="Why Nonla" data-ro="De Ce Nonla">Why Nonla</span>
    </div>
    <h2 class="section-title reveal d1">
      <span class="t" data-en="The Difference" data-ro="Diferența">The Difference</span><br>
      <em><span class="t" data-en="You'll Feel." data-ro="Pe Care O Vei Simți.">You'll Feel.</span></em>
    </h2>
    <div class="why-grid">
      <div class="why-item reveal d1">
        <div class="why-icon">🧭</div>
        <div class="why-title t" data-en="Local Expertise" data-ro="Expertiză Locală">Local Expertise</div>
        <p class="why-desc t" data-en="2–4 coordinators speak Vietnamese, Romanian & English. No language barrier, no wasted days." data-ro="2–4 coordonatori vorbesc vietnameză, română și engleză. Fără bariera lingvistică, fără zile pierdute.">2–4 coordinators speak Vietnamese, Romanian & English. No language barrier, no wasted days.</p>
      </div>
      <div class="why-item reveal d2">
        <div class="why-icon">🏨</div>
        <div class="why-title t" data-en="Vetted Stays" data-ro="Cazări Verificate">Vetted Stays</div>
        <p class="why-desc t" data-en="Every property personally tested and rated 8.5+ on Booking.com. Comfort you can count on." data-ro="Fiecare proprietate testată personal și evaluată 8.5+ pe Booking.com. Confort garantat.">Every property personally tested and rated 8.5+ on Booking.com. Comfort you can count on.</p>
      </div>
      <div class="why-item reveal d3">
        <div class="why-icon">✈️</div>
        <div class="why-title t" data-en="All-Inclusive" data-ro="All-Inclusive">All-Inclusive</div>
        <p class="why-desc t" data-en="Flights, transfers, accommodation, daily breakfast and curated experiences. You just show up." data-ro="Zboruri, transferuri, cazare, mic dejun zilnic și experiențe curate. Tu trebuie doar să apari.">Flights, transfers, accommodation, daily breakfast and curated experiences. You just show up.</p>
      </div>
      <div class="why-item reveal d1">
        <div class="why-icon">🌿</div>
        <div class="why-title t" data-en="NGO Organized" data-ro="Organizat de ONG">NGO Organized</div>
        <p class="why-desc t" data-en="We're a certified NGO. Sustainable travel, community partnerships, and cultural respect are in our DNA." data-ro="Suntem un ONG certificat. Turismul sustenabil, parteneriatele comunitare și respectul cultural sunt în ADN-ul nostru.">We're a certified NGO. Sustainable travel, community partnerships, and cultural respect are in our DNA.</p>
      </div>
      <div class="why-item reveal d2">
        <div class="why-icon">🍜</div>
        <div class="why-title t" data-en="Authentic Vietnam" data-ro="Vietnam Autentic">Authentic Vietnam</div>
        <p class="why-desc t" data-en="Phở for €1.50, coffee workshops, market visits, local restaurants. Real Vietnam, not the tourist version." data-ro="Phở de 1,50€, ateliere de cafea, vizite la piețe, restaurante locale. Vietnam autentic, nu varianta turistică.">Phở for €1.50, coffee workshops, market visits, local restaurants. Real Vietnam, not the tourist version.</p>
      </div>
      <div class="why-item reveal d3">
        <div class="why-icon">🤝</div>
        <div class="why-title t" data-en="Small Group" data-ro="Grup Mic">Small Group</div>
        <p class="why-desc t" data-en="Intimate group travel means real friendships. We travel, eat, and explore together. Community that lasts." data-ro="Călătoria în grup mic înseamnă prietenii reale. Călătorim, mâncăm și explorăm împreună. O comunitate care durează.">Intimate group travel means real friendships. We travel, eat, and explore together. Community that lasts.</p>
      </div>
    </div>
  </div>
</section>

<!-- GALLERY -->
<div class="gallery-wrap">
  <div class="gallery-row">
    <div class="gallery-photo tall">
      <img src="<?php echo $photos; ?>hero.jpg" alt="Trang An, Ninh Binh" loading="lazy">
    </div>
    <div style="display:flex;flex-direction:column;gap:4px;">
      <div class="gallery-photo short"><img src="<?php echo $photos; ?>garden.jpg" alt="Ninh Binh Garden" loading="lazy"></div>
      <div class="gallery-photo short"><img src="<?php echo $photos; ?>halong.jpg" alt="Ha Long Bay" loading="lazy"></div>
    </div>
    <div style="display:flex;flex-direction:column;gap:4px;">
      <div class="gallery-photo short"><img src="<?php echo $photos; ?>tamcoc.jpg" alt="Tam Coc" loading="lazy"></div>
      <div class="gallery-photo short"><img src="<?php echo $photos; ?>phuquoc.jpg" alt="Phu Quoc" loading="lazy"></div>
    </div>
  </div>
</div>

<!-- CTA -->
<div class="cta-wrap" id="contact">
  <div class="cta-overlay"></div>
  <div class="cta-inner">
    <div class="eyebrow eyebrow-center reveal">
      <span class="t" data-en="Ready to Escape?" data-ro="Gata să Evadezi?">Ready to Escape?</span>
    </div>
    <h2 class="reveal d1">
      <span class="t" data-en="Find Your" data-ro="Găsește-ți">Find Your</span><br>
      <em><span class="t" data-en="Escape." data-ro="Escapada.">Escape.</span></em>
    </h2>
    <p class="reveal d2 t"
       data-en="Browse our Vietnam escapes — past, present, and upcoming — and find the one that calls to you."
       data-ro="Explorează escapadele noastre din Vietnam — trecute, prezente și viitoare — și găsește-o pe cea care te cheamă.">
      Browse our Vietnam escapes — past, present, and upcoming — and find the one that calls to you.
    </p>
    <div class="cta-actions reveal d3">
      <a href="<?php echo esc_url(home_url('/projects')); ?>" class="btn-primary">
        <span class="t" data-en="See All Projects" data-ro="Vezi Toate Proiectele">See All Projects</span>
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M2.5 7.5h10M8.5 3.5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </a>
      <a href="mailto:contact@nonlaescape.com" class="btn-ghost-light">contact@nonlaescape.com</a>
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
        <div class="footer-head t" data-en="Navigate" data-ro="Navigare">Navigate</div>
        <ul class="footer-links">
          <li><a href="#about"><span class="t" data-en="About" data-ro="Despre">About</span></a></li>
          <li><a href="<?php echo esc_url(home_url('/projects')); ?>"><span class="t" data-en="Projects" data-ro="Proiecte">Projects</span></a></li>
          <li><a href="<?php echo esc_url(home_url('/team')); ?>"><span class="t" data-en="Team" data-ro="Echipă">Team</span></a></li>
          <li><a href="#contact"><span class="t" data-en="Contact" data-ro="Contact">Contact</span></a></li>
          <li><a href="<?php echo esc_url(home_url('/legal')); ?>"><span class="t" data-en="Legal Notice" data-ro="Mențiuni Legale">Legal Notice</span></a></li>
        </ul>
      </div>
      <div>
        <div class="footer-head">Contact</div>
        <ul class="footer-links">
          <li><a href="mailto:contact@nonlaescape.com">contact@nonlaescape.com</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <?php echo date('Y'); ?> Asociația Nonla Cultural Escape. <span class="t" data-en="All rights reserved." data-ro="Toate drepturile rezervate.">All rights reserved.</span></span>
      <span class="t" data-en="Cultural Escapes Through Vietnam" data-ro="Escapade Culturale Prin Vietnam">Cultural Escapes Through Vietnam</span>
    </div>
  </div>
</footer>

<script>
const LANG_KEY = 'nl_lang';
let currentLang = localStorage.getItem(LANG_KEY) || 'en';

function applyLang(l) {
  currentLang = l;
  localStorage.setItem(LANG_KEY, l);
  document.querySelectorAll('.t[data-en]').forEach(el => {
    el.innerHTML = el.dataset[l] || el.dataset.en;
  });
  document.querySelectorAll('.lang-btn').forEach(b => b.classList.toggle('active', b.dataset.lang === l));
  document.documentElement.lang = l === 'ro' ? 'ro' : 'en';
}

const nav = document.getElementById('nav');
window.addEventListener('scroll', () => nav.classList.toggle('stuck', scrollY > 50), { passive: true });

const obs = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('on'); obs.unobserve(e.target); } });
}, { threshold: 0.1, rootMargin: '0px 0px -32px 0px' });
document.querySelectorAll('.reveal').forEach(el => obs.observe(el));

applyLang(currentLang);
</script>

<?php wp_footer(); ?>
</body>
</html>
