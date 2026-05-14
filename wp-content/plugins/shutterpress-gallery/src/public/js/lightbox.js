// --- Lightbox Initialisation ---
export function initializeLightGalleries() {
  jQuery('.sp-gallery[id^="sp-gallery-"]').each(function () {
    const $gallery = jQuery(this);
    const galleryId = $gallery.attr("id");
    const mode = $gallery.data("lg-mode");
    const DownloadIcon = $gallery.attr("data-download-icon");
    const likes = String($gallery.data("likes")) === "1";
    const download = String($gallery.data("downloads")) === "1";

    // flags for load management when navigating in lightbox
    if (typeof $gallery.data("spLoadingNext") === "undefined") {
      $gallery.data("spLoadingNext", false);
    }
    // how many extra pages we will auto-fetch when no liked items are found
    if (typeof $gallery.data("spPrefetchBudget") === "undefined") {
      $gallery.data("spPrefetchBudget", 3);
    }

    if (shutterpressData.debug) console.log("Download:", download);

    const basePlugins = [lgThumbnail, lgZoom, lgAutoplay, lgFullscreen];
    if (likes && typeof lgLikes !== "undefined") {
      basePlugins.push(lgLikes);
    }
    const plugins = window.spGalleryFilters?.applyPlugins
      ? window.spGalleryFilters.applyPlugins(basePlugins, { galleryId })
      : basePlugins;

    const baseReplaceIcons = [
      {
        selector: ".lg-close",
        html: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="lucide lucide-x-icon lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>',
      },
      {
        selector: ".lg-zoom-in",
        html: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="lucide lucide-zoom-in-icon lucide-zoom-in"><circle cx="11" cy="11" r="8"/><line x1="21" x2="16.65" y1="21" y2="16.65"/><line x1="11" x2="11" y1="8" y2="14"/><line x1="8" x2="14" y1="11" y2="11"/></svg><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-zoom-out-icon lucide-zoom-out"><circle cx="11" cy="11" r="8"/><line x1="21" x2="16.65" y1="21" y2="16.65"/><line x1="8" x2="14" y1="11" y2="11"/></svg>',
      },
      {
        selector: "#lg-actual-size",
        html: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-zoom-out-icon lucide-zoom-out"><circle cx="11" cy="11" r="8"/><line x1="21" x2="16.65" y1="21" y2="16.65"/><line x1="8" x2="14" y1="11" y2="11"/></svg>',
      },
      {
        selector: ".lg-autoplay-button",
        html: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-play-icon lucide-play"><path d="M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z"/></svg><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pause-icon lucide-pause"><rect x="14" y="3" width="5" height="18" rx="1"/><rect x="5" y="3" width="5" height="18" rx="1"/></svg>',
      },
      {
        selector: ".lg-download",
        html: DownloadIcon,
      },
      {
        selector: ".lg-fullscreen",
        html: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-expand-icon lucide-expand"><path d="m15 15 6 6"/><path d="m15 9 6-6"/><path d="M21 16v5h-5"/><path d="M21 8V3h-5"/><path d="M3 16v5h5"/><path d="m3 21 6-6"/><path d="M3 8V3h5"/><path d="M9 9 3 3"/></svg><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shrink-icon lucide-shrink"><path d="m15 15 6 6m-6-6v4.8m0-4.8h4.8"/><path d="M9 19.8V15m0 0H4.2M9 15l-6 6"/><path d="M15 4.2V9m0 0h4.8M15 9l6-6"/><path d="M9 4.2V9m0 0H4.2M9 9 3 3"/></svg>',
      },
    ];
    const replaceIcons = window.spGalleryFilters?.applyReplaceIcons
      ? window.spGalleryFilters.applyReplaceIcons(baseReplaceIcons, {
          galleryId,
          $gallery,
          DownloadIcon,
        })
      : baseReplaceIcons;

    // Initialize with a broad selector so there are always items, we will narrow on open
    window.spGalleries[galleryId] = lightGallery(
      document.getElementById(galleryId),
      {
        plugins,
        selector: ".sp-gallery-item .sp-gallery-item-lightbox",
        download: download,
        autoplay: true,
        addClass: "lg-" + galleryId,
        getCaptionFromTitleOrAlt: false,
        zoomFromOrigin: true,
        thumbnail: true,
        mode: mode,
        allowMediaOverlap: false,
        toggleThumb: false,
        exThumbImage: "data-external-thumb-image",
        replaceIcons,
        /* customToolbar: [
          {
            id: "lg-cart",
            html: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart-icon lucide-shopping-cart"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>',
            ariaLabel: "Close",
          },
        ], */
      },
    );

    const lightbox = window.spGalleries[galleryId];
    const galleryEl = document.getElementById(galleryId);

    const getLightboxContainer = () =>
      jQuery(`.lg-container.lg-${galleryId}`).first();

    const getCurrentLightboxIndex = () => {
      const $container = getLightboxContainer();
      const $currentImg = $container.find(".lg-item.lg-current .lg-object[data-index]").first();
      const currentIndex = parseInt($currentImg.attr("data-index") || "", 10);
      if (Number.isFinite(currentIndex)) {
        return currentIndex;
      }
      return typeof lightbox?.index === "number" ? lightbox.index : 0;
    };

    const getCurrentLightboxSourceItem = () => {
      if (!lightbox || !lightbox.lgOpened || !Array.isArray(lightbox.items)) return null;
      const index = getCurrentLightboxIndex();
      return lightbox.items[index] || null;
    };

    const getCurrentLightboxImageId = () => {
      const sourceItem = getCurrentLightboxSourceItem();
      if (!sourceItem || typeof sourceItem.getAttribute !== "function") return 0;
      const value = parseInt(sourceItem.getAttribute("data-image-id") || "", 10);
      return Number.isFinite(value) ? value : 0;
    };

    const syncLightboxLikeState = () => {
      if (!likes) return;
      if (!lightbox || !lightbox.lgOpened) return;
      const imageId = getCurrentLightboxImageId();
      const $lightboxLike = getLightboxContainer().find(".lg-likes").first();
      if (!$lightboxLike.length) return;

      const sourceItem = getCurrentLightboxSourceItem();
      const likedAttr =
        sourceItem && typeof sourceItem.getAttribute === "function"
          ? sourceItem.getAttribute("data-liked")
          : null;
      const galleryLiked =
        imageId > 0 &&
        $gallery
          .find(`.sp-gallery-like-icon[data-image-id="${imageId}"]`)
          .first()
          .hasClass("sp-gallery-liked-image");
      const hasExplicitState = likedAttr === "1" || likedAttr === "0" || !!galleryLiked;

      if (likedAttr === "1" || galleryLiked) {
        $lightboxLike.addClass("sp-gallery-liked-image");
      } else if (hasExplicitState) {
        $lightboxLike.removeClass("sp-gallery-liked-image");
      }

      if (imageId > 0) {
        $lightboxLike.attr("data-image-id", imageId);
      }
    };

    // Helper to switch selector to match current filter state
    const setSelectorForFilter = () => {
      const want = $gallery.data("filterActive")
        ? ".sp-gallery-item-liked .sp-gallery-item-lightbox"
        : ".sp-gallery-item .sp-gallery-item-lightbox";
      if (
        lightbox &&
        lightbox.settings &&
        lightbox.settings.selector !== want
      ) {
        lightbox.settings.selector = want;
        try {
          lightbox.refresh();
        } catch (e) {}
      }
    };

    // When opening the lightbox, ensure the selector matches current filter
    galleryEl.addEventListener("lgBeforeOpen", function () {
        // if we are in liked-only mode, ensure liked images are not lazy
        if ($gallery.data("filterActive")) {
          $gallery
            .find('.sp-gallery-item-liked img[loading="lazy"]')
            .removeAttr("loading");
        }
        setSelectorForFilter();
        syncLightboxLikeState();
        if (window.spVirtualisers && window.spVirtualisers[galleryId])
          window.spVirtualisers[galleryId].freeze();
      });

    galleryEl.addEventListener("lgAfterClose", function () {
      if (window.spVirtualisers && window.spVirtualisers[galleryId])
        window.spVirtualisers[galleryId].unfreeze();
    });

    galleryEl.addEventListener("lgAfterOpen", syncLightboxLikeState);
    galleryEl.addEventListener("lgUpdateSlides", syncLightboxLikeState);
    galleryEl.addEventListener("lgAfterSlide", function (event) {
      syncLightboxLikeState();
      if (shutterpressData.debug) {
        console.log("lgAfterSlide event triggered");
        console.log("Event index:", event.detail.index);
          console.log("Gallery items length:", lightbox.galleryItems.length);
        }

        const isFilterActive = $gallery.data("filterActive");

        // If no more pages, bail early
        if ($gallery.data("spNoMorePages")) return;

        // Use LightGallery's current slide count (already respects the selector)
        const totalSlides = lightbox.galleryItems.length;
        const nearEnd = event.detail.index >= totalSlides - 4;

        if (nearEnd && !$gallery.data("spLoadingNext")) {
          // prevent concurrent loads
          $gallery.data("spLoadingNext", true);

          const prefetchNext = () => {
            if (
              !$gallery.data("infiniteScroll") ||
              !$gallery.data("infiniteScroll").canLoad
            ) {
              $gallery.data("spLoadingNext", false);
              return;
            }
            $gallery
              .infiniteScroll("loadNextPage")
              .then(function (loaded) {
                let { response } = loaded;

                // stop loading if endpoint signals no more pages
                if ($gallery.data("spNoMorePages")) {
                  $gallery.data("spLoadingNext", false);
                  return;
                }

                // layouts.js owns append/layout/refresh for infinite-scroll batches.
                // Here we only inspect the response to decide whether to prefetch again.
                let $newItems = jQuery();
                if (response && response.html) {
                  const $parsed = jQuery(response.html);
                  const $direct = $parsed.filter(".sp-gallery-item");
                  $newItems = $direct.length
                    ? $direct
                    : $parsed.find(".sp-gallery-item");
                }

                // If we're filtering liked-only and this batch added no liked items,
                // optionally prefetch the next page up to a small budget.
                const likedAdded =
                  $newItems.filter(".sp-gallery-item-liked").length ||
                  $newItems.find(".sp-gallery-item-liked").length;

                if (isFilterActive && !likedAdded) {
                  let budget = Number($gallery.data("spPrefetchBudget")) || 0;
                  if (budget > 0) {
                    $gallery.data("spPrefetchBudget", budget - 1);
                    // chain another load
                    return prefetchNext();
                  }
                } else {
                  // reset budget once we successfully add liked items or filter is off
                  $gallery.data("spPrefetchBudget", 3);
                }

                // allow new loads after this batch
                $gallery.data("spLoadingNext", false);

                if (shutterpressData.debug) {
                  console.log("Incremental loadNextPage handled by layouts.js");
                  console.log(response && response.path);
                }
              })
              .catch(function () {
                // on error, release the lock to avoid deadlock
                $gallery.data("spLoadingNext", false);
              });
          };

          prefetchNext();
      }
    });

    jQuery(document)
      .off(`click.sp-lightbox-like-${galleryId}`)
      .on(
        `click.sp-lightbox-like-${galleryId}`,
        `.lg-container.lg-${galleryId} .lg-likes`,
        function (e) {
        if (!likes || !lightbox || !lightbox.lgOpened) return;

        const imageId = getCurrentLightboxImageId();
        if (!imageId) return;

        const $imageLike = $gallery.find(`.sp-gallery-like-icon[data-image-id="${imageId}"]`).first();
        if (!$imageLike.length) return;

        e.preventDefault();
        $imageLike.trigger("click");

        // Optimistic update happens immediately in likes.js; sync now and once async handlers settle.
        syncLightboxLikeState();
        setTimeout(syncLightboxLikeState, 60);
        setTimeout(syncLightboxLikeState, 180);
        setTimeout(syncLightboxLikeState, 320);
      });
  });
}
