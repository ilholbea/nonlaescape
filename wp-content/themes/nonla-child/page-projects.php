<?php
$photos    = get_stylesheet_directory_uri() . '/assets/photos/';
$reg_url   = get_option('nonla_registration_url', 'https://docs.google.com/forms/d/e/1FAIpQLScaiRzBL7j7vd7o2TVD7zEXSXCG7ObDLJvfEtXsygLuM_dCIQ/viewform');
$contact   = get_option('nonla_contact_email', 'contact@nonlaescape.com');

// Add new projects here — no code changes needed elsewhere
$projects = [
  [
    'series'       => 'Vietnam Escape',
    'series_slug'  => 'vietnam-escape',
    'edition'      => 'Vol. 6',
    'year'         => '2026',
    'status'       => 'upcoming',
    'dates_en'     => 'Oct 27 – Nov 11, 2026',
    'dates_ro'     => '27 Oct – 11 Nov 2026',
    'duration_en'  => '15 days',
    'duration_ro'  => '15 zile',
    'destinations' => ['Hanoi', 'Ha Long Bay', 'Ninh Binh', 'Phu Quoc', 'Ho Chi Minh City'],
    'price'        => 'TBA',
    'price_note_en'=> '15 spots available',
    'price_note_ro'=> '15 locuri disponibile',
    'cover'        => 'halong.jpg',
    'desc_en'      => 'From the peaceful mountain formations of the north to the lively beaches of the south — a two-week journey through Vietnam\'s most diverse landscapes and cultures. 15 spots available.',
    'desc_ro'      => 'De la formațiunile montane liniștite din nord până la plajele animate din sud — o călătorie de două săptămâni prin peisajele și culturile cele mai diverse ale Vietnamului. 15 locuri disponibile.',
    'contact'      => 'mailto:contact@nonlaescape.com?subject=Vietnam Escape Vol. 6',
  ],
  [
    'series'       => 'Vietnam Escape',
    'series_slug'  => 'vietnam-escape',
    'edition'      => 'Vol. 5',
    'year'         => '2026',
    'status'       => 'completed',
    'dates_en'     => 'Apr 14 – 29, 2026',
    'dates_ro'     => '14 Apr – 29 Apr 2026',
    'duration_en'  => '15 days',
    'duration_ro'  => '15 zile',
    'destinations' => ['Hanoi', 'Lan Ha Bay', 'Ninh Binh', 'Phu Quoc', 'Ho Chi Minh City'],
    'price'        => '€1,895',
    'price_note_en'=> 'all-inclusive · flights · hotels',
    'price_note_ro'=> 'all-inclusive · zboruri · hoteluri',
    'cover'        => 'hero.jpg',
    'desc_en'      => '15 days across Vietnam\'s most iconic destinations. Hanoi\'s ancient streets, Ha Long Bay\'s limestone karsts, Ninh Binh\'s river valleys, Phu Quoc\'s tropical island, and Ho Chi Minh City. Turkish Airlines, 8.5+ rated hotels, expert coordinators — all-inclusive.',
    'desc_ro'      => '15 zile prin cele mai iconice destinații din Vietnam. Străzile antice ale Hanoiului, stâncile calcaroase din Ha Long Bay, văile fluviale din Ninh Binh, insula tropicală Phu Quoc și Ho Chi Minh City. Turkish Airlines, hoteluri evaluate 8.5+, coordonatori experți — all-inclusive.',
    'contact'      => 'mailto:contact@nonlaescape.com?subject=Vietnam Escape Vol. 5',
  ],
];
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php _e('Projects', 'nonla'); ?> — Nonla Cultural Escape</title>
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
  letter-spacing: .12em; text-transform: uppercase; padding: 10px 22px; border-radius: 3px;
  transition: background .2s;
}
.nav-cta:hover { background: var(--terra-l); }

/* PAGE HEADER */
.page-header {
  padding: 140px 52px 80px;
  background: var(--parch);
  border-bottom: 1px solid var(--border);
  max-width: 100%;
}
.page-header-inner { max-width: 1200px; margin: 0 auto; }
.breadcrumb { font-size: 12px; color: var(--muted); letter-spacing: .08em; margin-bottom: 24px; }
.breadcrumb a { color: var(--terra); }
.breadcrumb a:hover { text-decoration: underline; }
.page-title {
  font-family: var(--head); font-size: clamp(52px, 7vw, 96px);
  font-weight: 600; line-height: .92; color: var(--text); margin-bottom: 20px;
}
.page-title em { font-style: italic; color: var(--terra); }
.page-subtitle { font-size: 17px; color: var(--text2); line-height: 1.7; font-weight: 300; max-width: 540px; }

/* FILTERS */
.filters-bar { padding: 40px 52px 0; max-width: 1200px; margin: 0 auto; }
.filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
.filter-btn {
  font-size: 12px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase;
  padding: 9px 20px; border-radius: 100px; cursor: pointer;
  border: 1.5px solid var(--border); background: transparent; color: var(--muted);
  transition: all .2s;
}
.filter-btn.active { background: var(--terra); border-color: var(--terra); color: #fff; }
.filter-btn:hover:not(.active) { border-color: var(--terra); color: var(--terra); }

/* PROJECTS LIST */
.projects-wrap { padding: 48px 52px 100px; max-width: 1200px; margin: 0 auto; }
.project-card {
  display: grid; grid-template-columns: 420px 1fr; gap: 0;
  background: var(--card); border: 1px solid var(--border); border-radius: 8px;
  overflow: hidden; margin-bottom: 32px;
  transition: box-shadow .25s, transform .25s;
}
.project-card:hover { box-shadow: 0 16px 56px var(--shadow); transform: translateY(-3px); }
.project-cover { position: relative; overflow: hidden; }
.project-cover img { width: 100%; height: 100%; object-fit: cover; transition: transform .6s; display: block; }
.project-card:hover .project-cover img { transform: scale(1.04); }
.project-status-badge {
  position: absolute; top: 20px; left: 20px;
  font-size: 10px; font-weight: 700; letter-spacing: .18em; text-transform: uppercase;
  padding: 5px 12px; border-radius: 100px;
}
.project-status-badge.completed { background: rgba(26,14,6,.6); color: rgba(255,253,247,.8); }
.project-status-badge.upcoming { background: var(--terra); color: #fff; }
.project-body { padding: 44px 48px; display: flex; flex-direction: column; }
.project-series {
  display: inline-flex; align-items: center;
  font-size: 10px; font-weight: 700; letter-spacing: .22em; text-transform: uppercase;
  color: var(--terra); background: rgba(184,85,32,.08);
  padding: 5px 12px; border-radius: 100px; margin-bottom: 16px; align-self: flex-start;
}
.project-name {
  font-family: var(--head); font-size: 42px; font-weight: 600;
  color: var(--text); line-height: 1; margin-bottom: 6px;
}
.project-edition { font-size: 13px; color: var(--muted); letter-spacing: .06em; margin-bottom: 20px; }
.project-desc { font-size: 15px; color: var(--text2); line-height: 1.75; font-weight: 300; margin-bottom: 28px; }
.project-meta { display: flex; gap: 32px; flex-wrap: wrap; margin-bottom: 28px; padding: 20px 0; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
.meta-item { }
.meta-label { font-size: 10px; text-transform: uppercase; letter-spacing: .18em; color: var(--muted); margin-bottom: 4px; }
.meta-value { font-family: var(--head); font-size: 18px; font-weight: 600; color: var(--text); }
.meta-value.price { color: var(--terra); }
.project-destinations { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 32px; }
.dest-pill {
  font-size: 11px; font-weight: 500; letter-spacing: .06em;
  color: var(--text2); background: var(--parch);
  padding: 5px 12px; border-radius: 100px; border: 1px solid var(--border);
}
.project-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: auto; }
.btn-primary {
  display: inline-flex; align-items: center; gap: 10px;
  background: var(--terra); color: #fff; font-weight: 600;
  font-size: 12px; letter-spacing: .12em; text-transform: uppercase;
  padding: 13px 28px; border-radius: 3px; transition: background .2s, transform .2s;
}
.btn-primary:hover { background: var(--terra-l); transform: translateY(-1px); }
.btn-outline {
  display: inline-flex; align-items: center; gap: 8px;
  font-size: 12px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase;
  color: var(--terra); border: 1.5px solid rgba(184,85,32,.35);
  padding: 12px 24px; border-radius: 3px; transition: all .2s;
}
.btn-outline:hover { border-color: var(--terra); background: rgba(184,85,32,.05); }

/* EMPTY STATE */
.empty-state {
  text-align: center; padding: 80px 24px;
  background: var(--card); border: 1.5px dashed var(--border);
  border-radius: 8px;
}
.empty-state p { font-size: 15px; color: var(--muted); line-height: 1.7; }

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

/* RESPONSIVE */
@media (max-width: 900px) {
  .nav { padding: 16px 24px; }
  .nav-links { display: none; }
  .page-header { padding: 110px 24px 60px; }
  .filters-bar { padding: 32px 24px 0; }
  .projects-wrap { padding: 32px 24px 80px; }
  .project-card { grid-template-columns: 1fr; }
  .project-cover { aspect-ratio: 16/9; }
  .project-body { padding: 32px 28px; }
  .footer-top { grid-template-columns: 1fr; gap: 36px; }
  .footer-inner { padding: 48px 24px 28px; }
}
</style>
</head>
<body>

<!-- NAV -->
<nav class="nav" id="nav">
  <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">NONLA<span>.</span></a>
  <ul class="nav-links">
    <li><a href="<?php echo esc_url(home_url('/#about')); ?>"><span class="t" data-en="About" data-ro="Despre">About</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/projects')); ?>" class="current"><span class="t" data-en="Projects" data-ro="Proiecte">Projects</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/#contact')); ?>"><span class="t" data-en="Contact" data-ro="Contact">Contact</span></a></li>
  </ul>
  <div class="nav-right">
    <div class="lang-toggle">
      <button class="lang-btn active" data-lang="en" onclick="applyLang('en')">EN</button>
      <button class="lang-btn" data-lang="ro" onclick="applyLang('ro')">RO</button>
    </div>
    <a href="mailto:contact@nonlaescape.com" class="nav-cta">
      <span class="t" data-en="Get In Touch" data-ro="Contactează-ne">Get In Touch</span>
    </a>
  </div>
</nav>

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="page-header-inner">
    <div class="breadcrumb">
      <a href="<?php echo esc_url(home_url('/')); ?>">Nonla</a> / <span class="t" data-en="Projects" data-ro="Proiecte">Projects</span>
    </div>
    <h1 class="page-title reveal">
      <span class="t" data-en="Our" data-ro="Escapadele">Our</span><br>
      <em><span class="t" data-en="Escapes." data-ro="Noastre.">Escapes.</span></em>
    </h1>
    <p class="page-subtitle reveal t"
       data-en="Every edition of Vietnam Escape and Vietnam Undiscovered — past, present, and upcoming. Find the one that calls to you."
       data-ro="Fiecare ediție a Vietnam Escape și Vietnam Undiscovered — trecute, prezente și viitoare. Găsește-o pe cea care te cheamă.">
      Every edition of Vietnam Escape and Vietnam Undiscovered — past, present, and upcoming. Find the one that calls to you.
    </p>
  </div>
</div>

<!-- FILTERS -->
<div class="filters-bar">
  <div class="filter-tabs">
    <button class="filter-btn active" data-filter="all" onclick="filterProjects(this, 'all')">
      <span class="t" data-en="All" data-ro="Toate">All</span>
    </button>
    <button class="filter-btn" data-filter="vietnam-escape" onclick="filterProjects(this, 'vietnam-escape')">Vietnam Escape</button>
    <button class="filter-btn" data-filter="vietnam-undiscovered" onclick="filterProjects(this, 'vietnam-undiscovered')">Vietnam Undiscovered</button>
    <button class="filter-btn" data-filter="upcoming" onclick="filterProjects(this, 'upcoming')">
      <span class="t" data-en="Upcoming" data-ro="Viitoare">Upcoming</span>
    </button>
    <button class="filter-btn" data-filter="completed" onclick="filterProjects(this, 'completed')">
      <span class="t" data-en="Completed" data-ro="Finalizate">Completed</span>
    </button>
  </div>
</div>

<!-- PROJECTS -->
<div class="projects-wrap">
  <?php foreach ($projects as $p): ?>
  <div class="project-card reveal" data-series="<?php echo esc_attr($p['series_slug']); ?>" data-status="<?php echo esc_attr($p['status']); ?>">
    <div class="project-cover">
      <img src="<?php echo $photos . esc_attr($p['cover']); ?>" alt="<?php echo esc_attr($p['series'] . ' ' . $p['edition']); ?>" loading="lazy">
      <div class="project-status-badge <?php echo esc_attr($p['status']); ?>">
        <span class="t"
              data-en="<?php echo $p['status'] === 'completed' ? 'Completed' : 'Upcoming'; ?>"
              data-ro="<?php echo $p['status'] === 'completed' ? 'Finalizat' : 'Viitor'; ?>">
          <?php echo $p['status'] === 'completed' ? 'Completed' : 'Upcoming'; ?>
        </span>
      </div>
    </div>
    <div class="project-body">
      <span class="project-series"><?php echo esc_html($p['series']); ?></span>
      <div class="project-name"><?php echo esc_html($p['series']); ?></div>
      <div class="project-edition"><?php echo esc_html($p['edition']); ?> · <?php echo esc_html($p['year']); ?></div>
      <p class="project-desc t"
         data-en="<?php echo esc_attr($p['desc_en']); ?>"
         data-ro="<?php echo esc_attr($p['desc_ro']); ?>">
        <?php echo esc_html($p['desc_en']); ?>
      </p>
      <div class="project-meta">
        <div class="meta-item">
          <div class="meta-label t" data-en="Dates" data-ro="Perioada">Dates</div>
          <div class="meta-value t"
               data-en="<?php echo esc_attr($p['dates_en']); ?>"
               data-ro="<?php echo esc_attr($p['dates_ro']); ?>">
            <?php echo esc_html($p['dates_en']); ?>
          </div>
        </div>
        <div class="meta-item">
          <div class="meta-label t" data-en="Duration" data-ro="Durată">Duration</div>
          <div class="meta-value t"
               data-en="<?php echo esc_attr($p['duration_en']); ?>"
               data-ro="<?php echo esc_attr($p['duration_ro']); ?>">
            <?php echo esc_html($p['duration_en']); ?>
          </div>
        </div>
        <div class="meta-item">
          <div class="meta-label t" data-en="Price" data-ro="Preț">Price</div>
          <div class="meta-value price"><?php echo esc_html($p['price']); ?></div>
          <div style="font-size:11px;color:var(--muted);margin-top:2px;" class="t"
               data-en="<?php echo esc_attr($p['price_note_en']); ?>"
               data-ro="<?php echo esc_attr($p['price_note_ro']); ?>">
            <?php echo esc_html($p['price_note_en']); ?>
          </div>
        </div>
      </div>
      <div class="project-destinations">
        <?php foreach ($p['destinations'] as $dest): ?>
        <span class="dest-pill"><?php echo esc_html($dest); ?></span>
        <?php endforeach; ?>
      </div>
      <div class="project-actions">
        <a href="<?php echo esc_url($reg_url); ?>" target="_blank" rel="noopener" class="btn-primary">
          <span class="t" data-en="Register Your Interest" data-ro="Înregistrează-ți Interesul">Register Your Interest</span>
          <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M2.5 7.5h10M8.5 3.5l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
        <a href="mailto:<?php echo esc_attr($contact); ?>?subject=<?php echo urlencode($p['series'] . ' ' . $p['edition']); ?>" class="btn-outline">
          <span class="t" data-en="Ask a Question" data-ro="Pune o Întrebare">Ask a Question</span>
        </a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>

  <?php if (empty($projects)): ?>
  <div class="empty-state">
    <p class="t"
       data-en="No projects found. New escapes are announced soon — check back or get in touch."
       data-ro="Niciun proiect găsit. Escapadele noi sunt anunțate în curând — revino sau contactează-ne.">
      No projects found. New escapes are announced soon — check back or get in touch.
    </p>
  </div>
  <?php endif; ?>
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
          <li><a href="<?php echo esc_url(home_url('/#contact')); ?>"><span class="t" data-en="Contact" data-ro="Contact">Contact</span></a></li>
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

function filterProjects(btn, filter) {
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.project-card').forEach(card => {
    const show = filter === 'all'
      || card.dataset.series === filter
      || card.dataset.status === filter;
    card.style.display = show ? '' : 'none';
  });
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
