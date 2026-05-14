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
    <li><a href="<?php echo esc_url(home_url('/home')); ?>"><span class="t" data-en="Home" data-ro="Acasa">Home</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/projects')); ?>"><span class="t" data-en="Projects" data-ro="Proiecte">Projects</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/team')); ?>"><span class="t" data-en="Team" data-ro="Echipă">Team</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/gallery')); ?>"><span class="t" data-en="Gallery" data-ro="Galerie">Gallery</span></a></li>
    <li><a href="<?php echo esc_url(home_url('/legal')); ?>"><span class="t" data-en="Legal" data-ro="Contact">Legal</span></a></li>
  </ul>
  <div class="nl-nav-right">
    <div class="nl-lang">
      <button class="nl-lang-btn" data-lang="en" onclick="applyLang('en')">EN</button>
      <button class="nl-lang-btn" data-lang="ro" onclick="applyLang('ro')">RO</button>
    </div>
    <a href="<?php echo esc_url(home_url('/projects')); ?>" class="nl-nav-cta">
      <span class="t" data-en="Our Escapes" data-ro="Călătoriile Noastre">Our Escapes</span>
    </a>
  </div>
</nav>

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

  var nav = document.getElementById('nl-nav');
  var isHome = document.body.classList.contains('home');
  if (!isHome) nav.classList.add('stuck');
  window.addEventListener('scroll', function () {
    if (isHome) nav.classList.toggle('stuck', window.scrollY > 50);
  }, { passive: true });

  applyLang(localStorage.getItem(LANG_KEY) || 'en');
})();
</script>
