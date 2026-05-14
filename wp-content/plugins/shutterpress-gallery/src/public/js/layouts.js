import { spInitVirtualiser } from "./virtualiser";
import { generateDynamicGalleryStyles } from "./dynamic-styles";
import { handleLikedImages } from "./utils";
import { getLikedImages } from "./state";

// --- Gallery Initialisation ---
export function initializeGalleryLayouts($ = jQuery, isEditor = false) {
  const isVirtualiserEnabled = (win) =>
    !(
      win &&
      win.shutterpressData &&
      Object.prototype.hasOwnProperty.call(
        win.shutterpressData,
        "virtualiser_enabled"
      ) &&
      !win.shutterpressData.virtualiser_enabled
    );

  const isVirtualiserLowConflictModeEnabled = (win) =>
    !(
      win &&
      win.shutterpressData &&
      Object.prototype.hasOwnProperty.call(
        win.shutterpressData,
        "virtualiser_low_conflict_mode"
      ) &&
      !win.shutterpressData.virtualiser_low_conflict_mode
    );

  function spLogTiming(win, galleryId, stage, extra = {}) {
    if (
      !win.shutterpressData ||
      !win.shutterpressData.debug ||
      typeof win.performance === "undefined"
    ) {
      return;
    }
    const time = Math.round(win.performance.now());
    console.log(`[SP TIMING] ${galleryId} ${stage} @ ${time}ms`, extra);
  }

  // Ensure the gallery has a measurable width before initializing Isotope
  function waitForRenderable($el, cb) {
    const el = $el && $el[0];
    if (!el) return cb();
    const hasSize = () => el.clientWidth > 0 && el.offsetParent !== null;
    if (hasSize()) return cb();

    let done = false;
    const finish = () => {
      if (done) return;
      done = true;
      try {
        ro.disconnect();
      } catch (e) {}
      cb();
    };

    let ro;
    if (typeof ResizeObserver !== "undefined") {
      ro = new ResizeObserver(() => {
        if (hasSize()) finish();
      });
      ro.observe(el);
    }

    // Fallback so we do not stall if ResizeObserver is unavailable
    setTimeout(() => {
      if (hasSize()) finish();
    }, 1200);
  }

  function watchForVisibilityReturn($el) {
    const el = $el && $el[0];
    if (!el) return;
    const hasRenderableSize = () =>
      el.clientWidth > 0 && el.offsetParent !== null;
    let lastVisible = hasRenderableSize();

    const triggerLayout = () => {
      if (
        typeof shutterpressData !== "undefined" &&
        shutterpressData &&
        shutterpressData.debug
      ) {
        console.log("[SP] Gallery became visible again, forcing layout");
      }
      $el.isotope("layout");
    };

    if (typeof ResizeObserver !== "undefined") {
      const ro = new ResizeObserver(() => {
        const nowVisible = hasRenderableSize();
        if (nowVisible && !lastVisible) {
          triggerLayout();
        }
        lastVisible = nowVisible;
      });
      ro.observe(el);
      $el.data("spVisibilityObserver", ro);
      return;
    }

    // Fallback: poll visibility periodically
    const interval = setInterval(() => {
      if (!document.body.contains(el)) {
        clearInterval(interval);
        return;
      }
      const nowVisible = hasRenderableSize();
      if (nowVisible && !lastVisible) {
        triggerLayout();
      }
      lastVisible = nowVisible;
    }, 400);
    $el.data("spVisibilityInterval", interval);
  }

  function setupInfiniteScroll($gallery, itemSelector, galleryId) {
    const win = $gallery[0]?.ownerDocument?.defaultView || window;

    if ($gallery.data("is") && $(".sp-gallery").length <= 1) {
      // Build a path() function for Infinite Scroll that hits our REST endpoint
      const buildAttrs = () => {
        const columns = $gallery.data("columns");
        const breakpoints = $gallery.data("breakpoints");

        return {
          layout: $gallery.data("layout"),
          gap: $gallery.data("gap"),
          columns_desktop: columns?.desktop ?? 0,
          columns_tablet: columns?.tablet ?? 0,
          columns_mobile: columns?.mobile ?? 0,
          breakpoint_tablet: breakpoints.tablet,
          breakpoint_mobile: breakpoints.mobile,
          use_lightbox: $gallery.data("lightbox"),
          gallery_width: $gallery.get(0).clientWidth,
          viewport_width: win.innerWidth,
        };
      };

      $(".sp-gallery-page-load-container").css("display", "block");

      const baseRest = shutterpressData.rest_url + "shutterpress/v1/gallery/";
      const numericId = String(galleryId).replace("sp-gallery-", "");
      if (shutterpressData.debug) {
        console.log("Base-Rest:", baseRest);
        console.log("[SP] Attributes", buildAttrs());
      }

      // Guard: only initialize Infinite Scroll once per gallery
      if ($gallery.data("spInfScrollInit")) {
        if (win.shutterpressData && shutterpressData.debug) {
          console.log(
            "[SP] Infinite Scroll already initialized for",
            galleryId
          );
        }
        return;
      }
      $gallery.data("spInfScrollInit", true);

      const totalPages = parseInt($gallery.data("pages"), 10) || 1;
      if (totalPages <= 1) {
        $gallery.data("spNoMorePages", true);
        if (win.shutterpressData && shutterpressData.debug) {
          console.log("[SP] Single page gallery, Infinite Scroll disabled");
        }
        return; // do not initialize infiniteScroll
      }

      let nextPage = 2; // start after the initially rendered page
      $gallery.data("spInfLoading", false);

      const setInfiniteScrollLoading = (isLoading) => {
        $gallery.data("spInfLoading", isLoading);
        $gallery.infiniteScroll("option", { loadOnScroll: !isLoading });
      };

      // Remove any previous namespaced handlers before binding new ones
      $gallery.off(".infiniteScroll.shutterpress");

      let headers = { "Content-Type": "application/json" };

      if (typeof shutterpressData !== "undefined" && shutterpressData.nonce) {
        headers["X-WP-Nonce"] = shutterpressData.nonce;
      }

      $gallery.infiniteScroll({
        path: function () {
          return `${baseRest}`;
        },
        fetchOptions: function () {
          const attrs = buildAttrs();
          return {
            method: "POST",
            mode: "cors",
            cache: "no-cache",
            credentials: "same-origin",
            headers: headers,
            body: JSON.stringify({
              gallery_id: numericId,
              page: nextPage, // ← use the tracked page
              attributes: attrs,
            }),
          };
        },
        responseBody: "json",
        append: false,
        domParseResponse: false,
        status: ".sp-gallery-page-load-status",
        history: false,
        hideNav: ".sp-gallery-pagination",
        debug: !!(win.shutterpressData && shutterpressData.debug),
      });

      $gallery.on(
        "scrollThreshold.infiniteScroll.shutterpress",
        function () {
          if ($gallery.data("spNoMorePages")) {
            return false;
          }

          if ($gallery.data("spInfLoading")) {
            if (win.shutterpressData && shutterpressData.debug) {
              console.log(
                "[SP] Suppressing scrollThreshold while batch is still settling for",
                galleryId
              );
            }
            return false;
          }

          setInfiniteScrollLoading(true);
          spLogTiming(win, galleryId, "scroll_threshold_lock", { nextPage });
        }
      );

      $gallery.on(
        "request.infiniteScroll.shutterpress",
        function (event, path) {
          if ($gallery.data("spNoMorePages")) {
            return false;
          }

          if (!$gallery.data("spInfLoading")) {
            setInfiniteScrollLoading(true);
          }

          spLogTiming(win, galleryId, "request_started", { nextPage, path });
        }
      );

      $gallery.on(
        "error.infiniteScroll.shutterpress",
        function (event, error, path) {
          spLogTiming(win, galleryId, "request_error", {
            nextPage,
            path,
            message: error && error.message ? error.message : "",
          });

          if (!$gallery.data("spNoMorePages")) {
            setInfiniteScrollLoading(false);
          }
        }
      );

      $gallery.on(
        "load.infiniteScroll.shutterpress",
        function (event, response) {
          spLogTiming(win, galleryId, "response_received", {
            nextPage:
              response && typeof response.nextPage !== "undefined"
                ? response.nextPage
                : null,
            htmlLength: response && response.html ? response.html.length : 0,
          });
          if (shutterpressData.debug) console.log("Response:", response);

          // Determine whether there is another page after this response
          let noMoreAfterThis = false;
          if (
            response &&
            typeof response.nextPage !== "undefined" &&
            response.nextPage !== false
          ) {
            nextPage = response.nextPage;
            $gallery.data("spNoMorePages", false);
          } else {
            noMoreAfterThis = true; // this response is the last batch
          }

          const html = response && response.html ? response.html : "";
          if (!html || !html.trim()) {
            // No HTML returned, mark as last page if applicable and stop loading
            if (noMoreAfterThis) {
              setInfiniteScrollLoading(true);
              $gallery.data("spNoMorePages", true);
              const infEmpty = $gallery.data("infiniteScroll");
              if (infEmpty && typeof infEmpty.lastPageReached === "function") {
                infEmpty.lastPageReached();
              }
            } else {
              setInfiniteScrollLoading(false);
            }
            return;
          }

          let $items = $(html);
          // Prefer only the expected gallery items; fall back to descendants if needed
          const $filtered = $items.filter(itemSelector);
          if ($filtered.length) {
            $items = $filtered;
          } else {
            const $found = $items.find(itemSelector);
            if ($found.length) $items = $found;
          }

          $items.find("img").attr({ decoding: "async", fetchpriority: "low" });

          // prepare items to fade-in after they are positioned
          $items.css({ opacity: 0 });

          spLogTiming(win, galleryId, "append_items_start", {
            itemCount: $items.length,
          });
          $gallery.infiniteScroll("appendItems", $items.get());
          spLogTiming(win, galleryId, "append_items_done", {
            itemCount: $items.length,
          });

          if (
            isVirtualiserEnabled(win) &&
            win.spVirtualisers &&
            win.spVirtualisers[galleryId]
          ) {
            spLogTiming(win, galleryId, "virtualiser_add_start", {
              imageCount: $items.find("img").length,
            });
            win.spVirtualisers[galleryId].addDeferred(
              $items.find("img").toArray()
            );
            spLogTiming(win, galleryId, "virtualiser_add_done", {
              imageCount: $items.find("img").length,
            });
          }

          spLogTiming(win, galleryId, "images_loaded_wait_start", {
            imageCount: $items.find("img").length,
          });
          $items.imagesLoaded(function () {
            let finalized = false;
            const finalizeAppend = () => {
              if (finalized) {
                return;
              }

              finalized = true;
              if (!noMoreAfterThis) {
                setInfiniteScrollLoading(false);
              }
            };

            spLogTiming(win, galleryId, "images_loaded_done", {
              imageCount: $items.find("img").length,
            });
            // Helper to clear only width/height inline styles
            const clearWH = ($els) => {
              $els.each(function () {
                this.style.removeProperty("width");
                this.style.removeProperty("height");
              });
            };

            // Determine the exact elements to clear (only newly added items)
            const $itemsToClear = $items.filter(itemSelector).length
              ? $items.filter(itemSelector)
              : $items;

            // Ensure we only clear once per append
            let cleared = false;
            const finishClear = () => {
              if (cleared) return;
              cleared = true;
              if ($gallery.data("layout") === "masonry") {
                clearWH($itemsToClear);
              }
              $gallery.off(".spClearWHAppend");
            };

            // Bind one-time, namespaced handlers BEFORE triggering layout
            $gallery.off(".spClearWHAppend");
            $gallery.one("arrangeComplete.spClearWHAppend", function () {
              finishClear();
            });
            // Fallback: if arrangeComplete doesn't fire, try layoutComplete
            $gallery.one("layoutComplete.spClearWHAppend", function () {
              finishClear();
            });
            // Safety fallback: time-based clear in case neither event fires (e.g., zero-duration animations)
            setTimeout(finishClear, 1200);

            // Trigger Isotope layout for the new items

            spLogTiming(win, galleryId, "isotope_appended_start", {
              itemCount: $items.length,
            });
            $gallery.isotope("appended", $items);
            spLogTiming(win, galleryId, "isotope_appended_done", {
              itemCount: $items.length,
            });

            // force immediate layout so items get measured and positioned
            spLogTiming(win, galleryId, "isotope_layout_start");
            $gallery.isotope("layout");
            spLogTiming(win, galleryId, "isotope_layout_done");

            // ensure visible items become visible even if events are missed
            $gallery.off("layoutComplete.spEnsureVisible");
            $gallery.one("layoutComplete.spEnsureVisible", function () {
              spLogTiming(win, galleryId, "layout_complete_visible");
              finalizeAppend();
              $items
                .filter(":not(.isotope-hidden)")
                .css({ visibility: "visible" });
            });
            // last-resort safety
            setTimeout(function () {
              finalizeAppend();
              $items.filter(":not(.isotope-hidden)").css({
                visibility: "visible",
                opacity: $gallery.data("filterActive") ? "" : 1,
              });
            }, 600);

            $gallery.off("arrangeComplete.spFinalizeAppend");
            $gallery.one("arrangeComplete.spFinalizeAppend", function () {
              finalizeAppend();
            });
            $gallery.one("layoutComplete.spFinalizeAppend", function () {
              finalizeAppend();
            });
            setTimeout(finalizeAppend, 1200);

            // prepare base transition on the items
            $items.css({
              willChange: "transform, opacity, width, height",
              opacity: 0,
            });

            if ($gallery.data("filterActive")) {
              // suppress initial transform animation so items do not jump from 0,0
              $items.css({
                transitionProperty: "opacity, width, height",
                transitionDuration: "300ms",
                transitionTimingFunction: "ease",
                opacity: 0,
              });

              $gallery.off("arrangeComplete.spFirstAppend");
              $gallery.one("arrangeComplete.spFirstAppend", function () {
                // fade in only items that remain visible after filtering
                $items.filter(":not(.isotope-hidden)").css({ opacity: 1 });

                // re enable transform animations for later filter or layout changes
                requestAnimationFrame(function () {
                  $items.css({
                    transitionProperty: "transform, opacity, width, height",
                    transitionDuration: "300ms",
                    transitionTimingFunction: "ease",
                  });
                });

                $gallery.off("arrangeComplete.spFirstAppend");
              });
            } else {
              if ($gallery.data("layout") == "justified") {
                $items.css({
                  transitionProperty: "opacity, width, height",
                  transitionDuration: "300ms",
                  transitionTimingFunction: "ease",
                  opacity: 0,
                });
                requestAnimationFrame(function () {
                  $items.css({
                    transitionProperty: "transform, opacity, width, height",
                    transitionDuration: "300ms",
                    transitionTimingFunction: "ease",
                  });
                });
              } else {
                $items.css({
                  transitionProperty: "transform, opacity, width, height",
                  transitionDuration: "300ms",
                  transitionTimingFunction: "ease",
                  opacity: 0,
                });
              }
            }
            // Apply liked state *after* items are in the DOM
            handleLikedImages(getLikedImages());

            if (shutterpressData.debug) {
              console.log(
                "[SP] Infinite Scroll: appended items and refreshed layout (post-clear)"
              );
            }
          });

          if (win.spGalleries[galleryId]) {
            const lb = win.spGalleries[galleryId];
            const wantSel = $gallery.data("filterActive")
              ? ".sp-gallery-item-liked .sp-gallery-item-lightbox"
              : ".sp-gallery-item .sp-gallery-item-lightbox";
            if (lb.settings && lb.settings.selector !== wantSel) {
              lb.settings.selector = wantSel;
            }
            lb.refresh();
          }

          // Ensure the active filter remains enforced after appending items
          if ($gallery.data("filterActive")) {
            $gallery.isotope({ filter: ".sp-gallery-item-liked" });
          }

          // After successfully appending the current response, if it was the last batch
          if (noMoreAfterThis) {
            setInfiniteScrollLoading(true);
            $gallery.data("spNoMorePages", true);
            const inf = $gallery.data("infiniteScroll");
            if (inf && typeof inf.lastPageReached === "function") {
              inf.lastPageReached();
            }
          }
        }
      );
    }
  }

  $(".sp-gallery").each(function () {
    if (shutterpressData.debug) {
      console.log("Initializing layouts for gallery:", $(this).attr("id"));
    }
    const $gallery = $(this);
    const win = $gallery[0]?.ownerDocument?.defaultView || window;
    // Prevent double-init of layouts on the same gallery element
    if ($gallery.data("spLayoutsInit")) {
      if (win.shutterpressData && shutterpressData.debug) {
        console.log(
          "[SP] Layouts already initialized for",
          $gallery.attr("id")
        );
      }
      return; // already initialized
    }
    $gallery.data("spLayoutsInit", true);
    const layoutType = $gallery.data("layout");
    const galleryId = $gallery.attr("id");
    if (!isEditor) {
      if (isVirtualiserEnabled(win)) {
        if (!win.spVirtualisers) win.spVirtualisers = {};
        if (!win.spVirtualisers[galleryId]) {
          win.spVirtualisers[galleryId] = spInitVirtualiser($gallery, {
            maxActive: 120,
            enterMargin: "1200px",
            exitMargin: "2000px",
            lowConflictMode: isVirtualiserLowConflictModeEnabled(win),
          });
        }
      }
    }
    const columnGap = $gallery.data("gap");
    const columns = $gallery.data("columns") || {
      desktop: 3,
      tablet: 2,
      mobile: 1,
    };
    const breakpoints = $gallery.data("breakpoints");
    const galleryWidth = $gallery.get(0).clientWidth;

    let activeColumns = columns.desktop;
    const windowWidth = win.innerWidth;
    if (windowWidth <= breakpoints.mobile) {
      activeColumns = columns.mobile;
    } else if (windowWidth <= breakpoints.tablet) {
      activeColumns = columns.tablet;
    }

    const columnWidth =
      (galleryWidth - (activeColumns - 1) * columnGap) / activeColumns;

    if (shutterpressData.debug) {
      console.log("windowWidth", windowWidth);
      console.log("galleryWidth", galleryWidth);
      console.log("columnWidth", columnWidth);
      console.log("activeColumns", activeColumns);
      console.log("columns", columns.desktop);
      console.log("columnsTablet", columns.tablet);
      console.log("columnsMobile", columns.mobile);
      console.log("breakpointTablet", breakpoints.tablet);
      console.log("breakpointMobile", breakpoints.mobile);
    }

    const layoutConfigs = {
      masonry: {
        itemSelector: ".sp-gallery-masonry-item",
        options: {
          itemSelector: ".sp-gallery-masonry-item",
          layoutMode: "masonry",
          percentPosition: true,
          resize: false,
          transitionDuration: "0.4s",
          masonry: {
            gutter: columnGap,
            columnWidth: ".sp-gallery-masonry-item",
          },
          hiddenStyle: { opacity: 0, transform: "scale(0.001)" },
          visibleStyle: { opacity: 1, transform: "scale(1)" },
        },
      },
      grid: {
        itemSelector: ".sp-gallery-grid-item",
        options: {
          itemSelector: ".sp-gallery-grid-item",
          layoutMode: "spGridLayout",
          transitionDuration: "0.4s",
          hiddenStyle: { opacity: 0, transform: "scale(0.001)" },
          visibleStyle: { opacity: 1, transform: "scale(1)" },
        },
      },
      justified: {
        itemSelector: ".sp-gallery-justified-item",
        options: {
          itemSelector: ".sp-gallery-justified-item",
          layoutMode: "spJustifiedLayout",
          resize: false,
          transitionDuration: "0.4s",
          hiddenStyle: { opacity: 0, transform: "scale(0.001)" },
          visibleStyle: { opacity: 1, transform: "scale(1)" },
        },
      },
    };

    const config = layoutConfigs[layoutType];
    if (!config) return;

    // Use imagesLoaded when available; otherwise, fall back to a manual image-load watcher
    const initAfterImages = function () {
      waitForRenderable($gallery, function () {
        $gallery.isotope(config.options);

        // Apply liked states
        if (!isEditor) {
          handleLikedImages(getLikedImages());
        }

        // Preserve liked filter if active
        const numericId = String(galleryId).replace("sp-gallery-", "");
        const likedButtonSel = "#sp-gallery-filter-liked-photos-" + numericId;
        const likedFilterIsActive =
          $gallery.data("filterActive") ||
          $(likedButtonSel).hasClass("sp-gallery-filter-active");
        if (likedFilterIsActive) {
          $gallery.data("filterActive", true);
          $gallery.isotope({ filter: ".sp-gallery-item-liked" });
        }

        $gallery.isotope("layout");
        if (
          isVirtualiserEnabled(win) &&
          win.spVirtualisers &&
          win.spVirtualisers[galleryId] &&
          typeof win.spVirtualisers[galleryId].sync === "function"
        ) {
          win.spVirtualisers[galleryId].sync();
          $gallery.one("layoutComplete.spVirtualiserSync", function () {
            win.spVirtualisers[galleryId].sync();
          });
        }
        watchForVisibilityReturn($gallery);

        // Masonry, clear server inline width and height after first layout
        if (layoutType === "masonry") {
          (function () {
            const clearInitWH = () => {
              $gallery.find(config.itemSelector).each(function () {
                this.style.removeProperty("width");
                this.style.removeProperty("height");
              });
              $gallery.off(".spClearWHInit");
            };
            $gallery.off(".spClearWHInit");
            $gallery.one("arrangeComplete.spClearWHInit", clearInitWH);
            $gallery.one("layoutComplete.spClearWHInit", clearInitWH);
            setTimeout(clearInitWH, 1200);
          })();
        }

        $gallery.on("layoutComplete", function () {
          if (win.shutterpressData && shutterpressData.debug)
            console.log("Layout Complete:");
        });

        // Now safe to wire Infinite Scroll, width is known

        if (isEditor === false) {
          setupInfiniteScroll($gallery, config.itemSelector, galleryId);
        }
      });

      let resizeTimer;
      $(win).on("resize", function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
          $("#shutterpress-gallery-inline-style").remove();
          generateDynamicGalleryStyles();
          $gallery.isotope();
        }, 300);
      });
    };
    if (
      typeof $.fn.imagesLoaded === "function" &&
      typeof $gallery.imagesLoaded === "function"
    ) {
      $gallery.imagesLoaded(initAfterImages);
    } else {
      const imgs = $gallery.find("img").get();
      if (!imgs.length) {
        initAfterImages();
      } else {
        let remaining = imgs.length;
        const check = () => {
          if (--remaining <= 0) initAfterImages();
        };
        imgs.forEach((img) => {
          if (img.complete) {
            check();
          } else {
            $(img).one("load error", check);
          }
        });
        // Safety timeout in case some events never fire
        setTimeout(initAfterImages, 1200);
      }
    }

    document
      .getElementById(galleryId)
      .addEventListener("lgAfterClose", function () {
        $gallery.isotope();
        if (shutterpressData.debug) {
          console.log("Gallery visible:", $gallery.is(":visible"));
          console.log("Gallery height:", $gallery.height());
          console.log("Forced layout after LightGallery closed.");
        }
      });
  });
}
