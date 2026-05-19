<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<nav class="nl-nav <?php echo is_front_page() ? '' : 'stuck'; ?>" id="nl-nav">
  <a href="<?php echo esc_url(home_url('/')); ?>" class="nl-nav-logo">
    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/photos/nonla-logo.png'); ?>" alt="Nonla" class="nl-nav-logo-img">
  </a>
  <ul class="nl-nav-links">
    <li><a href="<?php echo esc_url(home_url('/home')); ?>"><span class="t" data-en="Home" data-ro="Acasă">Home</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/projects')); ?>"><span class="t" data-en="Projects" data-ro="Proiecte">Projects</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/team')); ?>"><span class="t" data-en="Team" data-ro="Echipă">Team</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/gallery')); ?>"><span class="t" data-en="Gallery" data-ro="Galerie">Gallery</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/legal')); ?>"><span class="t" data-en="Legal" data-ro="Contact">Legal</span></a></li>
  </ul>
  <div class="nl-nav-right">
    <div class="nl-nav-social">
      <a href="https://www.instagram.com/nonlaescape/" class="nl-nav-social-link" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
      </a>
      <a href="https://www.facebook.com/nonlaescape/" class="nl-nav-social-link" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
      </a>
    </div>
    <div class="nl-lang">
      <button class="nl-lang-btn" data-lang="en" onclick="applyLang('en')">EN</button>
      <button class="nl-lang-btn" data-lang="ro" onclick="applyLang('ro')">RO</button>
    </div>
    <a href="<?php echo esc_url(home_url('/projects')); ?>" class="nl-nav-cta">
      <span class="t" data-en="Our Escapes" data-ro="Călătoriile Noastre">Our Escapes</span>
    </a>
    <button class="nl-nav-burger" id="nl-nav-burger" aria-label="Open menu" aria-expanded="false">
      <svg width="22" height="16" viewBox="0 0 22 16" fill="none">
        <line x1="0" y1="1" x2="22" y2="1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="0" y1="8" x2="22" y2="8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="0" y1="15" x2="22" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </button>
  </div>
</nav>

<div class="nl-mobile-menu" id="nl-mobile-menu" aria-hidden="true">
  <div class="nl-mobile-head">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="nl-mobile-logo">NONLA<span>.</span></a>
    <button class="nl-mobile-close" id="nl-mobile-close" aria-label="Close menu">
      <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
        <line x1="2" y1="2" x2="18" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <line x1="18" y1="2" x2="2" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
    </button>
  </div>
  <ul class="nl-mobile-links">
    <li><a href="<?php echo esc_url(home_url('/home')); ?>"><span class="t" data-en="Home" data-ro="Acasă">Home</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/projects')); ?>"><span class="t" data-en="Projects" data-ro="Proiecte">Projects</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/team')); ?>"><span class="t" data-en="Team" data-ro="Echipă">Team</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/gallery')); ?>"><span class="t" data-en="Gallery" data-ro="Galerie">Gallery</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/legal')); ?>"><span class="t" data-en="Legal" data-ro="Juridic">Legal</span></a></li>
  </ul>
  <div class="nl-mobile-bottom">
    <div class="nl-mobile-social">
      <a href="https://www.instagram.com/nonlaescape/" class="nl-mobile-social-link" target="_blank" rel="noopener noreferrer">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
        Instagram
      </a>
      <a href="https://www.facebook.com/nonlaescape/" class="nl-mobile-social-link" target="_blank" rel="noopener noreferrer">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
        Facebook
      </a>
    </div>
    <div class="nl-lang">
      <button class="nl-lang-btn" data-lang="en" onclick="applyLang('en')">EN</button>
      <button class="nl-lang-btn" data-lang="ro" onclick="applyLang('ro')">RO</button>
    </div>
  </div>
</div>

<script>
(function () {
  var LANG_KEY = 'nl_lang';

  function applyLang(l) {
    localStorage.setItem(LANG_KEY, l);
    document.documentElement.lang = l === 'ro' ? 'ro' : 'en';
    document.querySelectorAll('.t[data-en]').forEach(function (el) {
      el.innerHTML = el.dataset[l] || el.dataset.en;
    });
    document.querySelectorAll('.nl-lang-btn').forEach(function (b) {
      b.classList.toggle('active', b.dataset.lang === l);
    });
  }

  window.applyLang = applyLang;

  var nav    = document.getElementById('nl-nav');
  var menu   = document.getElementById('nl-mobile-menu');
  var burger = document.getElementById('nl-nav-burger');
  var close  = document.getElementById('nl-mobile-close');
  var isHome = document.body.classList.contains('home');

  if (!isHome) nav.classList.add('stuck');
  window.addEventListener('scroll', function () {
    if (isHome) nav.classList.toggle('stuck', window.scrollY > 50);
  }, { passive: true });

  function openMenu() {
    menu.classList.add('open');
    menu.setAttribute('aria-hidden', 'false');
    burger.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  }
  function closeMenu() {
    menu.classList.remove('open');
    menu.setAttribute('aria-hidden', 'true');
    burger.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  }

  burger.addEventListener('click', openMenu);
  close.addEventListener('click', closeMenu);
  menu.querySelectorAll('.nl-mobile-links a').forEach(function (a) {
    a.addEventListener('click', closeMenu);
  });

  applyLang(localStorage.getItem(LANG_KEY) || 'en');
})();
</script>