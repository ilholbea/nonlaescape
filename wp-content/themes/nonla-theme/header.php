<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<style>
.nl-nav {
  position: fixed; top: 0; left: 0; right: 0; z-index: 9999;
  display: flex; align-items: center; justify-content: space-between;
  padding: 28px 52px;
  transition: background .35s, padding .35s;
}
.nl-nav.stuck {
  background: rgba(250,245,236,.96);
  backdrop-filter: blur(12px);
  padding: 16px 52px;
  border-bottom: 1px solid #DDD0B0;
}
.nl-nav-logo {
  font-family: 'Bebas Neue', sans-serif;
  font-size: 24px; letter-spacing: .1em;
  color: #FFFDF7; text-decoration: none;
}
.nl-nav-logo span { color: #D4A030; }
.nl-nav.stuck .nl-nav-logo { color: #1A0E06; }

.nl-nav-links { display: flex; gap: 32px; list-style: none; margin: 0; padding: 0; }
.nl-nav-links a {
  font-size: 12px; letter-spacing: .12em; text-transform: uppercase;
  color: rgba(255,253,247,.7); text-decoration: none; transition: color .2s;
}
.nl-nav.stuck .nl-nav-links a { color: #7A6040; }
.nl-nav-links a:hover { color: #FFFDF7; }
.nl-nav.stuck .nl-nav-links a:hover { color: #1A0E06; }

.nl-nav-right { display: flex; align-items: center; gap: 14px; }

.nl-lang { display: flex; gap: 3px; }
.nl-lang-btn {
  font-size: 11px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase;
  padding: 5px 10px; border-radius: 2px; cursor: pointer; border: none;
  background: transparent; color: rgba(255,253,247,.45); transition: all .2s;
}
.nl-lang-btn.active { background: #D4A030; color: #1A0E06; }
.nl-nav.stuck .nl-lang-btn { color: #7A6040; }
.nl-nav.stuck .nl-lang-btn.active { background: #B85520; color: #fff; }

.nl-nav-cta {
  background: #B85520; color: #fff; font-size: 11px; font-weight: 600;
  letter-spacing: .12em; text-transform: uppercase; padding: 10px 22px;
  border-radius: 3px; text-decoration: none; transition: background .2s;
}
.nl-nav-cta:hover { background: #D4703A; color: #fff; }

@media (max-width: 960px) {
  .nl-nav { padding: 20px 24px; }
  .nl-nav.stuck { padding: 14px 24px; }
  .nl-nav-links { display: none; }
}
</style>

<nav class="nl-nav <?php echo is_front_page() ? '' : 'stuck'; ?>" id="nl-nav">
  <a href="<?php echo esc_url(home_url('/')); ?>" class="nl-nav-logo">NONLA<span>.</span></a>
  <ul class="nl-nav-links">
    <li><a href="<?php echo esc_url(home_url('/#about')); ?>">About</a></li>
    <li><a href="<?php echo esc_url(home_url('/projects')); ?>">Projects</a></li>
    <li><a href="<?php echo esc_url(home_url('/team')); ?>">Team</a></li>
    <li><a href="<?php echo esc_url(home_url('/legal')); ?>">Legal</a></li>
  </ul>
  <div class="nl-nav-right">
<a href="<?php echo esc_url(home_url('/projects')); ?>" class="nl-nav-cta">Our Escapes</a>
  </div>
</nav>

<script>
(function(){
  var nav = document.getElementById('nl-nav');
  var isHome = document.body.classList.contains('home');
  if (!isHome) nav.classList.add('stuck');
  window.addEventListener('scroll', function(){ if (isHome) nav.classList.toggle('stuck', window.scrollY > 50); }, {passive:true});

  var LANG_KEY = 'nl_lang';
  window.nlLang = function(l) {
    localStorage.setItem(LANG_KEY, l);
    document.documentElement.lang = l;
    document.body.classList.remove('nl-lang-en', 'nl-lang-ro');
    document.body.classList.add('nl-lang-' + l);
    document.querySelectorAll('.nl-lang-btn').forEach(function(b){
      b.classList.toggle('active', b.dataset.lang === l);
    });
  };
  var saved = localStorage.getItem(LANG_KEY) || 'en';
  nlLang(saved);
})();
</script>
