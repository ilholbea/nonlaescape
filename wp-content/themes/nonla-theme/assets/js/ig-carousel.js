(function () {
  var initialized = false;
  var paused = false;

  function fixIgCarousel() {
    var wrap = document.querySelector('.nl-ig-wrap #sbi_images');
    if (!wrap) return;
    // no scroll-behavior here — it interferes with the rAF auto-scroll loop
    wrap.style.cssText = 'display:flex;flex-wrap:nowrap;overflow-x:auto;gap:16px;padding:0;';
    wrap.querySelectorAll('.sbi_item').forEach(function (item) {
      item.style.cssText = 'flex:0 0 360px;width:360px;height:360px;position:relative;';
    });
    if (!initialized && wrap.querySelectorAll('.sbi_item').length > 0) {
      initCarousel(wrap);
      initialized = true;
    }
  }

  function initCarousel(el) {
    var igWrap = document.querySelector('.nl-ig-wrap');
    var itemW = 376; // 360px item + 16px gap

    // arrow buttons — use scrollBy so only arrows get smooth scroll
    function makeBtn(dir) {
      var btn = document.createElement('button');
      btn.className = 'nl-ig-arrow nl-ig-arrow-' + dir;
      btn.setAttribute('aria-label', dir === 'prev' ? 'Previous' : 'Next');
      btn.innerHTML = dir === 'prev'
        ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>'
        : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>';
      btn.addEventListener('click', function () {
        paused = true;
        el.scrollBy({ left: dir === 'next' ? itemW * 3 : -itemW * 3, behavior: 'smooth' });
        setTimeout(function () { paused = false; }, 2500);
      });
      igWrap.appendChild(btn);
    }
    makeBtn('prev');
    makeBtn('next');

    // pause auto-scroll while mouse is over the section
    igWrap.addEventListener('mouseenter', function () { paused = true; });
    igWrap.addEventListener('mouseleave', function () { paused = false; });

    // auto-scroll — delay 1s so Smash Balloon finishes rendering before loop starts
    setTimeout(function () {
      var speed = 0.5;
      function tick() {
        if (!paused) {
          el.scrollLeft += speed;
          if (el.scrollLeft >= el.scrollWidth - el.clientWidth) el.scrollLeft = 0;
        }
        requestAnimationFrame(tick);
      }
      requestAnimationFrame(tick);
    }, 1000);
  }

  var igWrap = document.querySelector('.nl-ig-wrap');
  if (igWrap) {
    fixIgCarousel();
    new MutationObserver(fixIgCarousel).observe(igWrap, { childList: true, subtree: true });
  }
})();