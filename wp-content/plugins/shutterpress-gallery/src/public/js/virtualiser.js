export const SP_PLACEHOLDER = 'data:image/gif;base64,R0lGODlhAQABAAAAACw=';

// --- Virtualiser (keep only a window of decoded images) ---
export function spInitVirtualiser($gallery, opts = {}) {
  const maxActive = opts.maxActive || 120;
  const enterMargin = opts.enterMargin || '1200px';
  const exitMargin = opts.exitMargin || '2000px';
  const active = new Set();
  let frozen = false;

  function observeList(list) {
    list.forEach((img) => {
      io.observe(img);
      ex.observe(img);
    });
  }

  const items = $gallery.find('.sp-gallery-item img').toArray();
  for (const img of items) {
    if (!img.dataset.src && (img.getAttribute('src') || img.currentSrc)) {
      img.dataset.src = img.getAttribute('src') || '';
      img.dataset.srcset = img.getAttribute('srcset') || '';
      img.dataset.sizes = img.getAttribute('sizes') || '';
    }
  }

  const io = new IntersectionObserver(onEnter, { rootMargin: enterMargin, threshold: 0.01 });
  const ex = new IntersectionObserver(onExit, { rootMargin: exitMargin, threshold: 0 });

  observeList(items);

  async function activate(img) {
    if (frozen || active.has(img)) return;
    if (img.dataset.srcset) img.setAttribute('srcset', img.dataset.srcset);
    if (img.dataset.sizes) img.setAttribute('sizes', img.dataset.sizes);
    if (img.dataset.src) img.setAttribute('src', img.dataset.src);
    img.loading = 'lazy';
    img.decoding = 'async';
    img.fetchpriority = 'low';
    try {
      await img.decode();
    } catch (_) {}
    active.add(img);

    if (active.size > maxActive) {
      let victim = null,
        dist = -1;
      for (const el of active) {
        const r = el.getBoundingClientRect();
        const d = Math.min(Math.abs(r.top), Math.abs(r.bottom));
        if (d > dist) {
          dist = d;
          victim = el;
        }
      }
      if (victim) deactivate(victim);
    }
  }

  function deactivate(img) {
    if (frozen || !active.has(img)) return;
    img.removeAttribute('srcset');
    img.removeAttribute('sizes');
    img.setAttribute('src', SP_PLACEHOLDER);
    active.delete(img);
  }

  function onEnter(entries) {
    for (const e of entries) if (e.isIntersecting) activate(e.target);
  }
  function onExit(entries) {
    for (const e of entries) if (!e.isIntersecting) deactivate(e.target);
  }

  return {
    freeze() {
      frozen = true;
    },
    unfreeze() {
      frozen = false;
    },
    add(imgNodes) {
      const list = Array.isArray(imgNodes) ? imgNodes : [imgNodes];
      for (const img of list) {
        if (!img.dataset.src && (img.getAttribute('src') || img.currentSrc)) {
          img.dataset.src = img.getAttribute('src') || '';
          img.dataset.srcset = img.getAttribute('srcset') || '';
          img.dataset.sizes = img.getAttribute('sizes') || '';
        }
      }
      observeList(list);
    },
    destroy() {
      io.disconnect();
      ex.disconnect();
      active.clear();
    },
  };
}
