<?php
/**
 * Nonla Cultural Escape — Homepage
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nonla Cultural Escape</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600&family=Playfair+Display:ital@0;1&display=swap" rel="stylesheet">
<?php wp_head(); ?>
<style>
/* ─── Reset & Base ─── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --black:   #080808;
  --dark:    #111111;
  --card:    #181818;
  --border:  #2a2a2a;
  --gold:    #e8a040;
  --gold-2:  #c97b28;
  --rust:    #c94c3e;
  --white:   #f5f0eb;
  --muted:   #888880;
  --font-head: 'Bebas Neue', sans-serif;
  --font-body: 'Inter', sans-serif;
  --font-serif: 'Playfair Display', serif;
}

html { scroll-behavior: smooth; }

body {
  background: var(--black);
  color: var(--white);
  font-family: var(--font-body);
  font-size: 16px;
  line-height: 1.6;
  overflow-x: hidden;
}

a { color: inherit; text-decoration: none; }
img { display: block; max-width: 100%; }

/* ─── Nav ─── */
.nl-nav {
  position: fixed;
  top: 0; left: 0; right: 0;
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 48px;
  transition: background .4s, padding .4s, backdrop-filter .4s;
}
.nl-nav.scrolled {
  background: rgba(8,8,8,.85);
  backdrop-filter: blur(12px);
  padding: 16px 48px;
  border-bottom: 1px solid var(--border);
}
.nl-logo {
  font-family: var(--font-head);
  font-size: 26px;
  letter-spacing: .08em;
  color: var(--white);
}
.nl-logo span { color: var(--gold); }
.nl-nav-links {
  display: flex;
  gap: 36px;
  list-style: none;
  font-size: 13px;
  letter-spacing: .12em;
  text-transform: uppercase;
  font-weight: 500;
}
.nl-nav-links a {
  color: var(--muted);
  transition: color .2s;
}
.nl-nav-links a:hover { color: var(--white); }
.nl-nav-cta {
  background: var(--gold);
  color: var(--black) !important;
  padding: 10px 24px;
  border-radius: 2px;
  font-weight: 600;
  font-size: 13px;
  letter-spacing: .1em;
  text-transform: uppercase;
  transition: background .2s, transform .2s;
}
.nl-nav-cta:hover { background: var(--gold-2); transform: translateY(-1px); }

/* ─── Hero ─── */
.nl-hero {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  overflow: hidden;
}
.nl-hero-bg {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 80% 60% at 70% 50%, rgba(232,160,64,.12) 0%, transparent 60%),
    radial-gradient(ellipse 60% 80% at 10% 80%, rgba(201,76,62,.10) 0%, transparent 55%),
    var(--black);
}
.nl-hero-grid {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
  background-size: 80px 80px;
  mask-image: radial-gradient(ellipse 90% 80% at 60% 50%, black, transparent);
}
.nl-hero-content {
  position: relative;
  z-index: 2;
  max-width: 1200px;
  margin: 0 auto;
  padding: 160px 48px 100px;
  width: 100%;
}
.nl-hero-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-size: 11px;
  letter-spacing: .25em;
  text-transform: uppercase;
  color: var(--gold);
  font-weight: 600;
  margin-bottom: 28px;
}
.nl-hero-eyebrow::before {
  content: '';
  display: block;
  width: 32px;
  height: 1px;
  background: var(--gold);
}
.nl-hero h1 {
  font-family: var(--font-head);
  font-size: clamp(72px, 10vw, 148px);
  line-height: .92;
  letter-spacing: .02em;
  text-transform: uppercase;
  margin-bottom: 32px;
  max-width: 900px;
}
.nl-hero h1 em {
  font-style: normal;
  -webkit-text-stroke: 1px var(--white);
  color: transparent;
}
.nl-hero-sub {
  font-size: 18px;
  color: var(--muted);
  max-width: 480px;
  line-height: 1.7;
  margin-bottom: 48px;
  font-weight: 300;
}
.nl-hero-actions {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  align-items: center;
}
.nl-btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: var(--gold);
  color: var(--black);
  font-weight: 700;
  font-size: 13px;
  letter-spacing: .1em;
  text-transform: uppercase;
  padding: 16px 36px;
  border-radius: 2px;
  transition: background .2s, transform .2s, box-shadow .2s;
}
.nl-btn-primary:hover {
  background: var(--gold-2);
  transform: translateY(-2px);
  box-shadow: 0 16px 40px rgba(232,160,64,.25);
}
.nl-btn-ghost {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  color: var(--white);
  font-size: 13px;
  letter-spacing: .1em;
  text-transform: uppercase;
  font-weight: 500;
  padding: 16px 20px;
  border-bottom: 1px solid var(--border);
  transition: color .2s, border-color .2s;
}
.nl-btn-ghost:hover { color: var(--gold); border-color: var(--gold); }

.nl-hero-stats {
  position: absolute;
  bottom: 60px;
  right: 48px;
  display: flex;
  gap: 48px;
}
.nl-stat-num {
  font-family: var(--font-head);
  font-size: 42px;
  color: var(--white);
  line-height: 1;
}
.nl-stat-label {
  font-size: 11px;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: .15em;
  margin-top: 4px;
}

/* ─── Marquee ─── */
.nl-marquee-wrap {
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
  padding: 18px 0;
  overflow: hidden;
  background: var(--dark);
}
.nl-marquee {
  display: flex;
  gap: 0;
  animation: marquee 30s linear infinite;
  white-space: nowrap;
}
.nl-marquee-item {
  display: inline-flex;
  align-items: center;
  gap: 24px;
  padding: 0 32px;
  font-size: 13px;
  font-weight: 600;
  letter-spacing: .2em;
  text-transform: uppercase;
  color: var(--muted);
}
.nl-marquee-item .dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: var(--gold);
  flex-shrink: 0;
}
@keyframes marquee {
  0%   { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

/* ─── Section Shared ─── */
.nl-section {
  max-width: 1200px;
  margin: 0 auto;
  padding: 120px 48px;
}
.nl-section-eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-size: 11px;
  letter-spacing: .25em;
  text-transform: uppercase;
  color: var(--gold);
  font-weight: 600;
  margin-bottom: 20px;
}
.nl-section-eyebrow::before {
  content: '';
  display: block;
  width: 24px;
  height: 1px;
  background: var(--gold);
}
.nl-section-title {
  font-family: var(--font-head);
  font-size: clamp(48px, 6vw, 88px);
  line-height: .95;
  letter-spacing: .02em;
  text-transform: uppercase;
  margin-bottom: 24px;
}

/* ─── About ─── */
.nl-about-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 80px;
  align-items: center;
}
.nl-about-text p {
  color: var(--muted);
  font-size: 17px;
  line-height: 1.8;
  margin-bottom: 20px;
  font-weight: 300;
}
.nl-about-text p strong { color: var(--white); font-weight: 500; }
.nl-about-visual {
  position: relative;
}
.nl-about-card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 4px;
  padding: 40px;
}
.nl-about-card-title {
  font-family: var(--font-head);
  font-size: 22px;
  letter-spacing: .08em;
  text-transform: uppercase;
  color: var(--gold);
  margin-bottom: 24px;
}
.nl-about-pill-group {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 32px;
}
.nl-pill {
  border: 1px solid var(--border);
  border-radius: 100px;
  padding: 6px 16px;
  font-size: 12px;
  letter-spacing: .08em;
  color: var(--muted);
}
.nl-about-quote {
  font-family: var(--font-serif);
  font-size: 20px;
  font-style: italic;
  color: var(--white);
  line-height: 1.5;
  border-left: 2px solid var(--gold);
  padding-left: 20px;
}
.nl-about-float {
  position: absolute;
  top: -24px;
  right: -24px;
  background: var(--rust);
  color: var(--white);
  padding: 16px 20px;
  border-radius: 4px;
  font-family: var(--font-head);
  font-size: 13px;
  letter-spacing: .1em;
  text-transform: uppercase;
  line-height: 1.4;
}

/* ─── Featured Escape ─── */
.nl-featured-wrap {
  background: var(--dark);
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
}
.nl-featured {
  max-width: 1200px;
  margin: 0 auto;
  padding: 120px 48px;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 80px;
  align-items: center;
}
.nl-featured-label {
  display: inline-block;
  background: var(--rust);
  color: var(--white);
  font-size: 11px;
  font-weight: 700;
  letter-spacing: .2em;
  text-transform: uppercase;
  padding: 6px 14px;
  border-radius: 2px;
  margin-bottom: 24px;
}
.nl-featured h2 {
  font-family: var(--font-head);
  font-size: clamp(52px, 6vw, 84px);
  line-height: .95;
  letter-spacing: .02em;
  text-transform: uppercase;
  margin-bottom: 12px;
}
.nl-featured-sub {
  font-family: var(--font-serif);
  font-style: italic;
  font-size: 20px;
  color: var(--gold);
  margin-bottom: 32px;
}
.nl-featured-desc {
  color: var(--muted);
  font-size: 16px;
  line-height: 1.8;
  font-weight: 300;
  margin-bottom: 40px;
}
.nl-featured-meta {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1px;
  background: var(--border);
  border: 1px solid var(--border);
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 40px;
}
.nl-meta-item {
  background: var(--card);
  padding: 20px 24px;
}
.nl-meta-label {
  font-size: 10px;
  letter-spacing: .2em;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 6px;
}
.nl-meta-value {
  font-size: 17px;
  font-weight: 600;
  color: var(--white);
}
.nl-meta-value.gold { color: var(--gold); }
.nl-featured-visual {
  position: relative;
}
.nl-featured-map {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 4px;
  padding: 40px;
  aspect-ratio: 4/3;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  position: relative;
  overflow: hidden;
}
.nl-map-bg {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 60% 40%, rgba(232,160,64,.15) 0%, transparent 50%),
    radial-gradient(circle at 20% 70%, rgba(201,76,62,.1) 0%, transparent 40%);
}
.nl-map-grid {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
  background-size: 40px 40px;
}
.nl-route {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  z-index: 2;
  text-align: center;
}
.nl-route-from, .nl-route-to {
  background: var(--dark);
  border: 1px solid var(--border);
  padding: 8px 16px;
  border-radius: 2px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: .1em;
  text-transform: uppercase;
}
.nl-route-from { border-color: var(--gold); color: var(--gold); }
.nl-route-to { border-color: var(--rust); color: var(--rust); }
.nl-route-arrow {
  width: 1px;
  height: 28px;
  background: linear-gradient(var(--gold), var(--rust));
  position: relative;
}
.nl-route-arrow::after {
  content: '↓';
  position: absolute;
  bottom: -20px;
  left: 50%;
  transform: translateX(-50%);
  color: var(--rust);
  font-size: 14px;
}
.nl-map-label {
  position: relative;
  z-index: 2;
  font-size: 42px;
  font-family: var(--font-head);
  letter-spacing: .05em;
  opacity: .06;
  text-transform: uppercase;
  position: absolute;
  bottom: 20px;
  right: 24px;
  font-size: 72px;
  line-height: 1;
}
.nl-featured-includes {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.nl-include-item {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 14px;
  color: var(--muted);
}
.nl-include-item::before {
  content: '✓';
  color: var(--gold);
  font-weight: 700;
  font-size: 13px;
  flex-shrink: 0;
}

/* ─── Why Nonla ─── */
.nl-why-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1px;
  background: var(--border);
  border: 1px solid var(--border);
  border-radius: 4px;
  overflow: hidden;
  margin-top: 64px;
}
.nl-why-item {
  background: var(--card);
  padding: 40px 36px;
  transition: background .2s;
}
.nl-why-item:hover { background: #1e1e1e; }
.nl-why-icon {
  font-size: 32px;
  margin-bottom: 20px;
  display: block;
}
.nl-why-title {
  font-family: var(--font-head);
  font-size: 22px;
  letter-spacing: .05em;
  text-transform: uppercase;
  margin-bottom: 12px;
  color: var(--white);
}
.nl-why-desc {
  font-size: 14px;
  color: var(--muted);
  line-height: 1.7;
  font-weight: 300;
}

/* ─── Destinations Ticker ─── */
.nl-destinations-wrap {
  background: var(--dark);
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
  padding: 80px 0;
}
.nl-destinations-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 48px;
}
.nl-destinations-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 48px;
}
.nl-dest-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}
.nl-dest-card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: 4px;
  padding: 32px 28px;
  position: relative;
  overflow: hidden;
  cursor: default;
  transition: border-color .25s, transform .25s;
}
.nl-dest-card:hover {
  border-color: var(--gold);
  transform: translateY(-4px);
}
.nl-dest-card::after {
  content: '';
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 2px;
  background: linear-gradient(90deg, var(--gold), var(--rust));
  transform: scaleX(0);
  transform-origin: left;
  transition: transform .3s;
}
.nl-dest-card:hover::after { transform: scaleX(1); }
.nl-dest-vol {
  font-size: 10px;
  letter-spacing: .2em;
  text-transform: uppercase;
  color: var(--gold);
  font-weight: 600;
  margin-bottom: 12px;
}
.nl-dest-name {
  font-family: var(--font-head);
  font-size: 26px;
  letter-spacing: .04em;
  text-transform: uppercase;
  color: var(--white);
  line-height: 1.1;
  margin-bottom: 8px;
}
.nl-dest-region {
  font-size: 13px;
  color: var(--muted);
  margin-bottom: 20px;
}
.nl-dest-status {
  display: inline-block;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: .15em;
  text-transform: uppercase;
  padding: 4px 12px;
  border-radius: 100px;
}
.nl-dest-status.upcoming {
  background: rgba(232,160,64,.12);
  border: 1px solid rgba(232,160,64,.3);
  color: var(--gold);
}
.nl-dest-status.completed {
  background: rgba(136,136,128,.1);
  border: 1px solid var(--border);
  color: var(--muted);
}

/* ─── CTA ─── */
.nl-cta-wrap {
  position: relative;
  overflow: hidden;
}
.nl-cta-bg {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 70% 90% at 50% 50%, rgba(232,160,64,.1) 0%, transparent 70%),
    var(--black);
}
.nl-cta {
  position: relative;
  z-index: 2;
  max-width: 1200px;
  margin: 0 auto;
  padding: 140px 48px;
  text-align: center;
}
.nl-cta h2 {
  font-family: var(--font-head);
  font-size: clamp(56px, 8vw, 120px);
  line-height: .92;
  letter-spacing: .02em;
  text-transform: uppercase;
  margin-bottom: 28px;
}
.nl-cta h2 span { color: var(--gold); }
.nl-cta p {
  font-size: 18px;
  color: var(--muted);
  max-width: 500px;
  margin: 0 auto 48px;
  font-weight: 300;
  line-height: 1.7;
}
.nl-cta-actions { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }

/* ─── Footer ─── */
.nl-footer {
  background: var(--dark);
  border-top: 1px solid var(--border);
}
.nl-footer-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 64px 48px 40px;
}
.nl-footer-top {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 64px;
  margin-bottom: 48px;
}
.nl-footer-logo {
  font-family: var(--font-head);
  font-size: 24px;
  letter-spacing: .08em;
  margin-bottom: 16px;
}
.nl-footer-logo span { color: var(--gold); }
.nl-footer-tagline {
  font-size: 14px;
  color: var(--muted);
  line-height: 1.7;
  font-weight: 300;
  max-width: 280px;
}
.nl-footer-heading {
  font-size: 11px;
  font-weight: 600;
  letter-spacing: .2em;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 20px;
}
.nl-footer-links {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.nl-footer-links a {
  font-size: 14px;
  color: var(--muted);
  transition: color .2s;
}
.nl-footer-links a:hover { color: var(--white); }
.nl-footer-bottom {
  border-top: 1px solid var(--border);
  padding-top: 28px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12px;
  color: var(--muted);
}
.nl-footer-legal { display: flex; gap: 24px; }

/* ─── Scroll Animations ─── */
.nl-reveal {
  opacity: 0;
  transform: translateY(32px);
  transition: opacity .7s ease, transform .7s ease;
}
.nl-reveal.visible {
  opacity: 1;
  transform: translateY(0);
}
.nl-reveal-delay-1 { transition-delay: .1s; }
.nl-reveal-delay-2 { transition-delay: .2s; }
.nl-reveal-delay-3 { transition-delay: .3s; }
.nl-reveal-delay-4 { transition-delay: .4s; }
.nl-reveal-delay-5 { transition-delay: .5s; }

/* ─── Responsive ─── */
@media (max-width: 900px) {
  .nl-nav { padding: 20px 24px; }
  .nl-nav.scrolled { padding: 14px 24px; }
  .nl-nav-links { display: none; }
  .nl-hero-content { padding: 140px 24px 80px; }
  .nl-hero-stats { display: none; }
  .nl-section { padding: 80px 24px; }
  .nl-about-grid { grid-template-columns: 1fr; gap: 48px; }
  .nl-about-float { display: none; }
  .nl-featured { grid-template-columns: 1fr; gap: 48px; padding: 80px 24px; }
  .nl-why-grid { grid-template-columns: 1fr; }
  .nl-dest-grid { grid-template-columns: repeat(2, 1fr); }
  .nl-cta { padding: 100px 24px; }
  .nl-footer-top { grid-template-columns: 1fr; gap: 40px; }
  .nl-footer-inner { padding: 48px 24px 32px; }
  .nl-destinations-inner { padding: 0 24px; }
  .nl-featured-meta { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 540px) {
  .nl-dest-grid { grid-template-columns: 1fr; }
  .nl-featured-meta { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<!-- ─── Navigation ─── -->
<nav class="nl-nav" id="nl-nav">
  <div class="nl-logo">NONLA<span>.</span></div>
  <ul class="nl-nav-links">
    <li><a href="#about">About</a></li>
    <li><a href="#escapes">Escapes</a></li>
    <li><a href="#why">Why Us</a></li>
    <li><a href="#destinations">Past Trips</a></li>
  </ul>
  <a href="#contact" class="nl-nav-cta">Join an Escape</a>
</nav>

<!-- ─── Hero ─── -->
<section class="nl-hero">
  <div class="nl-hero-bg"></div>
  <div class="nl-hero-grid"></div>

  <div class="nl-hero-content">
    <div class="nl-hero-eyebrow">Asociația Nonla Cultural Escape</div>
    <h1>Escape<br>the <em>Ordinary</em></h1>
    <p class="nl-hero-sub">
      Curated cultural travel experiences for those who want to go further — not just in distance, but in understanding.
    </p>
    <div class="nl-hero-actions">
      <a href="#escapes" class="nl-btn-primary">
        See Current Escapes
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </a>
      <a href="#about" class="nl-btn-ghost">Learn our story</a>
    </div>
  </div>

  <div class="nl-hero-stats">
    <div>
      <div class="nl-stat-num">5+</div>
      <div class="nl-stat-label">Projects</div>
    </div>
    <div>
      <div class="nl-stat-num">3</div>
      <div class="nl-stat-label">Continents</div>
    </div>
    <div>
      <div class="nl-stat-num">100%</div>
      <div class="nl-stat-label">Cultural Immersion</div>
    </div>
  </div>
</section>

<!-- ─── Marquee ─── -->
<div class="nl-marquee-wrap">
  <div class="nl-marquee">
    <?php $items = ['Vietnam', 'Cultural Exchange', 'Sustainability', 'Community', 'Adventure', 'Authentic Experiences', 'Guided Travel', 'NGO Organized', 'Vietnam', 'Cultural Exchange', 'Sustainability', 'Community', 'Adventure', 'Authentic Experiences', 'Guided Travel', 'NGO Organized']; foreach ($items as $item): ?>
    <span class="nl-marquee-item"><span class="dot"></span><?php echo $item; ?></span>
    <?php endforeach; ?>
  </div>
</div>

<!-- ─── About ─── -->
<section id="about">
  <div class="nl-section">
    <div class="nl-about-grid">
      <div class="nl-about-text">
        <div class="nl-section-eyebrow nl-reveal">Who we are</div>
        <div class="nl-section-title nl-reveal nl-reveal-delay-1">More Than<br>a Trip.</div>
        <p class="nl-reveal nl-reveal-delay-2">
          <strong>Nonla Cultural Escape</strong> is a Romanian non-governmental organization dedicated to <strong>cultural exchange and sustainable travel</strong>. We design experiences that go beyond sightseeing — immersing you in local culture, food, people, and perspective.
        </p>
        <p class="nl-reveal nl-reveal-delay-3">
          Each "escape" is a fully organized project: flights, accommodation, transport, coordinators — everything handled, so you can focus on experiencing. Our trips run in small groups with <strong>2–4 expert coordinators</strong> who speak the local language, Romanian and English.
        </p>
        <p class="nl-reveal nl-reveal-delay-4">
          We select only <strong>accommodations rated 8.5+ on Booking.com</strong>, tested by our team. Every detail is curated, every experience is intentional.
        </p>
      </div>
      <div class="nl-about-visual nl-reveal nl-reveal-delay-2">
        <div class="nl-about-float">NGO<br>Certified</div>
        <div class="nl-about-card">
          <div class="nl-about-card-title">Our Escapes Include</div>
          <div class="nl-about-pill-group">
            <span class="nl-pill">International Flights</span>
            <span class="nl-pill">All Transfers</span>
            <span class="nl-pill">13 Nights Accommodation</span>
            <span class="nl-pill">Daily Breakfast</span>
            <span class="nl-pill">Expert Coordinators</span>
            <span class="nl-pill">Curated Experiences</span>
            <span class="nl-pill">Small Groups</span>
          </div>
          <div class="nl-about-quote">
            "Not just a trip — a shift in how you see the world."
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ─── Featured Escape ─── -->
<div class="nl-featured-wrap" id="escapes">
  <div class="nl-featured">
    <div>
      <span class="nl-featured-label nl-reveal">Now Open — Vol. 5</span>
      <h2 class="nl-reveal nl-reveal-delay-1">Vietnam<br>Escape</h2>
      <div class="nl-featured-sub nl-reveal nl-reveal-delay-2">Hanoi · Ha Long Bay · Ninh Binh · Phu Quoc · Ho Chi Minh City</div>
      <p class="nl-featured-desc nl-reveal nl-reveal-delay-2">
        15 days across Vietnam's most breathtaking landscapes — from the UNESCO bay of Ha Long to the limestone valleys of Ninh Binh, tropical island life in Phu Quoc, and the electric energy of Ho Chi Minh City.
      </p>
      <div class="nl-featured-meta nl-reveal nl-reveal-delay-3">
        <div class="nl-meta-item">
          <div class="nl-meta-label">Departure</div>
          <div class="nl-meta-value">14 April 2026</div>
        </div>
        <div class="nl-meta-item">
          <div class="nl-meta-label">Return</div>
          <div class="nl-meta-value">29 April 2026</div>
        </div>
        <div class="nl-meta-item">
          <div class="nl-meta-label">Duration</div>
          <div class="nl-meta-value">15 Days</div>
        </div>
        <div class="nl-meta-item">
          <div class="nl-meta-label">Price</div>
          <div class="nl-meta-value gold">€1,895</div>
        </div>
      </div>
      <div class="nl-featured-includes nl-reveal nl-reveal-delay-4">
        <div class="nl-include-item">International flights (Turkish Airlines, Bucharest ↔ Vietnam)</div>
        <div class="nl-include-item">13 nights in 8.5+ rated accommodations</div>
        <div class="nl-include-item">All airport & in-country transfers</div>
        <div class="nl-include-item">Daily breakfast + guided cultural experiences</div>
        <div class="nl-include-item">2–4 expert coordinators (RO/EN/VN)</div>
      </div>
      <div style="margin-top: 36px;" class="nl-reveal nl-reveal-delay-5">
        <a href="mailto:contact@nonlaescape.com?subject=Vietnam Escape Vol. 5" class="nl-btn-primary">
          Reserve Your Spot
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
      </div>
    </div>
    <div class="nl-featured-visual nl-reveal nl-reveal-delay-2">
      <div class="nl-featured-map">
        <div class="nl-map-bg"></div>
        <div class="nl-map-grid"></div>
        <div class="nl-route">
          <div class="nl-route-from">Bucharest</div>
          <div class="nl-route-arrow"></div>
          <div class="nl-route-to">Vietnam</div>
        </div>
        <div class="nl-map-label">VN</div>
      </div>
    </div>
  </div>
</div>

<!-- ─── Why Nonla ─── -->
<section id="why">
  <div class="nl-section">
    <div class="nl-section-eyebrow nl-reveal">Why choose us</div>
    <div class="nl-section-title nl-reveal nl-reveal-delay-1">The Nonla<br>Difference.</div>
    <div class="nl-why-grid">
      <div class="nl-why-item nl-reveal nl-reveal-delay-1">
        <span class="nl-why-icon">🧭</span>
        <div class="nl-why-title">Expert Coordination</div>
        <p class="nl-why-desc">Every escape comes with 2–4 multilingual coordinators who speak Romanian, English, and the local language. No language barrier, no getting lost.</p>
      </div>
      <div class="nl-why-item nl-reveal nl-reveal-delay-2">
        <span class="nl-why-icon">🏨</span>
        <div class="nl-why-title">Vetted Accommodations</div>
        <p class="nl-why-desc">Every hotel and homestay is personally tested by our team and rated 8.5 or higher on Booking.com. Comfort is non-negotiable.</p>
      </div>
      <div class="nl-why-item nl-reveal nl-reveal-delay-3">
        <span class="nl-why-icon">✈️</span>
        <div class="nl-why-title">All-Inclusive Pricing</div>
        <p class="nl-why-desc">Flights, transfers, accommodation, breakfast, and experiences — all in one transparent price. No hidden fees. You just show up.</p>
      </div>
      <div class="nl-why-item nl-reveal nl-reveal-delay-1">
        <span class="nl-why-icon">🌿</span>
        <div class="nl-why-title">Sustainable by Design</div>
        <p class="nl-why-desc">As an NGO, our mission goes beyond tourism. We partner with local communities and prioritize responsible, low-impact travel.</p>
      </div>
      <div class="nl-why-item nl-reveal nl-reveal-delay-2">
        <span class="nl-why-icon">🎯</span>
        <div class="nl-why-title">Cultural Depth</div>
        <p class="nl-why-desc">We go beyond tourist hotspots. Every itinerary includes authentic local experiences — workshops, local restaurants, off-the-beaten-path discoveries.</p>
      </div>
      <div class="nl-why-item nl-reveal nl-reveal-delay-3">
        <span class="nl-why-icon">🤝</span>
        <div class="nl-why-title">Community First</div>
        <p class="nl-why-desc">Travel with people who share your curiosity. Small groups, real connections, and a community that extends well beyond the trip itself.</p>
      </div>
    </div>
  </div>
</section>

<!-- ─── Destinations ─── -->
<div class="nl-destinations-wrap" id="destinations">
  <div class="nl-destinations-inner">
    <div class="nl-destinations-header">
      <div>
        <div class="nl-section-eyebrow nl-reveal">Our Projects</div>
        <div class="nl-section-title nl-reveal nl-reveal-delay-1">All Escapes.</div>
      </div>
    </div>
    <div class="nl-dest-grid">
      <div class="nl-dest-card nl-reveal nl-reveal-delay-1">
        <div class="nl-dest-vol">Vol. 5 · 2026</div>
        <div class="nl-dest-name">Vietnam</div>
        <div class="nl-dest-region">Southeast Asia</div>
        <span class="nl-dest-status upcoming">Upcoming</span>
      </div>
      <div class="nl-dest-card nl-reveal nl-reveal-delay-2">
        <div class="nl-dest-vol">Vol. 4 · 2025</div>
        <div class="nl-dest-name">Japan</div>
        <div class="nl-dest-region">East Asia</div>
        <span class="nl-dest-status completed">Completed</span>
      </div>
      <div class="nl-dest-card nl-reveal nl-reveal-delay-3">
        <div class="nl-dest-vol">Vol. 3 · 2025</div>
        <div class="nl-dest-name">Morocco</div>
        <div class="nl-dest-region">North Africa</div>
        <span class="nl-dest-status completed">Completed</span>
      </div>
      <div class="nl-dest-card nl-reveal nl-reveal-delay-4">
        <div class="nl-dest-vol">Vol. 2 · 2024</div>
        <div class="nl-dest-name">Georgia</div>
        <div class="nl-dest-region">Caucasus</div>
        <span class="nl-dest-status completed">Completed</span>
      </div>
    </div>
    <p style="margin-top: 24px; font-size: 13px; color: var(--muted);">* Past destinations shown as examples — to be updated with actual project data.</p>
  </div>
</div>

<!-- ─── CTA ─── -->
<div class="nl-cta-wrap" id="contact">
  <div class="nl-cta-bg"></div>
  <div class="nl-cta">
    <div class="nl-section-eyebrow nl-reveal" style="justify-content:center;">Ready to escape?</div>
    <h2 class="nl-reveal nl-reveal-delay-1">Your Next<br><span>Adventure</span><br>Awaits.</h2>
    <p class="nl-reveal nl-reveal-delay-2">
      Get in touch to learn about upcoming escapes, secure your spot, or simply ask us anything. We respond fast.
    </p>
    <div class="nl-cta-actions nl-reveal nl-reveal-delay-3">
      <a href="mailto:contact@nonlaescape.com" class="nl-btn-primary">
        Contact Us
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </a>
      <a href="mailto:contact@nonlaescape.com?subject=Vietnam Escape Vol. 5" class="nl-btn-ghost">Reserve Vietnam Vol. 5</a>
    </div>
  </div>
</div>

<!-- ─── Footer ─── -->
<footer class="nl-footer">
  <div class="nl-footer-inner">
    <div class="nl-footer-top">
      <div>
        <div class="nl-footer-logo">NONLA<span>.</span></div>
        <p class="nl-footer-tagline">
          A Romanian NGO dedicated to cultural exchange and sustainable travel. CIF: 49845083 · Câmpulung Muscel, Argeș County.
        </p>
      </div>
      <div>
        <div class="nl-footer-heading">Navigate</div>
        <ul class="nl-footer-links">
          <li><a href="#about">About</a></li>
          <li><a href="#escapes">Current Escape</a></li>
          <li><a href="#why">Why Nonla</a></li>
          <li><a href="#destinations">All Projects</a></li>
        </ul>
      </div>
      <div>
        <div class="nl-footer-heading">Contact</div>
        <ul class="nl-footer-links">
          <li><a href="mailto:contact@nonlaescape.com">contact@nonlaescape.com</a></li>
          <li><a href="<?php echo esc_url(home_url('/')); ?>legal">Legal</a></li>
        </ul>
      </div>
    </div>
    <div class="nl-footer-bottom">
      <span>© <?php echo date('Y'); ?> Asociația Nonla Cultural Escape. All rights reserved.</span>
      <div class="nl-footer-legal">
        <a href="<?php echo esc_url(home_url('/')); ?>legal">Legal Notice</a>
      </div>
    </div>
  </div>
</footer>

<script>
// ─── Nav scroll effect
const nav = document.getElementById('nl-nav');
window.addEventListener('scroll', () => {
  nav.classList.toggle('scrolled', window.scrollY > 40);
}, { passive: true });

// ─── Scroll reveal
const reveals = document.querySelectorAll('.nl-reveal');
const observer = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('visible');
      observer.unobserve(e.target);
    }
  });
}, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

reveals.forEach(el => observer.observe(el));

// ─── Hero entrance (no delay needed, trigger immediately)
document.querySelectorAll('.nl-hero .nl-reveal, .nl-hero-eyebrow, .nl-hero h1, .nl-hero-sub, .nl-hero-actions, .nl-hero-stats').forEach(el => {
  el.classList.add('visible');
});
</script>

<?php wp_footer(); ?>
</body>
</html>
