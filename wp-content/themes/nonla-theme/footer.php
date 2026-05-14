<footer class="nl-footer">
  <div class="nl-footer-inner">
    <div class="nl-footer-top">
      <div>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="nl-footer-logo">NONLA<span>.</span></a>
        <p class="nl-footer-tagline">Asociația Nonla Cultural Escape — Romanian NGO dedicated to cultural exchange and sustainable travel.<br><br>CIF: 49845083 · Câmpulung Muscel, Argeș County.</p>
      </div>
      <div>
        <div class="nl-footer-head t" data-en="Navigate" data-ro="Navigare">Navigate</div>
        <ul class="nl-footer-links">
          <li><a href="<?php echo esc_url(home_url('/#about')); ?>"><span class="t" data-en="About" data-ro="Despre">About</span></a></li>
          <li><a href="<?php echo esc_url(home_url('/projects')); ?>"><span class="t" data-en="Projects" data-ro="Proiecte">Projects</span></a></li>
          <li><a href="<?php echo esc_url(home_url('/team')); ?>"><span class="t" data-en="Team" data-ro="Echipă">Team</span></a></li>
          <li><a href="<?php echo esc_url(home_url('/gallery')); ?>"><span class="t" data-en="Gallery" data-ro="Galerie">Gallery</span></a></li>
          <li><a href="<?php echo esc_url(home_url('/#contact')); ?>"><span class="t" data-en="Contact" data-ro="Contact">Contact</span></a></li>
          <li><a href="<?php echo esc_url(home_url('/legal')); ?>"><span class="t" data-en="Legal Notice" data-ro="Mențiuni Legale">Legal Notice</span></a></li>
        </ul>
      </div>
      <div>
        <div class="nl-footer-head">Contact</div>
        <ul class="nl-footer-links">
          <li><a href="mailto:<?php echo esc_attr(get_option('nonla_contact_email', 'nonlaescape@gmail.com')); ?>"><?php echo esc_html(get_option('nonla_contact_email', 'nonlaescape@gmail.com')); ?></a></li>
        </ul>
      </div>
    </div>
    <div class="nl-footer-bottom">
      <span>© <?php echo date('Y'); ?> Asociația Nonla Cultural Escape. <span class="t" data-en="All rights reserved." data-ro="Toate drepturile rezervate.">All rights reserved.</span></span>
      <span class="t" data-en="Cultural Escapes Through Vietnam" data-ro="Călătorii Culturale Prin Vietnam">Cultural Escapes Through Vietnam</span>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
