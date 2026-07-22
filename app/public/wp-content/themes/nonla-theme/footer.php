<footer class="nl-footer">
  <div class="nl-footer-inner">
    <div class="nl-footer-top">
      <div>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="nl-footer-logo">NONLA<span>.</span></a>
        <p class="nl-footer-tagline">Asociația Nonla Cultural Escape — Romanian NGO dedicated to cultural exchange and sustainable travel.<br><br>CIF: 49845083 · Câmpulung Muscel, Argeș County.</p>
        <div class="nl-footer-social">
          <a href="https://www.instagram.com/nonlaescape/" class="nl-footer-social-link" target="_blank" rel="noopener noreferrer">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
            Instagram
          </a>
          <a href="https://www.facebook.com/nonlaescape/" class="nl-footer-social-link" target="_blank" rel="noopener noreferrer">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            Facebook
          </a>
        </div>
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
