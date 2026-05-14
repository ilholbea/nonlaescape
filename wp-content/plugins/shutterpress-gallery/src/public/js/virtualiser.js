export const SP_PLACEHOLDER = 'data:image/gif;base64,R0lGODlhAQABAAAAACw=';

// --- Virtualiser (keep only a window of decoded images) ---
export function spInitVirtualiser($gallery, opts = {}) {
  if (
    typeof window !== 'undefined' &&
    window.shutterpressData &&
    Object.prototype.hasOwnProperty.call(window.shutterpressData, 'virtualiser_enabled') &&
    !window.shutterpressData.virtualiser_enabled
  ) {
    return {
      freeze() {},
      unfreeze() {},
      add() {},
      addDeferred() {},
      destroy() {},
    };
  }

  const galleryId = $gallery.attr('id') || 'unknown-gallery';
  const maxActive = opts.maxActive || 120;
  const enterMargin = opts.enterMargin || '1200px';
  const exitMargin = opts.exitMargin || '2000px';
  const enterMarginPx = marginToPx(enterMargin);
  const exitMarginPx = marginToPx(exitMargin);
  const lowConflictMode = opts.lowConflictMode !== false;
  const active = new Set();
  const tracked = new Set();
  let frozen = false;

  function logTiming(stage, extra = {}) {
    if (
      typeof window === 'undefined' ||
      !window.shutterpressData ||
      !window.shutterpressData.debug ||
      typeof window.performance === 'undefined'
    ) {
      return;
    }
    const time = Math.round(window.performance.now());
    console.log(`[SP TIMING] ${galleryId} ${stage} @ ${time}ms`, extra);
  }

  function marginToPx(value) {
    const parsed = Number.parseInt(value, 10);
    return Number.isNaN(parsed) ? 0 : parsed;
  }

  function isWithinMargin(img, marginPx) {
    const rect = img.getBoundingClientRect();
    const viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
    return rect.bottom >= -marginPx && rect.top <= viewportHeight + marginPx;
  }

  function rememberSource(img) {
    if (!img.dataset.src && (img.getAttribute('src') || img.currentSrc)) {
      img.dataset.src = img.getAttribute('src') || '';
      img.dataset.srcset = img.getAttribute('srcset') || '';
      img.dataset.sizes = img.getAttribute('sizes') || '';
      img.dataset.spVirtualiserSource = '1';
    }
  }

  function hasRealSource(img) {
    const src = img.getAttribute('src');
    return Boolean(src && src !== SP_PLACEHOLDER);
  }

  function isWarm(img) {
    return img.dataset.spVirtualiserWarm === '1';
  }

  function hasSeenOnce(img) {
    return img.dataset.spVirtualiserSeen === '1';
  }

  function isManagedByExternalLazyLoader(img) {
    if (!lowConflictMode) {
      return false;
    }

    const classList = img.classList || { contains: () => false };
    if (
      classList.contains('lazyload') ||
      classList.contains('lazyloaded') ||
      classList.contains('rocket-lazyload') ||
      classList.contains('ewww_webp_lazy_load')
    ) {
      return true;
    }

    return Boolean(
      (img.hasAttribute('data-src') && img.dataset.spVirtualiserSource !== '1') ||
      img.hasAttribute('data-lazy-src') ||
      img.hasAttribute('data-original') ||
      img.hasAttribute('data-ll-status') ||
      img.hasAttribute('data-srcset-lazy')
    );
  }

  function observeList(list) {
    list.forEach((img) => {
      if (tracked.has(img)) {
        return;
      }
      tracked.add(img);
      items.push(img);
      io.observe(img);
      ex.observe(img);
    });
  }

  function syncImageState(list) {
    list.forEach((img) => {
      rememberSource(img);

      if (isManagedByExternalLazyLoader(img)) {
        active.add(img);
        return;
      }

      if (isWithinMargin(img, enterMarginPx)) {
        if (hasRealSource(img)) {
          active.add(img);
        } else {
          activate(img);
        }
        return;
      }

      if (hasRealSource(img) && !isWithinMargin(img, exitMarginPx)) {
        active.add(img);
        deactivate(img, true);
      }
    });
  }

  const items = [];
  const initialItems = $gallery.find('.sp-gallery-item img').toArray();
  for (const img of initialItems) rememberSource(img);

  const io = new IntersectionObserver(onEnter, { rootMargin: enterMargin, threshold: 0.01 });
  const ex = new IntersectionObserver(onExit, { rootMargin: exitMargin, threshold: 0 });

  observeList(initialItems);
  syncImageState(initialItems);

  async function activate(img) {
    if (frozen || active.has(img)) return;
    if (isManagedByExternalLazyLoader(img)) {
      active.add(img);
      return;
    }
    logTiming('virtualiser_activate_start', {
      imageId: img.closest('.sp-gallery-item')?.dataset.imageId || null,
      hadRealSrc: hasRealSource(img),
    });
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
    logTiming('virtualiser_activate_done', {
      imageId: img.closest('.sp-gallery-item')?.dataset.imageId || null,
      activeCount: active.size,
    });

    if (active.size > maxActive) {
      let victim = null,
        dist = -1;
      for (const el of active) {
        if (isWarm(el) || isWithinMargin(el, exitMarginPx)) {
          continue;
        }
        const r = el.getBoundingClientRect();
        const d = Math.min(Math.abs(r.top), Math.abs(r.bottom));
        if (d > dist) {
          dist = d;
          victim = el;
        }
      }
      if (victim) deactivate(victim, true);
    }
  }

  function deactivate(img, force = false) {
    if (frozen || !active.has(img)) return;
    if (isWarm(img)) return;
    if (!force && isWithinMargin(img, exitMarginPx)) return;
    if (isManagedByExternalLazyLoader(img)) {
      return;
    }
    logTiming('virtualiser_deactivate', {
      imageId: img.closest('.sp-gallery-item')?.dataset.imageId || null,
      activeCount: active.size,
    });
    img.removeAttribute('srcset');
    img.removeAttribute('sizes');
    img.setAttribute('src', SP_PLACEHOLDER);
    active.delete(img);
  }

  function onEnter(entries) {
    for (const e of entries) {
      if (!e.isIntersecting) continue;
      if (isWarm(e.target)) {
        e.target.dataset.spVirtualiserSeen = '1';
      }
      activate(e.target);
    }
  }
  function onExit(entries) {
    for (const e of entries) {
      if (e.isIntersecting) continue;
      if (isWarm(e.target)) {
        if (!hasSeenOnce(e.target)) {
          continue;
        }
        e.target.dataset.spVirtualiserWarm = '0';
      }
      deactivate(e.target);
    }
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
      for (const img of list) rememberSource(img);
      observeList(list);
      syncImageState(list);
    },
    addDeferred(imgNodes) {
      const list = Array.isArray(imgNodes) ? imgNodes : [imgNodes];
      for (const img of list) {
        rememberSource(img);
        img.dataset.spVirtualiserWarm = '1';
        img.dataset.spVirtualiserSeen = '0';
        if (hasRealSource(img)) {
          active.add(img);
        }
      }
      observeList(list);
    },
    sync() {
      syncImageState(items);
    },
    destroy() {
      io.disconnect();
      ex.disconnect();
      active.clear();
    },
  };
}
