(function () {
  var initialized = false;

  function fixIgCarousel() {
    var wrap = document.querySelector('.nl-ig-wrap #sbi_images');
    if (!wrap) return;
    wrap.style.cssText = 'display:flex;flex-wrap:nowrap;overflow-x:auto;gap:16px;padding:0;';
    wrap.querySelectorAll('.sbi_item').forEach(function (item) {
      item.style.cssText = 'flex:0 0 360px;width:360px;height:360px;position:relative;';
      var pw = item.querySelector('.sbi_photo_wrap');
      if (pw) {
        pw.style.cssText = 'position:relative;width:100%;height:100%;display:block;';
        if (!pw.querySelector('.nl-ig-logo')) {
          var logo = document.createElement('span');
          logo.className = 'nl-ig-logo';
          pw.appendChild(logo);
        }
      }
    });
    if (!initialized && wrap.querySelectorAll('.sbi_item').length > 0) {
      initCarousel(wrap);
      initialized = true;
    }
  }

  function initCarousel(el) {
    var igWrap = document.querySelector('.nl-ig-wrap');
    var itemW = 376; // 360px item + 16px gap

    function makeBtn(dir) {
      var btn = document.createElement('button');
      btn.className = 'nl-ig-arrow nl-ig-arrow-' + dir;
      btn.setAttribute('aria-label', dir === 'prev' ? 'Previous' : 'Next');
      btn.innerHTML = dir === 'prev'
        ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>'
        : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>';
      btn.addEventListener('click', function () {
        el.scrollBy({ left: dir === 'next' ? itemW * 3 : -itemW * 3, behavior: 'smooth' });
      });
      igWrap.appendChild(btn);
    }
    makeBtn('prev');
    makeBtn('next');
  }

  var igWrap = document.querySelector('.nl-ig-wrap');
  if (igWrap) {
    fixIgCarousel();
    new MutationObserver(fixIgCarousel).observe(igWrap, { childList: true, subtree: true });
  }
})();