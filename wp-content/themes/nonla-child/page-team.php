<?php
$photos = get_stylesheet_directory_uri() . '/assets/photos/';

// Team members — edit here to add/update team members
// For member photos: add images to wp-content/themes/nonla-child/assets/team/
$team_dir = get_stylesheet_directory_uri() . '/assets/team/';

$team = [
  [
    'name'    => 'Alex Ivan',
    'city'    => 'Ho Chi Minh City',
    'role_en' => 'Founder · Cultural Ambassador',
    'role_ro' => 'Fondator · Ambasador Cultural',
    'photo'   => 'https://nonlaescape.com/wp-content/uploads/2024/12/alex-profile-1-e1733645445625.jpg',
  ],
  [
    'name'    => 'Trần Thị Thanh Thuỳ',
    'city'    => 'Ho Chi Minh City',
    'role_en' => 'Cultural Ambassador · Operations Coordinator',
    'role_ro' => 'Ambasador Cultural · Coordonator Operațional',
    'photo'   => 'https://nonlaescape.com/wp-content/uploads/2024/12/thuy-profile-1-e1733636971467.jpg',
  ],
  [
    'name'    => 'Hiếu Phạm',
    'city'    => 'Hanoi',
    'role_en' => 'Cultural Ambassador',
    'role_ro' => 'Ambasador Cultural',
    'photo'   => 'https://nonlaescape.com/wp-content/uploads/2024/12/WhatsApp-Image-2024-12-07-at-14.59.20-1.jpeg',
  ],
  [
    'name'    => 'Nguyễn Duy Uyên',
    'city'    => 'Hanoi',
    'role_en' => 'Cultural Ambassador',
    'role_ro' => 'Ambasador Cultural',
    'photo'   => 'https://nonlaescape.com/wp-content/uploads/2024/12/uyen-profile-1-e1733641741146.jpg',
  ],
  [
    'name'    => 'Nguyễn Ngọc Hà Phương',
    'city'    => 'Hanoi',
    'role_en' => 'Cultural Ambassador',
    'role_ro' => 'Ambasador Cultural',
    'photo'   => 'https://nonlaescape.com/wp-content/uploads/2024/12/haphuong-profile-1-e1733643856255.jpg',
  ],
  [
    'name'    => 'Nguyễn Đoàn Yến Nhi',
    'city'    => 'Ho Chi Minh City',
    'role_en' => 'Cultural Ambassador',
    'role_ro' => 'Ambasador Cultural',
    'photo'   => 'https://nonlaescape.com/wp-content/uploads/2024/12/nhi-profile-1-e1733669989583.jpg',
  ],
  [
    'name'    => 'Trịnh Thu Phương',
    'city'    => 'Ho Chi Minh City',
    'role_en' => 'Cultural Ambassador',
    'role_ro' => 'Ambasador Cultural',
    'photo'   => 'https://nonlaescape.com/wp-content/uploads/2024/12/WhatsApp-Image-2024-12-07-at-13.53.44-2-1-scaled-e1733636020764.jpeg',
  ],
  [
    'name'    => 'Aura Amironesei',
    'city'    => 'Iași',
    'role_en' => 'Legal Advisor',
    'role_ro' => 'Consilier Juridic',
    'photo'   => 'https://nonlaescape.com/wp-content/uploads/2024/12/aura-profile-1-1-2.jpg',
  ],
  [
    'name'    => 'Constantin Anghel',
    'city'    => 'Buzău',
    'role_en' => 'Accountant',
    'role_ro' => 'Contabil',
    'photo'   => 'https://nonlaescape.com/wp-content/uploads/2024/12/WhatsApp-Image-2024-12-07-at-21.47.31-1-1-e1733635842883.jpeg',
  ],
];
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php _e('Team', 'nonla'); ?> — Nonla Cultural Escape</title>
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
  --gold-l:  #D4A030;
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
body { background: var(--cream); color: var(--text); font-family: var(--body); }
a { color: inherit; text-decoration: none; }
img { display: block; max-width: 100%; }

/* NAV */
.nav {
  position: sticky; top: 0; z-index: 200;
  display: flex; align-items: center; justify-content: space-between;
  padding: 20px 52px;
  background: rgba(250,245,236,.96); backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--border);
}
.logo { font-family: var(--display); font-size: 24px; letter-spacing: .1em; color: var(--text); }
.logo span { color: var(--gold-l); }
.nav-links { display: flex; gap: 32px; list-style: none; }
.nav-links a { font-size: 12px; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); transition: color .2s; }
.nav-links a:hover, .nav-links a.current { color: var(--text); }
.nav-right { display: flex; align-items: center; gap: 14px; }
.lang-toggle { display: flex; gap: 3px; }
.lang-btn {
  font-size: 11px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase;
  padding: 5px 10px; border-radius: 2px; cursor: pointer; border: none;
  background: transparent; color: var(--muted); transition: all .2s;
}
.lang-btn.active { background: var(--terra); color: #fff; }
.lang-btn:hover:not(.active) { color: var(--text); }
.nav-cta {
  background: var(--terra); color: #fff; font-size: 11px; font-weight: 600;
  letter-spacing: .12em; text-transform: uppercase; padding: 10px 22px; border-radius: 3px; transition: background .2s;
}
.nav-cta:hover { background: var(--terra-l); }

/* PAGE HEADER */
.page-header { padding: 80px 52px 64px; background: var(--parch); border-bottom: 1px solid var(--border); }
.page-header-inner { max-width: 1200px; margin: 0 auto; }
.breadcrumb { font-size: 12px; color: var(--muted); margin-bottom: 20px; }
.breadcrumb a { color: var(--terra); }
.page-title { font-family: var(--head); font-size: clamp(52px, 7vw, 92px); font-weight: 600; line-height: .92; color: var(--text); margin-bottom: 18px; }
.page-title em { font-style: italic; color: var(--terra); }
.page-subtitle { font-size: 17px; color: var(--text2); line-height: 1.7; font-weight: 300; max-width: 560px; }

/* TEAM GRID */
.team-wrap { max-width: 1200px; margin: 0 auto; padding: 80px 52px 100px; }
.team-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }

/* TEAM CARD */
.team-card {
  background: var(--card); border: 1px solid var(--border); border-radius: 8px;
  overflow: hidden; transition: box-shadow .25s, transform .25s;
}
.team-card:hover { box-shadow: 0 16px 48px var(--shadow); transform: translateY(-3px); }
.team-photo { aspect-ratio: 3/4; overflow: hidden; background: var(--parch); }
.team-photo img { width: 100%; height: 100%; object-fit: cover; object-position: top; transition: transform .5s; }
.team-card:hover .team-photo img { transform: scale(1.04); }
.team-photo-placeholder {
  width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
  font-size: 56px; color: var(--border); background: var(--parch);
}
.team-body { padding: 28px 28px 32px; }
.team-name { font-family: var(--head); font-size: 26px; font-weight: 600; color: var(--text); margin-bottom: 4px; }
.team-role { font-size: 12px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--terra); margin-bottom: 16px; }
.team-bio { font-size: 14px; color: var(--text2); line-height: 1.75; font-weight: 300; }
.team-email { margin-top: 18px; }
.team-email a { font-size: 12px; color: var(--terra); letter-spacing: .04em; }
.team-email a:hover { text-decoration: underline; }

/* EMPTY STATE */
.team-empty {
  text-align: center; padding: 100px 24px;
  font-size: 15px; color: var(--muted); line-height: 1.7;
}
.team-empty p { max-width: 480px; margin: 0 auto 24px; }

/* BOTTOM CTA */
.team-cta { background: var(--parch); border-top: 1px solid var(--border); padding: 80px 52px; text-align: center; }
.team-cta-inner { max-width: 600px; margin: 0 auto; }
.eyebrow {
  display: inline-flex; align-items: center; gap: 10px; justify-content: center;
  font-size: 11px; letter-spacing: .24em; text-transform: uppercase;
  color: var(--terra); font-weight: 600; margin-bottom: 16px;
}
.team-cta h2 { font-family: var(--head); font-size: clamp(36px, 5vw, 56px); font-weight: 600; color: var(--text); margin-bottom: 16px; line-height: 1.05; }
.team-cta h2 em { font-style: italic; color: var(--terra); }
.team-cta p { font-size: 16px; color: var(--text2); line-height: 1.75; font-weight: 300; margin-bottom: 32px; }
.btn-primary {
  display: inline-flex; align-items: center; gap: 10px;
  background: var(--terra); color: #fff; font-weight: 600;
  font-size: 12px; letter-spacing: .12em; text-transform: uppercase;
  padding: 14px 32px; border-radius: 3px; transition: background .2s;
}
.btn-primary:hover { background: var(--terra-l); }

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
.reveal { opacity: 0; transform: translateY(20px); transition: opacity .55s ease, transform .55s ease; }
.reveal.on { opacity: 1; transform: none; }

@media (max-width: 960px) {
  .nav { padding: 16px 24px; }
  .nav-links { display: none; }
  .page-header { padding: 60px 24px 48px; }
  .team-wrap { padding: 60px 24px 80px; }
  .team-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; }
  .team-cta { padding: 60px 24px; }
  .footer-top { grid-template-columns: 1fr; gap: 36px; }
  .footer-inner { padding: 48px 24px 28px; }
}
@media (max-width: 540px) {
  .team-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<nav class="nav">
  <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">NONLA<span>.</span></a>
  <ul class="nav-links">
    <li><a href="<?php echo esc_url(home_url('/#about')); ?>"><span class="t" data-en="About" data-ro="Despre">About</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/projects')); ?>"><span class="t" data-en="Projects" data-ro="Proiecte">Projects</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/team')); ?>" class="current"><span class="t" data-en="Team" data-ro="Echipă">Team</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/#contact')); ?>"><span class="t" data-en="Contact" data-ro="Contact">Contact</span></a></li>
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

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="page-header-inner">
    <div class="breadcrumb">
      <a href="<?php echo esc_url(home_url('/')); ?>">Nonla</a> / <span class="t" data-en="Team" data-ro="Echipă">Team</span>
    </div>
    <h1 class="page-title reveal">
      <span class="t" data-en="The People" data-ro="Oamenii">The People</span><br>
      <em><span class="t" data-en="Behind the Escapes." data-ro="Din Spatele Escapadelor.">Behind the Escapes.</span></em>
    </h1>
    <p class="page-subtitle reveal t"
       data-en="Meet the coordinators who make it all happen — on the ground in Vietnam, every single day of the escape."
       data-ro="Cunoaște coordonatorii care fac totul posibil — pe teren în Vietnam, în fiecare zi a escapadei.">
      Meet the coordinators who make it all happen — on the ground in Vietnam, every single day of the escape.
    </p>
  </div>
</div>

<!-- TEAM -->
<div class="team-wrap">
  <?php if (!empty($team)): ?>
  <div class="team-grid">
    <?php foreach ($team as $i => $member): ?>
    <div class="team-card reveal" style="transition-delay: <?php echo ($i % 3) * 0.1; ?>s;">
      <div class="team-photo">
        <?php if (!empty($member['photo'])): ?>
        <img src="<?php echo esc_url($member['photo']); ?>" alt="<?php echo esc_attr($member['name']); ?>" loading="lazy">
        <?php else: ?>
        <div class="team-photo-placeholder">👤</div>
        <?php endif; ?>
      </div>
      <div class="team-body">
        <div class="team-name"><?php echo esc_html($member['name']); ?></div>
        <div class="team-role t"
             data-en="<?php echo esc_attr($member['role_en']); ?>"
             data-ro="<?php echo esc_attr($member['role_ro']); ?>">
          <?php echo esc_html($member['role_en']); ?>
        </div>
        <?php if (!empty($member['city'])): ?>
        <div style="font-size:11px;color:var(--muted);letter-spacing:.08em;margin-top:4px;">📍 <?php echo esc_html($member['city']); ?></div>
        <?php endif; ?>
        <?php if (!empty($member['bio_en'])): ?>
        <p class="team-bio t"
           data-en="<?php echo esc_attr($member['bio_en']); ?>"
           data-ro="<?php echo esc_attr($member['bio_ro'] ?? $member['bio_en']); ?>">
          <?php echo esc_html($member['bio_en']); ?>
        </p>
        <?php endif; ?>
        <?php if (!empty($member['email'])): ?>
        <div class="team-email"><a href="mailto:<?php echo esc_attr($member['email']); ?>"><?php echo esc_html($member['email']); ?></a></div>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <div class="team-empty">
    <p class="t"
       data-en="Team profiles are coming soon. In the meantime, get in touch directly — we'd love to hear from you."
       data-ro="Profilurile echipei vin în curând. Între timp, contactați-ne direct — ne-ar bucura să auzim de la voi.">
      Team profiles are coming soon. In the meantime, get in touch directly — we'd love to hear from you.
    </p>
    <a href="mailto:<?php echo esc_attr(get_option('nonla_contact_email', 'contact@nonlaescape.com')); ?>" class="btn-primary">
      contact@nonlaescape.com
    </a>
  </div>
  <?php endif; ?>
</div>

<!-- VALUES CTA -->
<div class="team-cta">
  <div class="team-cta-inner">
    <div class="eyebrow">
      <span class="t" data-en="Our Approach" data-ro="Abordarea Noastră">Our Approach</span>
    </div>
    <h2>
      <span class="t" data-en="We Travel" data-ro="Călătorim">We Travel</span><br>
      <em><span class="t" data-en="With You." data-ro="Cu Voi.">With You.</span></em>
    </h2>
    <p class="t"
       data-en="Our coordinators aren't just guides — they're travel companions who speak your language, know Vietnam deeply, and are with you from the first flight to the last goodbye."
       data-ro="Coordonatorii noștri nu sunt doar ghizi — sunt însoțitori de călătorie care vorbesc limba ta, cunosc Vietnam în profunzime și sunt alături de tine de la primul zbor până la ultimul rămas bun.">
      Our coordinators aren't just guides — they're travel companions who speak your language, know Vietnam deeply, and are with you from the first flight to the last goodbye.
    </p>
    <a href="<?php echo esc_url(home_url('/projects')); ?>" class="btn-primary">
      <span class="t" data-en="See Our Escapes" data-ro="Vezi Escapadele Noastre">See Our Escapes</span>
      <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M2.5 7.5h10M8.5 3.5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </a>
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
          <li><a href="<?php echo esc_url(home_url('/#about')); ?>"><span class="t" data-en="About" data-ro="Despre">About</span></a></li>
          <li><a href="<?php echo esc_url(home_url('/projects')); ?>"><span class="t" data-en="Projects" data-ro="Proiecte">Projects</span></a></li>
          <li><a href="<?php echo esc_url(home_url('/team')); ?>"><span class="t" data-en="Team" data-ro="Echipă">Team</span></a></li>
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
      <span>© <?php echo date('Y'); ?> Asociația Nonla Cultural Escape.</span>
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
  document.querySelectorAll('.t[data-en]').forEach(el => { el.innerHTML = el.dataset[l] || el.dataset.en; });
  document.querySelectorAll('.lang-btn').forEach(b => b.classList.toggle('active', b.dataset.lang === l));
  document.documentElement.lang = l === 'ro' ? 'ro' : 'en';
}
const obs = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('on'); obs.unobserve(e.target); } });
}, { threshold: 0.08 });
document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
applyLang(currentLang);
</script>

<?php wp_footer(); ?>
</body>
</html>
